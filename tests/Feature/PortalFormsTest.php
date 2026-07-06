<?php

use App\Models\FormTemplate;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Psychiatry', 'slug' => 'bloom-psychiatry']);

    $this->provider = User::factory()->attending()->create([
        'practice_id' => $this->practice->id,
    ]);

    $this->guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);

    $this->patient = Patient::create([
        'mrn' => 'MRN-10101',
        'first_name' => 'Timmy',
        'last_name' => 'Doe',
        'date_of_birth' => '2016-04-12',
        'gender_identity' => 'male',
        'preferred_language' => 'en',
        'practice_id' => $this->practice->id,
        'portal_user_id' => $this->guardian->id,
        'primary_provider_id' => $this->provider->id,
    ]);

    // Create a template
    $this->template = FormTemplate::create([
        'title' => 'Telehealth Consent',
        'description' => 'Telehealth informed consent.',
        'type' => 'consent',
        'version' => 1,
        'is_active' => true,
        'fields' => [
            [
                'name' => 'nys_resident',
                'label' => 'Located in NYS',
                'type' => 'checkbox',
                'required' => true,
            ],
            [
                'name' => 'guardian_name',
                'label' => 'Guardian Name',
                'type' => 'text',
                'required' => true,
            ],
        ],
    ]);
});

test('provider can assign a form to a patient', function () {
    $this->actingAs($this->provider);

    Livewire::test(\App\Livewire\Patients\PatientDetail::class, ['patient' => $this->patient])
        ->call('assignForm', $this->template->id)
        ->assertHasNoErrors();

    $assignedForm = PatientForm::where('patient_id', $this->patient->id)
        ->where('template_id', $this->template->id)
        ->first();

    expect($assignedForm)->not->toBeNull()
        ->and($assignedForm->status)->toBe('pending');
});

test('guardian can view pending form on the portal', function () {
    // Assign form first
    $assignedForm = PatientForm::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'template_id' => $this->template->id,
        'status' => 'pending',
    ]);

    $this->actingAs($this->guardian, 'portal');

    Livewire::test(\App\Livewire\Portal\PortalForms::class)
        ->assertSee('Telehealth Consent')
        ->assertSee('Complete & Sign');
});

test('guardian can submit and e-sign the assigned form with validation', function () {
    $assignedForm = PatientForm::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'template_id' => $this->template->id,
        'status' => 'pending',
    ]);

    $this->actingAs($this->guardian, 'portal');

    Livewire::test(\App\Livewire\Portal\PortalForms::class)
        ->call('openForm', $assignedForm->id)
        // Submit empty to trigger validation
        ->call('submitForm')
        ->assertHasErrors(['responses.nys_resident', 'responses.guardian_name', 'signature'])
        // Fill out properly
        ->set('responses.nys_resident', true)
        ->set('responses.guardian_name', 'Jane Doe')
        ->set('signature', 'Jane Doe')
        ->call('submitForm')
        ->assertHasNoErrors();

    $assignedForm->refresh();
    expect($assignedForm->status)->toBe('completed')
        ->and($assignedForm->signature_name)->toBe('Jane Doe')
        ->and($assignedForm->signed_at)->not->toBeNull()
        ->and($assignedForm->form_data)->toBe([
            'nys_resident' => true,
            'guardian_name' => 'Jane Doe',
        ]);
});

test('guardian can submit a yes/no form field', function () {
    $yesNoTemplate = FormTemplate::create([
        'title' => 'Yes/No Consent',
        'description' => 'A consent form with a yes/no question.',
        'type' => 'consent',
        'version' => 1,
        'is_active' => true,
        'fields' => [
            [
                'name' => 'consent_to_treatment',
                'label' => 'Do you consent to treatment?',
                'type' => 'yes_no',
                'required' => true,
            ],
        ],
    ]);

    $assignedForm = PatientForm::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'template_id' => $yesNoTemplate->id,
        'status' => 'pending',
    ]);

    $this->actingAs($this->guardian, 'portal');

    Livewire::test(\App\Livewire\Portal\PortalForms::class)
        ->call('openForm', $assignedForm->id)
        ->set('responses.consent_to_treatment', true)
        ->set('signature', 'Jane Doe')
        ->call('submitForm')
        ->assertHasNoErrors();

    $assignedForm->refresh();

    expect($assignedForm->status)->toBe('completed')
        ->and($assignedForm->form_data['consent_to_treatment'])->toBeTrue();
});

test('provider can create a new form template from the patient detail builder', function () {
    $this->actingAs($this->provider);

    Livewire::test(\App\Livewire\Patients\PatientDetail::class, ['patient' => $this->patient])
        ->set('templateTitle', 'New Intake Form')
        ->set('templateDescription', 'A custom intake form for onboarding.')
        ->set('templateType', 'intake')
        ->set('templateFields', [[
            'name' => 'reason_for_visit',
            'label' => 'Reason for visit',
            'type' => 'text',
            'required' => true,
        ]])
        ->call('createTemplate')
        ->assertHasNoErrors();

    $template = FormTemplate::where('title', 'New Intake Form')->first();

    expect($template)->not->toBeNull()
        ->and($template->type)->toBe('intake')
        ->and($template->fields[0]['name'])->toBe('reason_for_visit');
});

test('provider can view the signed form details', function () {
    // Submit form first
    $assignedForm = PatientForm::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'template_id' => $this->template->id,
        'status' => 'completed',
        'form_data' => [
            'nys_resident' => true,
            'guardian_name' => 'Jane Doe',
        ],
        'signature_name' => 'Jane Doe',
        'signature_ip' => '127.0.0.1',
        'signed_at' => now(),
    ]);

    $this->actingAs($this->provider);

    Livewire::test(\App\Livewire\Patients\PatientDetail::class, ['patient' => $this->patient])
        ->call('viewCompletedForm', $assignedForm->id)
        ->assertSet('showFormModal', true)
        ->assertSet('selectedFormTitle', 'Telehealth Consent')
        ->assertSet('selectedFormSignature', 'Jane Doe');
});
