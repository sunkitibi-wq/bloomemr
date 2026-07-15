<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', 'in:attending,resident,clinical_staff,receptionist,laboratory_officer,pharmacist,billing_admin,medical_records_officer,radiologist,insurance_officer,public_health_officer,hospital_administrator,guardian'],
            'practice_name' => ['nullable', 'string', 'max:255'],
            'child_name' => ['nullable', 'string', 'max:255'],
            'terms' => ['required_if:role,guardian', 'accepted'],
        ])->validate();

        $role = $input['role'];

        // Resolve an existing practice from the middleware context or create a new one for clinicians
        $practice = app()->bound('current_practice') ? app('current_practice') : null;

        if (! $practice) {
            if ($role !== 'guardian' && ! empty($input['practice_name'])) {
                // New clinician registering — create their practice
                $practiceName = $input['practice_name'];
                $slug = Str::slug($practiceName);

                // Ensure unique slug
                $originalSlug = $slug;
                $counter = 1;
                while (Practice::where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$counter++;
                }

                $practice = Practice::create([
                    'name' => $practiceName,
                    'slug' => $slug,
                    'is_active' => true,
                    'settings' => [
                        'timezone' => 'America/New_York',
                        'locale' => 'en',
                    ],
                ]);

                // Bind so BelongsToPractice::creating() can pick it up
                app()->instance('current_practice', $practice);
            } else {
                // Fallback for guardian self-registration (joins via existing portal)
                $practice = Practice::first();
            }
        }

        $practiceId = $practice?->id;

        $user = new User([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $role,
        ]);
        $user->practice_id = $practiceId;
        $user->save();
        
        $user->assignRole($role);

        // Grant super_admin to first clinician of a freshly created practice
        if ($role !== 'guardian' && $practice && $practice->wasRecentlyCreated) {
            $user->role = 'super_admin';
            $user->save();
            $user->assignRole('super_admin');
        }

        if ($role === 'guardian' && ! empty($input['child_name'])) {
            // Explode child's name into first and last name fallback
            $parts = explode(' ', trim($input['child_name']), 2);
            $firstName = $parts[0];
            $lastName = $parts[1] ?? 'Patient';

            Patient::create([
                'practice_id' => $practiceId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'mrn' => 'MRN-'.rand(100000, 999999),
                'date_of_birth' => now()->subYears(8),
                'portal_user_id' => $user->id,
            ]);
        }

        return $user;
    }
}
