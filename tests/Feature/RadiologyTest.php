<?php

use App\Livewire\Patients\PatientRadiology;
use App\Livewire\Portal\PortalRadiology;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\RadiologyOrder;
use App\Models\RadiologyReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    ]);
});

test('clinician can place a new radiology imaging order', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientRadiology::class, ['patient' => $this->patient])
        ->set('procedureName', 'MRI Brain without contrast')
        ->set('clinicalIndication', 'Rule out structural lesion due to headaches')
        ->call('placeOrder')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('radiology_orders', [
        'patient_id' => $this->patient->id,
        'procedure_name' => 'MRI Brain without contrast',
        'clinical_indication' => 'Rule out structural lesion due to headaches',
        'status' => 'ordered',
    ]);
});

test('technician or radiologist can publish radiology report findings and attach scan', function () {
    Storage::fake('public');

    $order = RadiologyOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'ordered_by' => $this->provider->id,
        'procedure_name' => 'X-Ray Chest PA',
        'clinical_indication' => 'Chronic cough',
        'status' => 'ordered',
        'order_date' => now(),
    ]);

    $file = UploadedFile::fake()->create('chest_xray.pdf', 500);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientRadiology::class, ['patient' => $this->patient])
        ->call('openReportModal', $order->id)
        ->set('findings', 'Lungs are clear. No active disease.')
        ->set('impression', 'Normal chest x-ray.')
        ->set('scanFile', $file)
        ->call('submitReport')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('reported');
    
    $report = RadiologyReport::where('radiology_order_id', $order->id)->first();
    expect($report)->not->toBeNull();
    expect($report->findings)->toBe('Lungs are clear. No active disease.');
    expect($report->impression)->toBe('Normal chest x-ray.');
    
    Storage::disk('public')->assertExists($report->attachment_path);
});

test('patient portal user can view their published radiology imaging reports', function () {
    $order = RadiologyOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'ordered_by' => $this->provider->id,
        'procedure_name' => 'CT Head',
        'status' => 'reported',
        'order_date' => now(),
    ]);

    $report = RadiologyReport::create([
        'practice_id' => $this->practice->id,
        'radiology_order_id' => $order->id,
        'patient_id' => $this->patient->id,
        'radiologist_id' => $this->provider->id,
        'findings' => 'No acute intracranial hemorrhage.',
        'impression' => 'Negative head CT.',
        'reported_at' => now(),
    ]);

    Livewire::actingAs($this->guardian, 'portal')
        ->test(PortalRadiology::class)
        ->assertOk()
        ->assertSee('CT Head')
        ->call('viewReport', $report->id)
        ->assertSet('showViewModal', true)
        ->assertSee('Negative head CT.');
});
