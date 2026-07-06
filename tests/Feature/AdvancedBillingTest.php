<?php

use App\Livewire\Billing\BillingManager;
use App\Livewire\Patients\PatientDetail;
use App\Livewire\Portal\PortalBilling;
use App\Models\ClaimSubmission;
use App\Models\EligibilityCheck;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Practice;
use App\Models\User;
use App\Services\ClearinghouseService;
use App\Services\EligibilityService;
use App\Services\StripePaymentService;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom clinic', 'slug' => 'bloom-clinic']);
    
    // Attending
    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
        'name' => 'Dr. Bob Attending',
    ]);
    $this->provider->assignRole('attending');

    // Guardian
    $this->guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
        'name' => 'Jane Guardian',
    ]);
    $this->guardian->assignRole('guardian');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'portal_user_id' => $this->guardian->id,
        'first_name' => 'Timmy',
        'last_name' => 'Doe',
        'primary_insurance' => 'Aetna POLICY-12345',
    ]);
});

test('insurance eligibility service simulates coverage check correctly', function () {
    $service = app(EligibilityService::class);
    
    // Case 1: Has insurance
    $check = $service->checkEligibility($this->patient);
    expect($check->status)->toBe('eligible');
    expect((float) $check->copay_amount)->toBe(30.00);
    expect((float) $check->deductible_amount)->toBe(250.00);
    expect($check->payer_name)->toBe('Aetna');

    // Case 2: No insurance
    $noInsPatient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_insurance' => null,
    ]);
    $check2 = $service->checkEligibility($noInsPatient);
    expect($check2->status)->toBe('error');
    expect($check2->payer_name)->toBe('No Insurance On File');

    // Case 3: Inactive insurance
    $inactivePatient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'primary_insurance' => 'BlueShield Inactive Policy',
    ]);
    $check3 = $service->checkEligibility($inactivePatient);
    expect($check3->status)->toBe('ineligible');
});

test('clinician can trigger insurance eligibility check from patient chart dashboard', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientDetail::class, ['patient' => $this->patient])
        ->call('checkInsuranceEligibility')
        ->assertOk();

    $this->assertDatabaseHas('eligibility_checks', [
        'patient_id' => $this->patient->id,
        'status' => 'eligible',
        'payer_name' => 'Aetna',
    ]);
});

test('clearinghouse service generates valid ANSI ASC X12 837P format and submits claim', function () {
    $invoice = Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'total_amount' => 150.00,
        'status' => 'pending',
        'due_date' => now()->addDays(30),
        'cpt_codes' => [
            ['code' => '90834', 'description' => 'Psychotherapy 45m', 'fee' => 150.00]
        ],
    ]);

    $service = app(ClearinghouseService::class);
    $edi = $service->generateEdi837($invoice);

    expect($edi)->toContain('ISA*00*');
    expect($edi)->toContain('ST*837*');
    expect($edi)->toContain('CLM*INV-');
    expect($edi)->toContain('SV1*HC:90834*150.00');

    $submission = $service->submitClaim($invoice);
    expect($submission->status)->toBe('accepted');
    expect($submission->edi_request)->toBe($edi);
    expect($submission->edi_response)->toContain('AK5*A');

    expect($invoice->fresh()->insurance_claim_status)->toBe('submitted');
    expect($invoice->fresh()->claim_reference)->toBe('CLM-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT));
});

test('clinician can submit claim and view EDI segments inside billing dashboard', function () {
    $invoice = Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'total_amount' => 110.00,
        'status' => 'pending',
        'due_date' => now()->addDays(30),
        'cpt_codes' => [
            ['code' => '90834', 'description' => 'Psychotherapy 45m', 'fee' => 110.00]
        ],
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(BillingManager::class)
        ->call('submitClaim', $invoice->id)
        ->call('viewEdiPayload', $invoice->id)
        ->assertSet('showEdiModal', true)
        ->assertSee('ISA*00*')
        ->assertSee('AK5*A');

    expect(ClaimSubmission::where('invoice_id', $invoice->id)->count())->toBe(1);
});

test('patient portal payment calls StripePaymentService and creates database logs', function () {
    $invoice = Invoice::create([
        'patient_id' => $this->patient->id,
        'practice_id' => $this->practice->id,
        'total_amount' => 200.00,
        'status' => 'pending',
        'due_date' => now()->addDays(30),
        'cpt_codes' => [
            ['code' => '90791', 'description' => 'Psychiatric Eval', 'fee' => 200.00]
        ],
    ]);

    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalBilling::class)
        ->call('openPaymentModal', $invoice)
        ->set('cardNumber', '4242424242424242')
        ->set('cardExpiry', '12/28')
        ->set('cardCvc', '123')
        ->call('processPayment')
        ->assertHasNoErrors();

    expect($invoice->fresh()->status)->toBe('paid');
    
    $payment = Payment::where('invoice_id', $invoice->id)->first();
    expect($payment)->not->toBeNull();
    expect((float) $payment->amount)->toBe(200.00);
    expect($payment->payment_method)->toBe('credit_card (ending in 4242)');
    expect($payment->transaction_reference)->toStartWith('ch_');
});
