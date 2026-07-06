<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('Roles & Permissions')]
class RolesAndPermissions extends Component
{
    // List state
    /** @var array<int, array<string, mixed>> */
    public array $roles = [];

    /** @var array<int, array<string, mixed>> */
    public array $permissions = [];

    // Role form
    public ?int $selectedRoleId = null;

    public string $roleName = '';

    /** @var array<string> */
    public array $rolePermissions = [];

    public bool $showRoleModal = false;

    // Permission form
    public string $newPermissionName = '';

    public function mount(): void
    {
        if (! Auth::user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->loadData();
    }

    public function loadData(): void
    {
        $this->roles = Role::withCount('users')
            ->with('permissions')
            ->get()
            ->toArray();

        $this->permissions = Permission::withCount('roles')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function openCreateRoleModal(): void
    {
        $this->resetRoleForm();
        $this->showRoleModal = true;
    }

    public function openEditRoleModal(int $roleId): void
    {
        $this->resetRoleForm();

        $role = Role::with('permissions')->findOrFail($roleId);
        $this->selectedRoleId = (int) $role->id;
        $this->roleName = $role->name;
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();

        $this->showRoleModal = true;
    }

    public function saveRole(): void
    {
        $this->validate([
            'roleName' => 'required|string|max:64|alpha_dash',
            'rolePermissions' => 'array',
        ]);

        if ($this->selectedRoleId) {
            $role = Role::findOrFail($this->selectedRoleId);
            $role->name = $this->roleName;
            $role->save();
        } else {
            $role = Role::findOrCreate($this->roleName);
        }

        $role->syncPermissions($this->rolePermissions);

        Flux::toast(
            variant: 'success',
            text: $this->selectedRoleId
                ? __('Role updated successfully.')
                : __('Role created successfully.')
        );

        $this->showRoleModal = false;
        $this->resetRoleForm();
        $this->loadData();
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::withCount('users')->findOrFail($roleId);

        if ($role->users_count > 0) {
            Flux::toast(
                variant: 'danger',
                text: __('Cannot delete a role that has users assigned to it.')
            );

            return;
        }

        $role->delete();

        Flux::toast(variant: 'success', text: __('Role deleted.'));
        $this->loadData();
    }

    public function createPermission(): void
    {
        $this->validate([
            'newPermissionName' => 'required|string|max:64|alpha_dash|unique:permissions,name',
        ]);

        Permission::create(['name' => $this->newPermissionName]);
        $this->newPermissionName = '';

        Flux::toast(variant: 'success', text: __('Permission created.'));
        $this->loadData();
    }

    public function deletePermission(int $permissionId): void
    {
        $permission = Permission::withCount('roles')->findOrFail($permissionId);

        if ($permission->roles_count > 0) {
            Flux::toast(
                variant: 'danger',
                text: __('Cannot delete a permission assigned to one or more roles.')
            );

            return;
        }

        $permission->delete();

        Flux::toast(variant: 'success', text: __('Permission deleted.'));
        $this->loadData();
    }

    protected function resetRoleForm(): void
    {
        $this->selectedRoleId = null;
        $this->roleName = '';
        $this->rolePermissions = [];
    }

    public function render(): View
    {
        return view('livewire.settings.roles-and-permissions');
    }
}
