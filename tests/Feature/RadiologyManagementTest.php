<?php

use App\Livewire\Radiology\OrderForm;
use App\Livewire\Radiology\RadiologyManager;
use App\Livewire\Radiology\ReportUpload;
use App\Models\Patient;
use App\Models\RadiologyOrder;
use App\Models\User;
use Livewire\Livewire;

it('renders the radiology manager page for providers', function () {
    $practice = \App\Models\Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->create(['role' => 'attending', 'practice_id' => $practice->id]);
    
    $this->actingAs($user)
        ->get(route('radiology'))
        ->assertOk()
        ->assertSeeLivewire(RadiologyManager::class);
});

it('prevents patients from accessing the radiology manager', function () {
    $practice = \App\Models\Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->create(['role' => 'patient', 'practice_id' => $practice->id]);
    
    $this->actingAs($user)
        ->get(route('radiology'))
        ->assertForbidden();
});

it('can create a new radiology order', function () {
    $practice = \App\Models\Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->create(['role' => 'attending', 'practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);

    Livewire::actingAs($user)
        ->test(OrderForm::class)
        ->set('patient_id', $patient->id)
        ->set('procedure_name', 'MRI Brain w/o Contrast')
        ->set('clinical_indication', 'Chronic headaches')
        ->call('save')
        ->assertDispatched('order-created')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('radiology_orders', [
        'patient_id' => $patient->id,
        'procedure_name' => 'MRI Brain w/o Contrast',
        'status' => 'ordered',
        'ordered_by' => $user->id,
    ]);
});

it('can upload a radiology report and update order status', function () {
    $practice = \App\Models\Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $user = User::factory()->create(['role' => 'attending', 'practice_id' => $practice->id]);
    $patient = Patient::factory()->create(['practice_id' => $practice->id]);
    
    $order = RadiologyOrder::create([
        'practice_id' => $user->practice_id,
        'patient_id' => $patient->id,
        'procedure_name' => 'Chest X-Ray',
        'clinical_indication' => 'Cough',
        'status' => 'ordered',
        'order_date' => now(),
        'ordered_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(ReportUpload::class, ['order' => $order])
        ->set('findings', 'Lungs are clear.')
        ->set('impression', 'Normal chest x-ray.')
        ->call('save')
        ->assertDispatched('report-uploaded')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('radiology_reports', [
        'radiology_order_id' => $order->id,
        'patient_id' => $patient->id,
        'findings' => 'Lungs are clear.',
        'impression' => 'Normal chest x-ray.',
    ]);

    $this->assertDatabaseHas('radiology_orders', [
        'id' => $order->id,
        'status' => 'reported',
    ]);
});
