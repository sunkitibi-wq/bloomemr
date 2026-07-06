<?php

use App\Livewire\Dashboard;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Psychiatry', 'slug' => 'bloom-psychiatry']);

    $this->provider = User::factory()->attending()->create([
        'practice_id' => $this->practice->id,
    ]);

    $this->patient = Patient::create([
        'mrn' => 'MRN-30001',
        'first_name' => 'Alex',
        'last_name' => 'Rivera',
        'date_of_birth' => '1985-05-05',
        'gender_identity' => 'male',
        'preferred_language' => 'en',
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->provider->id,
    ]);
});

test('dashboard reports monthly revenue and encounter volume for the practice', function () {
    $this->actingAs($this->provider);

    Encounter::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'provider_id' => $this->provider->id,
        'encounter_date' => now(),
        'type' => 'follow_up',
        'status' => 'signed',
    ]);

    Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90834', 'description' => 'Therapy', 'fee' => 110.00]],
        'total_amount' => 110.00,
        'status' => 'paid',
        'insurance_claim_status' => 'accepted',
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90837', 'description' => 'Therapy', 'fee' => 140.00]],
        'total_amount' => 140.00,
        'status' => 'pending',
        'insurance_claim_status' => 'unsubmitted',
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    Livewire::test(Dashboard::class)
        ->assertSee('Monthly Revenue')
        ->assertSee('Open Invoices')
        ->assertSee('This Month Encounters');

    $component = Livewire::test(Dashboard::class);

    expect($component->instance()->monthlyRevenue)->toEqual(110.00)
        ->and($component->instance()->unpaidInvoices)->toBe(1)
        ->and($component->instance()->thisMonthEncounters)->toBe(1);
});
