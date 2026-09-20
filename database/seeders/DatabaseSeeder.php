<?php

namespace Database\Seeders;

use App\Models\Medication;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $hospital = Practice::create([
            'name' => 'Bloom Child Psychiatry',
            'type' => 'hospital',
            'slug' => 'bloom-child-psychiatry',
            'settings' => [
                'timezone' => 'America/New_York',
                'locale' => 'en',
            ],
        ]);

        $mainHospital = $hospital->facilities()->create([
            'name' => 'Main Hospital Campus',
            'phone' => '555-0100',
            'address' => '123 Health Way',
        ]);

        $mainHospital->departments()->createMany([
            ['name' => 'Pediatrics'],
            ['name' => 'Emergency Room'],
            ['name' => 'Radiology'],
        ]);

        $northClinic = $hospital->facilities()->create([
            'name' => 'Northside Clinic',
            'phone' => '555-0200',
            'address' => '456 North Blvd',
        ]);

        $northClinic->departments()->createMany([
            ['name' => 'Urgent Care'],
            ['name' => 'Family Medicine'],
        ]);

        $pharmacy = Practice::create([
            'name' => 'Bloom Pharmacy Center',
            'type' => 'pharmacy',
            'slug' => 'bloom-pharmacy-center',
            'settings' => [
                'timezone' => 'America/New_York',
                'locale' => 'en',
            ],
        ]);

        $hospitalAdmin = User::factory()->superAdmin()->create([
            'name' => 'Admin User',
            'email' => 'admin@bloomemr.test',
            'practice_id' => $hospital->id,
            'is_system_admin' => true,
        ]);

        User::factory()->attending()->create([
            'name' => 'Dr. Sarah Chen',
            'email' => 'sarah@bloomemr.test',
            'npi_number' => '1234567890',
            'practice_id' => $hospital->id,
        ]);

        User::factory()->attending()->create([
            'name' => 'Dr. James Wilson',
            'email' => 'james@bloomemr.test',
            'npi_number' => '1234567891',
            'practice_id' => $hospital->id,
        ]);

        User::factory()->create([
            'name' => 'Dr. Mike Rivera',
            'email' => 'mike@bloomemr.test',
            'role' => 'resident',
            'npi_number' => '1234567892',
            'practice_id' => $hospital->id,
        ]);

        User::factory()->clinicalStaff()->create([
            'name' => 'Lisa Park',
            'email' => 'lisa@bloomemr.test',
            'practice_id' => $hospital->id,
        ]);

        User::factory()->create([
            'name' => 'Karen Miller',
            'email' => 'karen@bloomemr.test',
            'role' => 'billing_admin',
            'practice_id' => $hospital->id,
        ]);

        $pharmacist = User::factory()->create([
            'name' => 'PharmD. Elena Ruiz',
            'email' => 'pharmacy@bloomemr.test',
            'role' => 'pharmacist',
            'practice_id' => $pharmacy->id,
        ]);
        $pharmacist->assignRole('pharmacist');
        $pharmacist->givePermissionTo(['view_prescriptions', 'update_prescriptions']);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'practice_id' => $hospital->id,
        ]);

        // Seed a patient guardian user who can log in to the Patient Portal
        $guardian = User::factory()->create([
            'name' => 'John Doe (Guardian)',
            'email' => 'guardian@bloomemr.test',
            'password' => bcrypt('password'),
            'role' => 'guardian',
            'practice_id' => $hospital->id,
        ]);
        // Spatie role assignment
        $guardian->assignRole('guardian');

        // Seed a corresponding patient record linked to the guardian
        $patient = Patient::create([
            'mrn' => 'MRN-99999',
            'first_name' => 'Joey',
            'last_name' => 'Doe',
            'date_of_birth' => '2018-05-15',
            'gender_identity' => 'male',
            'preferred_language' => 'en',
            'practice_id' => $hospital->id,
            'portal_user_id' => $guardian->id,
            'primary_provider_id' => User::where('email', 'sarah@bloomemr.test')->first()->id,
        ]);

        // Seed an active medication for the patient so the refills view has items
        Medication::create([
            'practice_id' => $hospital->id,
            'patient_id' => $patient->id,
            'name' => 'Ritalin',
            'dose' => '10mg',
            'frequency' => 'Once daily in the morning',
            'prescriber_id' => $patient->primary_provider_id,
            'status' => 'active',
        ]);

        $this->call(FormTemplateSeeder::class);
        $this->call(EducationArticleSeeder::class);
    }
}
