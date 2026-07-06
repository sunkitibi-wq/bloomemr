<?php

use App\Livewire\Pharmacy\PharmacyPortal;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

test('pharmacist role is seeded with pharmacy portal permissions', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $role = Role::findByName('pharmacist');

    expect($role)->not->toBeNull()
        ->and($role->hasPermissionTo('view_prescriptions'))->toBeTrue()
        ->and($role->hasPermissionTo('update_prescriptions'))->toBeTrue();
});

test('pharmacist can access the pharmacy portal and update prescription fulfillment', function () {
    $practice = Practice::create(['name' => 'Pharmacy Practice', 'slug' => 'pharmacy-practice']);
    $user = User::factory()->create([
        'practice_id' => $practice->id,
        'role' => 'pharmacist',
    ]);

    $user->syncPermissions(['view_prescriptions', 'update_prescriptions']);

    $patient = Patient::factory()->create([
        'practice_id' => $practice->id,
        'primary_provider_id' => $user->id,
    ]);

    $medication = Medication::create([
        'practice_id' => $practice->id,
        'patient_id' => $patient->id,
        'name' => 'Lisinopril',
        'dose' => '10mg',
        'frequency' => 'Daily',
        'prescriber_id' => $user->id,
        'status' => 'active',
    ]);

    $prescription = Prescription::create([
        'practice_id' => $practice->id,
        'patient_id' => $patient->id,
        'medication_id' => $medication->id,
        'status' => 'sent',
        'fulfillment_status' => 'pending',
    ]);

    $this->actingAs($user);

    Livewire::test(PharmacyPortal::class)
        ->assertOk()
        ->assertSee('Pharmacy Portal')
        ->call('updateFulfillmentStatus', $prescription->id, 'filled');

    $prescription->refresh();

    expect($prescription->fulfillment_status)->toBe('filled')
        ->and($prescription->status)->toBe('filled');
});
