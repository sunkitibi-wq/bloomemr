<?php

use App\Livewire\Settings\UserManagement;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Practice One', 'slug' => 'practice-one']);

    // Attending user
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');

    // Super admin user
    $this->admin = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'super_admin',
    ]);
    $this->admin->assignRole('super_admin');
});

test('non super admins are unauthorized to access user management', function () {
    $this->actingAs($this->user);

    Livewire::test(UserManagement::class)
        ->assertStatus(403);
});

test('super admins can access user management settings', function () {
    $this->actingAs($this->admin);

    Livewire::test(UserManagement::class)
        ->assertOk()
        ->assertSee('Practice Users')
        ->assertSee($this->user->name);
});

test('super admin can create a user with a role', function () {
    $this->actingAs($this->admin);

    Livewire::test(UserManagement::class)
        ->call('openCreateModal')
        ->set('name', 'New Clinical Staff')
        ->set('email', 'staff@test.com')
        ->set('role', 'clinical_staff')
        ->set('password', 'secret12345')
        ->call('saveUser')
        ->assertHasNoErrors();

    $newUser = User::where('email', 'staff@test.com')->first();
    expect($newUser)->not->toBeNull()
        ->and($newUser->name)->toBe('New Clinical Staff')
        ->and($newUser->practice_id)->toBe($this->practice->id)
        ->and($newUser->hasRole('clinical_staff'))->toBeTrue();
});

test('super admin can toggle user status', function () {
    $this->actingAs($this->admin);

    expect($this->user->is_active)->toBeTrue();

    Livewire::test(UserManagement::class)
        ->call('toggleUserStatus', $this->user->id);

    $this->user->refresh();
    expect($this->user->is_active)->toBeFalse();
});

test('super admin can assign direct permission override', function () {
    $this->actingAs($this->admin);

    $staff = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'clinical_staff',
    ]);
    $staff->assignRole('clinical_staff');

    expect($staff->hasDirectPermission('sign_encounters'))->toBeFalse();

    Livewire::test(UserManagement::class)
        ->call('openEditModal', $staff->id)
        ->set('userPermissions', ['sign_encounters'])
        ->call('saveUser')
        ->assertHasNoErrors();

    $staff->refresh();
    expect($staff->hasDirectPermission('sign_encounters'))->toBeTrue();
});
