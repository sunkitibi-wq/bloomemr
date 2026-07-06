<?php

use App\Livewire\Patients\PatientLabs;
use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->patient = Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'MRN-10002',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1995-05-15',
        'preferred_language' => 'English',
    ]);
});

test('can order a lab panel', function () {
    $this->actingAs($this->user);

    Livewire::test(PatientLabs::class, ['patient' => $this->patient])
        ->set('panel', 'Thyroid Panel')
        ->call('orderLabs')
        ->assertHasNoErrors();

    expect(LabOrder::count())->toBe(1);

    $order = LabOrder::first();
    expect($order->panel)->toBe('Thyroid Panel')
        ->and($order->status)->toBe('ordered');
});

test('can simulate Quest diagnostics HL7 result delivery', function () {
    $this->actingAs($this->user);

    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'Thyroid Panel',
        'status' => 'ordered',
    ]);

    Livewire::test(PatientLabs::class, ['patient' => $this->patient])
        ->call('simulateQuestResult', $order->id);

    expect(LabOrder::first()->status)->toBe('completed')
        ->and(LabResult::count())->toBe(1);

    $result = LabResult::first();
    expect($result->is_critical)->toBeFalse()
        ->and($result->result_data)->toHaveCount(2);

    expect($result->result_data[0]['name'])->toBe('TSH')
        ->and($result->result_data[0]['value'])->toBe('6.20')
        ->and($result->result_data[0]['flag'])->toBe('H');
});

test('critical HL7 value triggers critical flag', function () {
    $this->actingAs($this->user);

    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'CBC',
        'status' => 'ordered',
    ]);

    Livewire::test(PatientLabs::class, ['patient' => $this->patient])
        ->call('simulateQuestResult', $order->id);

    $result = LabResult::first();
    expect($result->is_critical)->toBeTrue()
        ->and($result->result_data[0]['name'])->toBe('WBC')
        ->and($result->result_data[0]['flag'])->toBe('LL');
});

test('can compute delta change against prior results', function () {
    $this->actingAs($this->user);

    // Create a prior result
    $order1 = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'Thyroid Panel',
        'status' => 'completed',
    ]);
    LabResult::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'lab_order_id' => $order1->id,
        'result_data' => [
            ['name' => 'TSH', 'value' => '4.80', 'range' => '0.40-4.00', 'flag' => 'H', 'unit' => 'uIU/mL'],
        ],
    ]);

    // Create a new order and simulate result
    $order2 = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'Thyroid Panel',
        'status' => 'ordered',
    ]);

    $component = Livewire::test(PatientLabs::class, ['patient' => $this->patient]);
    $component->call('simulateQuestResult', $order2->id);

    $latestResult = LabResult::orderBy('id', 'desc')->first();

    $prior = $component->instance()->getPriorValue('Thyroid Panel', 'TSH', $latestResult->id);

    expect($prior)->not->toBeNull()
        ->and($prior['value'])->toBe('4.80');
});
