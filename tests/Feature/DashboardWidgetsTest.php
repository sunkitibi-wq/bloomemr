<?php

use App\Livewire\Dashboard;
use App\Livewire\Patients\PatientDetail;
use App\Models\Assessment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

test('attending can see notes awaiting co-signature in queue', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $attending = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $resident = User::factory()->create(['practice_id' => $practice->id, 'role' => 'resident']);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    $encounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $resident->id,
        'practice_id' => $practice->id,
        'status' => 'co_sign_pending',
    ]);

    // Attending user sees it in queue
    $component = Livewire::actingAs($attending)->test(Dashboard::class);
    expect($component->get('coSignQueue'))->toHaveCount(1);

    // Resident user does not see it in queue
    $component2 = Livewire::actingAs($resident)->test(Dashboard::class);
    expect($component2->get('coSignQueue'))->toHaveCount(0);
});

test('recent patient charts are tracked and shown on dashboard', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    // Visit patient detail page (actingAs user)
    Livewire::actingAs($user)
        ->test(PatientDetail::class, ['patient' => $patient]);

    // Check dashboard has this patient in recent charts
    $component = Livewire::actingAs($user)->test(Dashboard::class);
    expect($component->get('recentCharts'))->toHaveCount(1);
    expect($component->get('recentCharts')->first()->id)->toBe($patient->id);
});

test('unsigned notes older than 24 hours trigger alerts', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    // Create an encounter created 2 days ago as draft
    $encounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
        'status' => 'draft',
        'created_at' => now()->subDays(2),
    ]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);
    expect($component->get('clinicalAlerts'))->toHaveCount(1);
    expect($component->get('clinicalAlerts')->first()['message'])->toContain('Unsigned note');
});

test('high risk patients with no visits in 60 days trigger alerts', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->attending()->create(['practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    // PHQ-9 of 18 (Severe)
    Assessment::create([
        'patient_id' => $patient->id,
        'practice_id' => $practice->id,
        'instrument' => 'phq-9',
        'rater_type' => 'patient',
        'responses' => [],
        'score' => 18,
        'severity_band' => 'Severe depression',
    ]);

    // Create last encounter 70 days ago
    $encounter = Encounter::factory()->create([
        'patient_id' => $patient->id,
        'provider_id' => $user->id,
        'practice_id' => $practice->id,
        'status' => 'signed',
        'encounter_date' => now()->subDays(70),
    ]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);
    expect($component->get('clinicalAlerts'))->toHaveCount(1);
    expect($component->get('clinicalAlerts')->first()['message'])->toContain('High Risk');
});
