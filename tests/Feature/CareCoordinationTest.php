<?php

use App\Livewire\Patients\PatientCareCoordination;
use App\Livewire\Portal\PortalCareCoordination;
use App\Models\DirectMessage;
use App\Models\FormTemplate;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom clinic', 'slug' => 'bloom-clinic']);

    // Attending/Provider
    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
        'name' => 'Dr. Bob Attending',
    ]);
    $this->provider->assignRole('attending');

    // Guardian/Portal user
    $this->guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
        'name' => 'Jane Guardian',
    ]);
    $this->guardian->assignRole('guardian');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'portal_user_id' => $this->guardian->id,
        'first_name' => 'Timmy',
        'last_name' => 'Doe',
    ]);

    // ROI Consent Form Template
    $this->consentTemplate = FormTemplate::create([
        'title' => 'Release of Information (ROI) Consent',
        'description' => 'Authorization to release medical records to external entities.',
        'type' => 'consent',
        'version' => 1,
        'is_active' => true,
        'fields' => [
            ['name' => 'agree_to_share', 'label' => 'I agree to share my health info', 'type' => 'checkbox', 'required' => true],
        ],
    ]);

    // Signed consent form instance
    $this->signedConsent = PatientForm::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'template_id' => $this->consentTemplate->id,
        'status' => 'completed',
        'completed_at' => now(),
        'form_data' => ['agree_to_share' => true],
        'signature' => 'Jane Guardian',
        'signed_at' => now(),
    ]);
});

test('clinician can load patient care coordination dashboard and see signed consents', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientCareCoordination::class, ['patient' => $this->patient])
        ->assertOk()
        ->assertSee('Release of Information (ROI) Consent');
});

test('clinician can share clinical summary and generate FHIR R4 payload', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientCareCoordination::class, ['patient' => $this->patient])
        ->set('recipientName', 'External Therapist')
        ->set('recipientAddress', 'therapist@direct.health.org')
        ->set('consentFormId', $this->signedConsent->id)
        ->call('sendSummary')
        ->assertHasNoErrors();

    // Verify DirectMessage record exists in DB with FHIR payload
    $message = DirectMessage::first();
    expect($message)->not->toBeNull();
    expect($message->recipient_name)->toBe('External Therapist');
    expect($message->recipient_address)->toBe('therapist@direct.health.org');
    expect($message->payload)->toBeArray();
    expect($message->payload['resourceType'])->toBe('Composition');
    expect($message->payload['subject']['display'])->toBe('Timmy Doe');
    expect($message->payload['section'][0]['title'])->toBe('Active Medication List');
});

test('clinician cannot share clinical summary without signed consent form', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientCareCoordination::class, ['patient' => $this->patient])
        ->set('recipientName', 'External Therapist')
        ->set('recipientAddress', 'therapist@direct.health.org')
        ->set('consentFormId', null)
        ->call('sendSummary')
        ->assertHasErrors(['consentFormId']);

    expect(DirectMessage::count())->toBe(0);
});

test('patient portal user can view their clinical sharing logs', function () {
    // Create a historical shared record
    DirectMessage::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'patient_form_id' => $this->signedConsent->id,
        'sender_id' => $this->provider->id,
        'sender_address' => 'dr.bob.attending@direct.bloom.test',
        'recipient_address' => 'therapist@direct.health.org',
        'recipient_name' => 'External Therapist',
        'subject' => 'Clinical Summary (CCD) for Timmy Doe',
        'scope' => 'CCD',
        'payload' => ['resourceType' => 'Composition'],
        'status' => 'delivered',
    ]);

    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalCareCoordination::class)
        ->assertOk()
        ->assertSee('External Therapist')
        ->assertSee('therapist@direct.health.org')
        ->assertSee('Release of Information (ROI) Consent');
});
