<?php

use App\Livewire\Patients\PatientRefills;
use App\Livewire\Portal\PortalRefills;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\Prescription;
use App\Models\RefillRequest;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->provider->assignRole('attending');

    $this->guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $this->guardian->assignRole('guardian');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'Joey',
        'last_name' => 'Doe',
        'portal_user_id' => $this->guardian->id,
        'primary_provider_id' => $this->provider->id,
    ]);

    $this->medication = Medication::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'name' => 'Ritalin',
        'dose' => '10mg',
        'frequency' => 'Once daily',
        'prescriber_id' => $this->provider->id,
        'status' => 'active',
    ]);
});

test('guardian can request a refill', function () {
    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalRefills::class)
        ->set('medicationId', $this->medication->id)
        ->set('notes', 'Need refill at Walgreens')
        ->call('submitRequest')
        ->assertHasNoErrors();

    expect(RefillRequest::count())->toBe(1);
    $req = RefillRequest::first();
    expect($req->medication_id)->toBe($this->medication->id);
    expect($req->status)->toBe('pending');
    expect($req->notes)->toBe('Need refill at Walgreens');
});

test('provider can approve a refill request', function () {
    $request = RefillRequest::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'medication_id' => $this->medication->id,
        'requested_by' => $this->guardian->id,
        'status' => 'pending',
        'notes' => 'Refill please',
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientRefills::class, ['patient' => $this->patient])
        ->call('approve', $request->id)
        ->assertHasNoErrors();

    $request->refresh();
    expect($request->status)->toBe('approved');
    expect(Prescription::count())->toBe(1);
    expect(Prescription::first()->medication_id)->toBe($this->medication->id);
});

test('provider can deny a refill request', function () {
    $request = RefillRequest::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'medication_id' => $this->medication->id,
        'requested_by' => $this->guardian->id,
        'status' => 'pending',
        'notes' => 'Refill please',
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientRefills::class, ['patient' => $this->patient])
        ->call('deny', $request->id)
        ->assertHasNoErrors();

    $request->refresh();
    expect($request->status)->toBe('denied');
    expect(Prescription::count())->toBe(0);
});
