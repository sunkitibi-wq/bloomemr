<?php

use App\Livewire\Settings\Pharmacies;
use App\Models\Pharmacy;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);

    $this->user->givePermissionTo('manage_users');
});

test('staff can create and manage pharmacies from settings', function () {
    $this->actingAs($this->user);

    Livewire::test(Pharmacies::class)
        ->call('openCreateModal')
        ->set('name', 'Northside Pharmacy')
        ->set('ncpdp', '1234567')
        ->set('phone', '555-0100')
        ->set('address', '100 Main St')
        ->call('savePharmacy');

    $pharmacy = Pharmacy::first();

    expect($pharmacy)->not->toBeNull()
        ->and($pharmacy->name)->toBe('Northside Pharmacy')
        ->and($pharmacy->practice_id)->toBe($this->practice->id);
});
