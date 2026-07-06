<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('User Management')]
class UserManagement extends Component
{
    // List state
    public array $users = [];

    public array $roles = [];

    public array $permissionsList = [];

    // Modal state
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    // Selected user for editing
    public ?int $selectedUserId = null;

    // Form fields
    public string $name = '';

    public string $email = '';

    public string $role = '';

    public string $password = '';

    public bool $is_active = true;

    public array $userPermissions = []; // Selected direct permissions for edit/create

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        if (! Gate::allows('manage_users')) {
            abort(403, 'Unauthorized.');
        }

        $this->loadData();
    }

    /**
     * Load users and role definitions.
     */
    public function loadData(): void
    {
        $this->users = User::where('practice_id', Auth::user()->practice_id)
            ->with(['roles', 'permissions'])
            ->get()
            ->toArray();

        $this->roles = Role::all()->toArray();
        $this->permissionsList = Permission::all()->toArray();
    }

    /**
     * Open create modal.
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Open edit modal for user.
     */
    public function openEditModal(int $userId): void
    {
        $this->resetForm();
        $user = User::findOrFail($userId);

        if ($user->practice_id !== Auth::user()->practice_id) {
            abort(403);
        }

        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name ?? $user->role ?? '';
        $this->is_active = (bool) $user->is_active;
        $this->userPermissions = $user->permissions->pluck('name')->toArray();

        $this->showEditModal = true;
    }

    /**
     * Toggle status.
     */
    public function toggleUserStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->practice_id !== Auth::user()->practice_id) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            Flux::toast(variant: 'danger', text: __('You cannot deactivate your own account.'));

            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        Flux::toast(variant: 'success', text: $user->is_active ? __('User activated.') : __('User deactivated.'));
        $this->loadData();
    }

    /**
     * Delete user.
     */
    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->practice_id !== Auth::user()->practice_id) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            Flux::toast(variant: 'danger', text: __('You cannot delete your own account.'));

            return;
        }

        $user->delete();

        Flux::toast(variant: 'success', text: __('User deleted successfully.'));
        $this->loadData();
    }

    /**
     * Save user.
     */
    public function saveUser(): void
    {
        $isEdit = (bool) $this->selectedUserId;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $isEdit ? Rule::unique('users', 'email')->ignore($this->selectedUserId) : 'unique:users,email',
            ],
            'role' => 'required|string|in:'.implode(',', array_column($this->roles, 'name')),
            'password' => $isEdit ? 'nullable|string|min:8' : 'required|string|min:8',
            'is_active' => 'boolean',
            'userPermissions' => 'array',
        ];

        $validated = $this->validate($rules);

        if ($isEdit) {
            $user = User::findOrFail($this->selectedUserId);
            if ($user->practice_id !== Auth::user()->practice_id) {
                abort(403);
            }

            $user->name = $this->name;
            $user->email = $this->email;
            $user->is_active = $this->is_active;
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }

            $user->save();

            // Sync role
            $user->syncRoles($this->role);

            // Update role text column for backwards compatibility
            $user->role = $this->role;
            $user->save();

            // Sync direct permissions
            $user->syncPermissions($this->userPermissions);

            Flux::toast(variant: 'success', text: __('User updated successfully.'));
            $this->showEditModal = false;
        } else {
            $user = new User;
            $user->practice_id = Auth::user()->practice_id;
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role = $this->role;
            $user->is_active = $this->is_active;
            $user->password = Hash::make($this->password);
            $user->save();

            $user->assignRole($this->role);
            $user->syncPermissions($this->userPermissions);

            Flux::toast(variant: 'success', text: __('User created successfully.'));
            $this->showCreateModal = false;
        }

        $this->resetForm();
        $this->loadData();
    }

    /**
     * Reset form fields.
     */
    protected function resetForm(): void
    {
        $this->selectedUserId = null;
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->password = '';
        $this->is_active = true;
        $this->userPermissions = [];
    }

    /**
     * Get active role's standard permissions.
     */
    public function getActiveRolePermissions(): array
    {
        if (empty($this->role)) {
            return [];
        }

        try {
            $roleObj = Role::findByName($this->role);

            return $roleObj ? $roleObj->permissions->pluck('name')->toArray() : [];
        } catch (RoleDoesNotExist $e) {
            return [];
        }
    }

    public function render(): View
    {
        return view('livewire.settings.user-management');
    }
}
