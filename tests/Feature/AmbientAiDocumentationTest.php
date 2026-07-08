<?php

use App\Livewire\Encounters\EncounterNote;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom clinic', 'slug' => 'bloom-clinic']);
    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
        'name' => 'Dr. Alice Ambient',
    ]);
    $this->provider->assignRole('attending');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $this->encounter = Encounter::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'provider_id' => $this->provider->id,
        'type' => 'SOAP',
        'status' => 'pending',
        'encounter_date' => now(),
    ]);
});

test('clinician can record ambient clinical conversation transcript', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(EncounterNote::class, ['encounter' => $this->encounter])
        ->call('startAmbientRecording')
        ->assertSet('isRecording', true)
        ->call('stopAmbientRecording')
        ->assertSet('isRecording', false)
        ->assertNotSet('ambientTranscript', '');
});

test('ambient assistant can compile conversational raw dialog into structured draft', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(EncounterNote::class, ['encounter' => $this->encounter])
        ->set('ambientTranscript', "Patient says they are sleeping poorly.\nWeight is normal, BP 120/80.\nGeneralized anxiety disorder diagnosis.\nStart Buspar 10mg.")
        ->call('generateAiDraft')
        ->assertHasNoErrors()
        ->assertSet('aiDraft.subjective', 'Patient says they are sleeping poorly.')
        ->assertSet('aiDraft.objective', 'Weight is normal, BP 120/80.')
        ->assertSet('aiDraft.assessment', 'Generalized anxiety disorder diagnosis.')
        ->assertSet('aiDraft.plan', 'Start Buspar 10mg.');
});

test('clinician can apply structured AI draft to active note sections', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(EncounterNote::class, ['encounter' => $this->encounter])
        ->set('aiDraft', [
            'subjective' => 'Insomnia details.',
            'objective' => 'MSE normal.',
            'assessment' => 'Adjustment disorder.',
            'plan' => 'CBT therapy.',
        ])
        ->call('applyAiDraft')
        ->assertHasNoErrors()
        ->assertSet('sections.subjective', 'Insomnia details.')
        ->assertSet('sections.objective', 'MSE normal.')
        ->assertSet('sections.assessment', 'Adjustment disorder.')
        ->assertSet('sections.plan', 'CBT therapy.');
});

test('clinician can scan active section text for AI smart phrase suggestions and apply them', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(EncounterNote::class, ['encounter' => $this->encounter])
        ->set('sections.subjective', 'Patient complains of severe sleep issues and panic')
        ->call('suggestAiPhrases', 'subjective')
        ->assertHasNoErrors()
        ->assertNotSet('aiSuggestions.subjective', [])
        ->call('applySuggestedPhrase', 'subjective', 'Reports severe sleep onset insomnia, sleeping average of 4 hours per night.')
        ->assertHasNoErrors()
        ->assertSet('sections.subjective', 'Patient complains of severe sleep issues and panic Reports severe sleep onset insomnia, sleeping average of 4 hours per night.');
});
