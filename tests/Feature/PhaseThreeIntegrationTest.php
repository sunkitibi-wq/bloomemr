<?php

use App\Jobs\PollLabResults;
use App\Livewire\Encounters\EncounterNote;
use App\Livewire\Patients\PatientMedications;
use App\Models\Encounter;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use App\Services\SurescriptsService;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create([
        'role' => 'attending',
        'practice_id' => $this->practice->id,
    ]);
    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->user->id,
    ]);
});

test('surescripts service returns correct formulary checks', function () {
    $service = new SurescriptsService;

    $lexapro = $service->checkFormulary($this->patient, 'Lexapro');
    expect($lexapro['status'])->toBe('Preferred Generic')
        ->and($lexapro['pa_required'])->toBeFalse();

    $vyvanse = $service->checkFormulary($this->patient, 'Vyvanse');
    expect($vyvanse['status'])->toBe('Non-Preferred Brand')
        ->and($vyvanse['pa_required'])->toBeTrue();
});

test('poll lab results job parses hl7 and appends results to draft note', function () {
    $encounter = Encounter::factory()->create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->patient->practice_id,
        'provider_id' => $this->user->id,
        'status' => 'draft',
        'type' => 'SOAP',
    ]);

    $order = LabOrder::create([
        'practice_id' => $this->patient->practice_id,
        'patient_id' => $this->patient->id,
        'encounter_id' => $encounter->id,
        'panel' => 'Thyroid Panel',
        'status' => 'ordered',
        'ordered_by' => $this->user->id,
    ]);

    // Dispatch the job
    PollLabResults::dispatchSync();

    // Check that order is now completed
    $order->refresh();
    expect($order->status)->toBe('completed');

    // Check that the clinical note has the appended lab summary in the objective section
    $note = $encounter->clinicalNotes()->first();
    expect($note)->not->toBeNull();
    expect($note->sections['objective'])->toContain('[Auto-populated Thyroid Panel');
    expect($note->sections['objective'])->toContain('TSH: 6.20 (H) uIU/mL');
});

test('patient medications livewire component handles formulary and teaching sheets', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientMedications::class, ['patient' => $this->patient])
        ->set('name', 'Lexapro')
        ->assertSet('formularyDetails.status', 'Preferred Generic')
        ->call('assignTeachingSheet', 'Lexapro Patient Guide', 'Lexapro');

    $this->assertDatabaseHas('medication_teaching_logs', [
        'patient_id' => $this->patient->id,
        'material_title' => 'Lexapro Patient Guide',
        'medication_name' => 'Lexapro',
        'given_by' => $this->user->id,
    ]);
});

test('encounter note livewire component handles cds flowchart titration', function () {
    $this->actingAs($this->user);

    $encounter = Encounter::factory()->create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->patient->practice_id,
        'provider_id' => $this->user->id,
        'status' => 'draft',
        'type' => 'SOAP',
    ]);

    Livewire::test(EncounterNote::class, ['encounter' => $encounter])
        ->call('selectCdsAlgorithm', 'adhd')
        ->assertSet('cdsAlgorithm', 'adhd')
        ->assertSet('cdsStep', 'start')
        ->call('selectCdsStep', 'stimulant', 'Stimulant Recommendation Plan')
        ->assertSet('cdsTitrationPlan', 'Stimulant Recommendation Plan')
        ->call('applyTitrationToNote')
        ->assertSet('sections.plan', 'Stimulant Recommendation Plan');
});
