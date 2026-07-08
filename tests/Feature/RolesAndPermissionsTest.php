<?php

use App\Livewire\Settings\RolesAndPermissions;
use App\Models\Practice;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Seed standard roles & permissions
    $this->seed(RoleAndPermissionSeeder::class);

    // Create a practice
    Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
});

test('roles and permissions page is not accessible by non-super-admins', function () {
    $user = User::factory()->create([
        'role' => 'attending',
        'practice_id' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('settings.roles'))
        ->assertStatus(403);
});

test('roles and permissions page is accessible by super-admins', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('settings.roles'))
        ->assertStatus(200);
});

test('super admin can create a new role and assign permissions', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->set('roleName', 'new_custom_role')
        ->set('rolePermissions', ['view_patients', 'create_patients'])
        ->call('saveRole')
        ->assertHasNoErrors();

    $role = Role::findByName('new_custom_role');
    expect($role)->not->toBeNull();
    expect($role->hasPermissionTo('view_patients'))->toBeTrue();
    expect($role->hasPermissionTo('create_patients'))->toBeTrue();
});

test('super admin cannot delete a role with assigned users', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    $role = Role::findByName('attending');

    // Assign a user to the role
    $user = User::factory()->create([
        'role' => 'attending',
        'practice_id' => 1,
    ]);

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->call('deleteRole', $role->id);

    // Role should still exist
    expect(Role::findById($role->id))->not->toBeNull();
});

test('super admin can delete an empty role', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    $role = Role::create(['name' => 'empty_role']);

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->call('deleteRole', $role->id);

    expect(Role::where('name', 'empty_role')->first())->toBeNull();
});

test('super admin can create a new permission', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->set('newPermissionName', 'custom_permission')
        ->call('createPermission')
        ->assertHasNoErrors();

    expect(Permission::findByName('custom_permission'))->not->toBeNull();
});

test('super admin cannot delete a permission assigned to roles', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    $permission = Permission::findByName('view_patients');

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::findById($permission->id))->not->toBeNull();
});

test('super admin can delete an unassigned permission', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'practice_id' => 1,
    ]);

    $permission = Permission::create(['name' => 'unassigned_perm']);

    Livewire::actingAs($superAdmin)
        ->test(RolesAndPermissions::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::where('name', 'unassigned_perm')->first())->toBeNull();
});
