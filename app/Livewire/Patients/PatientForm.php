<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Patient;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('New Patient')]
class PatientForm extends Component
{
    use WithFileUploads;

    public ?Patient $patient = null;

    public int $currentStep = 1;

    public $photo;

    public string $first_name = '';

    public string $last_name = '';

    public string $mrn = '';

    public string $date_of_birth = '';

    public string $gender_identity = '';

    public string $pronouns = '';

    public string $race_ethnicity = '';

    public string $preferred_language = 'en';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_phone = '';

    public string $emergency_contact_relationship = '';

    public string $primary_insurance = '';

    public string $secondary_insurance = '';

    public string $allergies = '';

    public bool $editing = false;

    public function mount(?Patient $patient = null): void
    {
        $this->patient = $patient;

        if ($patient) {
            $this->editing = true;
            $this->first_name = $patient->first_name;
            $this->last_name = $patient->last_name;
            $this->mrn = $patient->mrn;
            $this->date_of_birth = $patient->date_of_birth?->format('Y-m-d') ?? '';
            $this->gender_identity = $patient->gender_identity ?? '';
            $this->pronouns = $patient->pronouns ?? '';
            $this->race_ethnicity = $patient->race_ethnicity ?? '';
            $this->preferred_language = $patient->preferred_language;
            $this->phone = $patient->phone ?? '';
            $this->email = $patient->email ?? '';
            $this->address = $patient->address ?? '';
            $this->emergency_contact_name = $patient->emergency_contact_name ?? '';
            $this->emergency_contact_phone = $patient->emergency_contact_phone ?? '';
            $this->emergency_contact_relationship = $patient->emergency_contact_relationship ?? '';
            $this->primary_insurance = $patient->primary_insurance ?? '';
            $this->secondary_insurance = $patient->secondary_insurance ?? '';
            $this->allergies = $patient->allergies ?? '';
        }

        if (! $this->mrn) {
            $this->mrn = 'MRN-'.str_pad((string) (Patient::max('id') + 1), 5, '0', STR_PAD_LEFT);
        }
    }

    public function nextStep(): void
    {
        $rules = [];
        if ($this->currentStep === 1) {
            $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'mrn' => ['required', 'string', 'max:50'],
                'date_of_birth' => ['required', 'date'],
                'gender_identity' => ['nullable', 'string', 'max:50'],
                'pronouns' => ['nullable', 'string', 'max:50'],
                'race_ethnicity' => ['nullable', 'string', 'max:100'],
                'preferred_language' => ['required', 'string', 'max:10'],
                'photo' => ['nullable', 'image', 'max:1024'],
            ];
        } elseif ($this->currentStep === 2) {
            $rules = [
                'phone' => ['nullable', 'string', 'max:50'],
                'email' => ['nullable', 'email', 'max:255'],
                'address' => ['nullable', 'string', 'max:500'],
                'emergency_contact_name' => ['nullable', 'string', 'max:255'],
                'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
                'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            ];
        }

        if (! empty($rules)) {
            $this->validate($rules);
        }

        $this->currentStep++;
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mrn' => $this->mrn,
            'date_of_birth' => $this->date_of_birth,
            'gender_identity' => $this->gender_identity ?: null,
            'pronouns' => $this->pronouns ?: null,
            'race_ethnicity' => $this->race_ethnicity ?: null,
            'preferred_language' => $this->preferred_language,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'emergency_contact_name' => $this->emergency_contact_name ?: null,
            'emergency_contact_phone' => $this->emergency_contact_phone ?: null,
            'emergency_contact_relationship' => $this->emergency_contact_relationship ?: null,
            'primary_insurance' => $this->primary_insurance ?: null,
            'secondary_insurance' => $this->secondary_insurance ?: null,
            'allergies' => $this->allergies ?: null,
            'primary_provider_id' => Auth::id(),
        ];

        if ($this->photo) {
            $photoPath = $this->photo->store('patient-photos', 'public');
            $data['photo_path'] = $photoPath;

            if ($this->editing && $this->patient->photo_path) {
                Storage::disk('public')->delete($this->patient->photo_path);
            }
        }

        if ($this->editing) {
            $this->patient->update($data);

            app(LogAudit::class)(
                user: Auth::user(),
                action: 'update',
                entityType: 'patient',
                entityId: $this->patient->id,
                patientId: $this->patient->id,
            );

            Flux::toast(variant: 'success', text: 'Patient updated successfully.');
        } else {
            $patient = Patient::create($data);

            app(LogAudit::class)(
                user: Auth::user(),
                action: 'create',
                entityType: 'patient',
                entityId: $patient->id,
                patientId: $patient->id,
            );

            Flux::toast(variant: 'success', text: 'Patient created successfully.');

            $this->redirect(route('patients.show', $patient), navigate: true);
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mrn' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date'],
            'gender_identity' => ['nullable', 'string', 'max:50'],
            'pronouns' => ['nullable', 'string', 'max:50'],
            'race_ethnicity' => ['nullable', 'string', 'max:100'],
            'preferred_language' => ['required', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'primary_insurance' => ['nullable', 'string', 'max:255'],
            'secondary_insurance' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function render(): View
    {
        return view('livewire.patients.patient-form');
    }
}
