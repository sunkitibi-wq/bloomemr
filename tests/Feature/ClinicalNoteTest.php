<?php

use App\Livewire\Encounters\EncounterNote;
use App\Models\ClinicalNote;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

test('clinical note screen can be rendered', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);
    $encounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
    ]);

    $response = $this->actingAs($user)->get(route('encounters.note', $encounter));

    $response->assertOk();
});

test('clinical note draft can be saved', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);
    $encounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
        'type' => 'SOAP',
    ]);

    Livewire::actingAs($user)
        ->test(EncounterNote::class, ['encounter' => $encounter])
        ->set('sections', [
            'subjective' => 'Patient reports feeling depressed.',
            'objective' => 'Affect is flat.',
            'assessment' => 'Major depressive disorder.',
            'plan' => 'Increase SSRI dose.',
        ])
        ->call('saveDraft')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('clinical_notes', [
        'encounter_id' => $encounter->id,
        'template_type' => 'SOAP',
    ]);

    $note = ClinicalNote::where('encounter_id', $encounter->id)->first();
    expect($note->sections['subjective'])->toBe('Patient reports feeling depressed.');
    expect($note->body)->toContain('## SUBJECTIVE');
});

test('encounter note carries forward previous encounter sections', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    // Create prior encounter and sign it
    $priorEncounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
        'status' => 'signed',
    ]);
    ClinicalNote::create([
        'encounter_id' => $priorEncounter->id,
        'template_type' => 'SOAP',
        'sections' => [
            'subjective' => 'Old Subjective',
            'objective' => 'Old Objective',
            'assessment' => 'Old Assessment',
            'plan' => 'Old Plan',
        ],
        'body' => 'Old body text',
        'signed_by' => $user->id,
        'signed_at' => now(),
        'practice_id' => $practice->id,
    ]);

    // Create new encounter
    $newEncounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
        'type' => 'SOAP',
    ]);

    Livewire::actingAs($user)
        ->test(EncounterNote::class, ['encounter' => $newEncounter])
        ->call('checkPriorEncounter')
        ->assertSet('showCarryForwardModal', true)
        ->set('selectedPriorSections', [
            'subjective' => true,
            'objective' => false,
            'assessment' => true,
            'plan' => false,
        ])
        ->call('carryForward')
        ->assertSet('sections.subjective', 'Old Subjective')
        ->assertSet('sections.assessment', 'Old Assessment')
        ->assertSet('sections.objective', ''); // Objective was set to false, so not carried forward
});
