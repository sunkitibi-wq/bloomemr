<flux:main>
    <section class="w-full space-y-6">
        @include('partials.settings-heading')

        <flux:heading class="sr-only">{{ __('Roles & Permissions settings') }}</flux:heading>

        <x-settings.layout :heading="__('Roles & Permissions')" :subheading="__('Manage roles and the permissions assigned to them. Restricted to super administrators.')" class="max-w-none">
        <div class="mt-6 space-y-10 w-full max-w-4xl">

            {{-- ========================================================= --}}
            {{-- ROLES SECTION --}}
            {{-- ========================================================= --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <flux:heading size="md">{{ __('Roles') }}</flux:heading>
                    <flux:button variant="primary" wire:click="openCreateRoleModal">
                        <flux:icon.plus class="size-4 mr-1.5" />
                        {{ __('Add Role') }}
                    </flux:button>
                </div>

                <div class="border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">{{ __('Role') }}</th>
                                <th class="px-6 py-3.5">{{ __('Permissions') }}</th>
                                <th class="px-6 py-3.5">{{ __('Users') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($roles as $role)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="role-{{ $role['id'] }}">
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ ucfirst(str_replace('_', ' ', $role['name'])) }}
                                        </span>
                                        <div class="text-xs font-mono text-zinc-400 mt-0.5">{{ $role['name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (! empty($role['permissions']))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($role['permissions'] as $perm)
                                                    <flux:badge size="sm" color="blue" wire:key="rp-{{ $role['id'] }}-{{ $perm['id'] }}">
                                                        {{ $perm['name'] }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="italic text-xs text-zinc-400">{{ __('No permissions') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <flux:badge size="sm" color="{{ $role['users_count'] > 0 ? 'green' : 'zinc' }}">
                                            {{ $role['users_count'] }} {{ Str::plural('user', $role['users_count']) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openEditRoleModal({{ $role['id'] }})">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            class="text-red-500 hover:text-red-600"
                                            wire:click="deleteRole({{ $role['id'] }})"
                                            wire:confirm="{{ __('Delete this role? This will fail if users are assigned to it.') }}"
                                        >
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-zinc-400 italic text-sm">
                                        {{ __('No roles found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- PERMISSIONS SECTION --}}
            {{-- ========================================================= --}}
            <div class="space-y-4">
                <flux:heading size="md">{{ __('Permissions') }}</flux:heading>

                {{-- Add Permission Inline Form --}}
                <form wire:submit.prevent="createPermission" class="flex items-end gap-3 max-w-sm">
                    <flux:field class="flex-1">
                        <flux:label>{{ __('New Permission') }}</flux:label>
                        <flux:input
                            wire:model="newPermissionName"
                            placeholder="e.g. view_reports"
                            class="font-mono"
                        />
                        <flux:error name="newPermissionName" />
                    </flux:field>
                    <flux:button type="submit" variant="primary">
                        {{ __('Add') }}
                    </flux:button>
                </form>

                <div class="border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">{{ __('Permission') }}</th>
                                <th class="px-6 py-3.5">{{ __('Assigned to Roles') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($permissions as $permission)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="perm-{{ $permission['id'] }}">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-zinc-800 dark:text-zinc-200 font-medium">{{ $permission['name'] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <flux:badge size="sm" color="{{ $permission['roles_count'] > 0 ? 'green' : 'zinc' }}">
                                            {{ $permission['roles_count'] }} {{ Str::plural('role', $permission['roles_count']) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            class="text-red-500 hover:text-red-600"
                                            wire:click="deletePermission({{ $permission['id'] }})"
                                            wire:confirm="{{ __('Delete this permission? This will fail if it is assigned to any role.') }}"
                                        >
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-zinc-400 italic text-sm">
                                        {{ __('No permissions defined.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        </x-settings.layout>

        {{-- ========================================================= --}}
        {{-- CREATE / EDIT ROLE MODAL --}}
        {{-- ========================================================= --}}
        <div x-data="{ open: @entangle('showRoleModal') }">
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                    <div>
                        <flux:heading size="lg">
                            {{ $selectedRoleId ? __('Edit Role') : __('Create Role') }}
                        </flux:heading>
                        <flux:text class="text-xs mt-1">
                            {{ __('Set the role name and select which permissions it inherits by default.') }}
                        </flux:text>
                    </div>

                    <form wire:submit.prevent="saveRole" class="space-y-5">
                        <flux:field>
                            <flux:label>{{ __('Role Name') }}</flux:label>
                            <flux:input
                                wire:model="roleName"
                                placeholder="e.g. billing_admin"
                                class="font-mono"
                                :disabled="(bool) $selectedRoleId"
                            />
                            <flux:error name="roleName" />
                            @if ($selectedRoleId)
                                <flux:text class="text-xs text-zinc-400">{{ __('Role name cannot be changed once created.') }}</flux:text>
                            @endif
                        </flux:field>

                        {{-- Permission Checkbox Grid --}}
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-zinc-500 block">{{ __('Permissions') }}</span>
                            @if (! empty($permissions))
                                <div class="border rounded-lg border-zinc-100 dark:border-zinc-800 p-3 grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                                    @foreach ($permissions as $p)
                                        <label class="flex items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer" wire:key="modal-perm-{{ $p['id'] }}">
                                            <input
                                                type="checkbox"
                                                wire:model="rolePermissions"
                                                value="{{ $p['name'] }}"
                                                class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary"
                                            />
                                            <span class="font-mono text-[11px]">{{ $p['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs italic text-zinc-400">{{ __('No permissions defined yet. Add some in the Permissions section.') }}</p>
                            @endif
                            <flux:error name="rolePermissions" />
                        </div>

                        <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                            <flux:button type="button" wire:click="$set('showRoleModal', false)">
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button variant="primary" type="submit">
                                {{ $selectedRoleId ? __('Save Changes') : __('Create Role') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</flux:main>
