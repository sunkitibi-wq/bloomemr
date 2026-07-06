<?php

use App\Livewire\Assessments\ScoredAssessment;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

test('assessment screen can be rendered', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    $response = $this->actingAs($user)->get(route('patients.assessment.create', $patient));

    $response->assertOk();
});

test('phq9 assessment scores correctly', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    $component = Livewire::actingAs($user)
        ->test(ScoredAssessment::class, ['patient' => $patient])
        ->set('instrument', 'phq-9')
        ->set('responses', [
            1 => 3, // Nearly every day
            2 => 3,
            3 => 3,
            4 => 3,
            5 => 3,
            6 => 3,
            7 => 3,
            8 => 3,
            9 => 3,
        ]);

    expect($component->get('score'))->toBe(27);
    expect($component->get('severityBand'))->toBe('Severe depression');
});

test('gad7 assessment scores correctly', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    $component = Livewire::actingAs($user)
        ->test(ScoredAssessment::class, ['patient' => $patient])
        ->set('instrument', 'gad-7')
        ->set('responses', [
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1,
            7 => 1,
        ]);

    expect($component->get('score'))->toBe(7);
    expect($component->get('severityBand'))->toBe('Mild anxiety');
});

test('assessment can be saved', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    Livewire::actingAs($user)
        ->test(ScoredAssessment::class, ['patient' => $patient])
        ->set('instrument', 'phq-9')
        ->set('responses', [
            1 => 2,
            2 => 2,
            3 => 2,
            4 => 2,
            5 => 2,
            6 => 2,
            7 => 2,
            8 => 2,
            9 => 2,
        ])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('assessments', [
        'patient_id' => $patient->id,
        'instrument' => 'phq-9',
        'score' => 18,
        'severity_band' => 'Moderately severe depression',
    ]);
});
