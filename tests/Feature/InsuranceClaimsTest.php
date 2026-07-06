<?php

use App\Livewire\Billing\BillingManager;
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
        'mrn' => 'MRN-20002',
        'first_name' => 'Nina',
        'last_name' => 'Jones',
        'date_of_birth' => '1990-01-01',
        'gender_identity' => 'female',
        'preferred_language' => 'en',
        'practice_id' => $this->practice->id,
        'primary_provider_id' => $this->provider->id,
    ]);
});

test('billing manager can track insurance claim submission metadata', function () {
    $this->actingAs($this->provider);

    $invoice = Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90834', 'description' => 'Therapy', 'fee' => 110.00]],
        'total_amount' => 110.00,
        'status' => 'pending',
        'insurance_claim_status' => 'unsubmitted',
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    Livewire::test(BillingManager::class)
        ->call('updateClaimStatus', $invoice, 'submitted')
        ->assertHasNoErrors();

    $invoice->refresh();

    expect($invoice->insurance_claim_status)->toBe('submitted')
        ->and($invoice->claim_reference)->toContain('CLM-')
        ->and($invoice->claim_note)->toBeNull();
});

test('claim lifecycle records submission and acceptance timestamps', function () {
    $this->actingAs($this->provider);

    $invoice = Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90834', 'description' => 'Therapy', 'fee' => 110.00]],
        'total_amount' => 110.00,
        'status' => 'pending',
        'insurance_claim_status' => 'unsubmitted',
        'due_date' => now()->addDays(30)->toDateString(),
    ]);

    Livewire::test(BillingManager::class)
        ->call('updateClaimStatus', $invoice, 'submitted')
        ->call('updateClaimStatus', $invoice, 'accepted');

    $invoice->refresh();

    expect($invoice->insurance_claim_status)->toBe('accepted')
        ->and($invoice->submitted_at)->not->toBeNull()
        ->and($invoice->accepted_at)->not->toBeNull();
});
