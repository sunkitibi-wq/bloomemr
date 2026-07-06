<?php

use App\Livewire\Billing\BillingManager;
use App\Livewire\Portal\PortalBilling;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');
});

test('clinician can load billing manager and see invoices', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    $invoice = Invoice::create([
        'patient_id' => $patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90791', 'description' => 'Psychiatric Diagnostic Evaluation', 'fee' => 150.00]],
        'total_amount' => 150.00,
        'status' => 'pending',
        'due_date' => today()->addDays(30)->format('Y-m-d'),
    ]);

    Livewire::test(BillingManager::class)
        ->assertOk()
        ->assertSee($patient->full_name)
        ->assertSee('$150.00');
});

test('clinician can open create form', function () {
    $this->actingAs($this->user);

    Livewire::test(BillingManager::class)
        ->assertSet('isFormOpen', false)
        ->call('openCreateForm')
        ->assertSet('isFormOpen', true);
});

test('clinician can create an invoice with CPT codes', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    Livewire::test(BillingManager::class)
        ->set('patientId', $patient->id)
        ->set('dueDate', today()->addDays(30)->format('Y-m-d'))
        ->set('status', 'draft')
        ->set('cptCodes', [['code' => '90791', 'description' => 'Psychiatric Diagnostic Evaluation', 'fee' => 150.00]])
        ->set('totalAmount', 150.00)
        ->call('save');

    $this->assertDatabaseHas('invoices', [
        'patient_id' => $patient->id,
        'total_amount' => 150.00,
        'status' => 'draft',
    ]);
});

test('clinician can mark invoice as paid', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
    ]);

    $invoice = Invoice::create([
        'patient_id' => $patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90791', 'description' => 'Psychiatric Diagnostic Evaluation', 'fee' => 150.00]],
        'total_amount' => 150.00,
        'status' => 'pending',
        'due_date' => today()->addDays(30)->format('Y-m-d'),
    ]);

    Livewire::test(BillingManager::class)
        ->call('updateStatus', $invoice->id, 'paid');

    $this->assertEquals('paid', $invoice->fresh()->status);
});

test('portal user can view and pay their invoice', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'portal_user_id' => $guardian->id,
    ]);

    $invoice = Invoice::create([
        'patient_id' => $patient->id,
        'practice_id' => $this->practice->id,
        'cpt_codes' => [['code' => '90834', 'description' => 'Psychotherapy, 45 minutes', 'fee' => 110.00]],
        'total_amount' => 110.00,
        'status' => 'pending',
        'due_date' => today()->addDays(30)->format('Y-m-d'),
    ]);

    Livewire::actingAs($guardian, 'portal')
        ->test(PortalBilling::class)
        ->assertSee('$110.00')
        ->call('mockPayment', $invoice->id);

    $this->assertEquals('paid', $invoice->fresh()->status);
});
