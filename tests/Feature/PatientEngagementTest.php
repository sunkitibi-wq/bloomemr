<?php

use App\Livewire\Patients\PatientEngagements;
use App\Models\Patient;
use App\Models\PatientEngagement;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Psychiatry', 'slug' => 'bloom-psychiatry']);

    $this->provider = User::factory()->attending()->create([
        'practice_id' => $this->practice->id,
    ]);

    $this->patient = Patient::create([
        'mrn' => 'MRN-40001',
        'first_name' => 'Jordan',
        'last_name' => 'Lee',
        'date_of_birth' => '1990-01-01',
        'gender_identity' => 'nonbinary',
        'preferred_language' => 'en',
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->provider->id,
    ]);
});

test('staff can create a patient engagement task from the patient engagement component', function () {
    $this->actingAs($this->provider);

    Livewire::test(PatientEngagements::class, ['patient' => $this->patient])
        ->set('title', 'Call patient about follow-up')
        ->set('description', 'Check in after recent appointment')
        ->set('type', 'call')
        ->set('dueDate', now()->addDay()->toDateString())
        ->call('createEngagement')
        ->assertHasNoErrors();

    $task = PatientEngagement::where('patient_id', $this->patient->id)->latest()->first();

    expect($task)->not->toBeNull()
        ->and($task->title)->toBe('Call patient about follow-up')
        ->and($task->status)->toBe('planned');
});
