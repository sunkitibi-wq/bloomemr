<?php

use App\Livewire\Patients\PatientMedications;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\Practice;
use App\Models\Prescription;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->patient = Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'MRN-10001',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1995-05-15',
        'preferred_language' => 'English',
        'allergies' => 'Aspirin, Penicillin',
    ]);
    $this->pharmacy = Pharmacy::create([
        'practice_id' => $this->practice->id,
        'name' => 'Northside Pharmacy',
        'ncpdp' => '1234567',
        'phone' => '555-0100',
        'address' => '100 Main St',
    ]);
});

test('can view medications tab and prescribe normal drug', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Lexapro')
        ->set('dose', '10mg')
        ->set('frequency', 'Once daily')
        ->call('prescribe')
        ->assertHasNoErrors()
        ->assertSet('showEpcsModal', false);

    expect(Medication::count())->toBe(1)
        ->and(Prescription::count())->toBe(1);

    $med = Medication::first();
    expect($med->name)->toBe('Lexapro')
        ->and($med->status)->toBe('active');
});

test('can associate a selected pharmacy with a prescription', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Lexapro')
        ->set('dose', '10mg')
        ->set('frequency', 'Once daily')
        ->set('pharmacyId', $this->pharmacy->id)
        ->call('prescribe');

    $prescription = Prescription::first();

    expect($prescription)->not->toBeNull()
        ->and($prescription->pharmacy_id)->toBe($this->pharmacy->id);
});

test('can update prescription fulfillment status', function () {
    $this->actingAs($this->user);

    $prescription = Prescription::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'medication_id' => Medication::create([
            'practice_id' => $this->practice->id,
            'patient_id' => $this->patient->id,
            'name' => 'Lexapro',
            'dose' => '10mg',
            'frequency' => 'daily',
            'status' => 'active',
        ])->id,
        'pharmacy_id' => $this->pharmacy->id,
        'status' => 'sent',
    ]);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->call('updatePrescriptionStatus', $prescription->id, 'filled');

    $prescription->refresh();

    expect($prescription->fulfillment_status)->toBe('filled');
});

test('controlled substance requires EPCS 2FA modal verification', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Adderall')
        ->set('dose', '15mg')
        ->set('frequency', 'Every morning')
        ->set('is_controlled', true)
        ->call('prescribe')
        ->assertSet('showEpcsModal', true);

    expect(Medication::count())->toBe(0)
        ->and(Prescription::count())->toBe(0);
});

test('can sign controlled substance with correct 2FA token', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Adderall')
        ->set('dose', '15mg')
        ->set('frequency', 'Every morning')
        ->set('is_controlled', true)
        ->call('prescribe')
        ->set('two_factor_code', '123456')
        ->call('signPrescription')
        ->assertSet('showEpcsModal', false);

    expect(Medication::count())->toBe(1)
        ->and(Prescription::count())->toBe(1);

    $prescription = Prescription::first();
    expect($prescription->is_controlled)->toBeTrue()
        ->and($prescription->epcs_id)->not->toBeNull();
});

test('triggers allergy warning when drug contains allergy list term', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Aspirin 325mg')
        ->assertSee('Allergy Alert: Patient is allergic to');
});

test('triggers drug-drug interaction warning on SSRI plus MAOI combination', function () {
    $this->actingAs($this->user);

    // Give the patient active SSRI first
    Medication::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'name' => 'Lexapro',
        'dose' => '10mg',
        'frequency' => 'daily',
        'status' => 'active',
    ]);

    // Try prescribing MAOI
    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Phenelzine')
        ->assertSee('Contraindicated: Combination of SSRI');
});

test('can reconcile external medication history', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->call('reconcileMed', 0); // Reconciles Abilify

    expect(Medication::count())->toBe(1);

    $med = Medication::first();
    expect($med->name)->toBe('Abilify')
        ->and($med->dose)->toBe('5mg');
});

test('can discontinue an active medication', function () {
    $this->actingAs($this->user);

    $med = Medication::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'name' => 'Prozac',
        'dose' => '20mg',
        'frequency' => 'daily',
        'status' => 'active',
    ]);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->call('discontinue', $med->id);

    expect($med->fresh()->status)->toBe('discontinued');
});
