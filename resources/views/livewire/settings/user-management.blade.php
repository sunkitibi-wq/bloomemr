<flux:main>
    <section class="w-full space-y-6">
        @include('partials.settings-heading')

        <flux:heading class="sr-only">{{ __('User Management settings') }}</flux:heading>

        <x-settings.layout :heading="__('User Management')" :subheading="__('Manage practice users, roles, and direct permission overrides.')" class="max-w-none">
        <div class="mt-6 space-y-6 w-full max-w-4xl">
            <!-- Create User Header -->
            <div class="flex justify-between items-center">
                <flux:heading size="md">{{ __('Practice Users') }}</flux:heading>
                <flux:button variant="primary" wire:click="openCreateModal">
                    <flux:icon.plus class="size-4 mr-1.5" />
                    {{ __('Add User') }}
                </flux:button>
            </div>

            <!-- Users Table -->
            <div class="border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('Name / Email') }}</th>
                            <th class="px-6 py-3.5">{{ __('Role') }}</th>
                            <th class="px-6 py-3.5">{{ __('Status') }}</th>
                            <th class="px-6 py-3.5">{{ __('Created') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($users as $u)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $u['name'] }}</div>
                                    <div class="text-xs text-zinc-400 mt-0.5">{{ $u['email'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm" color="zinc">
                                        {{ str_replace('_', ' ', strtoupper($u['roles'][0]['name'] ?? $u['role'] ?? '')) }}
                                    </flux:badge>
                                    @if (! empty($u['permissions']))
                                        <div class="text-[10px] text-primary mt-1.5 font-medium flex flex-wrap gap-1">
                                            <span class="text-zinc-400">{{ __('+ Overrides:') }}</span>
                                            @foreach ($u['permissions'] as $p)
                                                <span class="bg-primary/10 text-primary px-1 py-0.5 rounded font-mono">{{ $p['name'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <button 
                                        type="button" 
                                        wire:click="toggleUserStatus({{ $u['id'] }})"
                                        class="inline-flex items-center gap-1.5 cursor-pointer"
                                        @if ($u['id'] === auth()->id()) disabled @endif
                                    >
                                        <span class="h-2 w-2 rounded-full {{ $u['is_active'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        <span class="text-xs font-semibold {{ $u['is_active'] ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                            {{ $u['is_active'] ? __('Active') : __('Inactive') }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-xs text-zinc-400">
                                    {{ \Carbon\Carbon::parse($u['created_at'])->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $u['id'] }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    @if ($u['id'] !== auth()->id())
                                        <flux:button size="sm" variant="ghost" class="text-red-500 hover:text-red-600" wire:click="deleteUser({{ $u['id'] }})" wire:confirm="{{ __('Are you sure you want to delete this user?') }}">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-settings.layout>

    <!-- Create User Modal Overlay -->
    <div x-data="{ open: @entangle('showCreateModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                <div>
                    <flux:heading size="lg">{{ __('Add New Practice User') }}</flux:heading>
                    <flux:text class="text-xs mt-1">{{ __('Enter details to register a new user in this practice.') }}</flux:text>
                </div>

                <form wire:submit.prevent="saveUser" class="space-y-4">
                    <flux:input wire:model="name" :label="__('Name')" required />
                    <flux:input wire:model="email" :label="__('Email Address')" type="email" required />
                    <flux:input wire:model="password" :label="__('Temporary Password')" type="password" required />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label for="create_role">{{ __('User Role') }}</flux:label>
                            <flux:select id="create_role" wire:model.live="role" required class="mt-1">
                                <flux:select.option value="">{{ __('Select Role') }}</flux:select.option>
                                @foreach ($roles as $r)
                                    <flux:select.option value="{{ $r['name'] }}">{{ ucfirst(str_replace('_', ' ', $r['name'])) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="checkbox" wire:model="is_active" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                                <span>{{ __('Active User Account') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Role Permissions Summary -->
                    @if (! empty($role))
                        <div class="border rounded-lg border-zinc-100 dark:border-zinc-800 p-3 bg-zinc-50 dark:bg-zinc-800/40 space-y-2">
                            <span class="text-xs font-semibold text-zinc-500 block">{{ __('Permissions Inherited from Role:') }}</span>
                            <div class="flex flex-wrap gap-1 text-[10px] text-zinc-600 dark:text-zinc-300">
                                @forelse ($this->getActiveRolePermissions() as $rp)
                                    <span class="bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded font-mono">{{ $rp }}</span>
                                @empty
                                    <span class="italic text-zinc-400">{{ __('No default permissions assigned to this role.') }}</span>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Direct Overrides Checkboxes -->
                    <div class="border-t border-zinc-150 dark:border-zinc-850 pt-3 space-y-2">
                        <span class="text-xs font-semibold text-zinc-500 block">{{ __('Direct Override Permissions (Optional):') }}</span>
                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-1">
                            @foreach ($permissionsList as $p)
                                <label class="flex items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="userPermissions" value="{{ $p['name'] }}" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                                    <span class="font-mono text-[11px]">{{ $p['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <flux:button type="button" wire:click="$set('showCreateModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Create User') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal Overlay -->
    <div x-data="{ open: @entangle('showEditModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                <div>
                    <flux:heading size="lg">{{ __('Edit Practice User') }}</flux:heading>
                    <flux:text class="text-xs mt-1">{{ __('Modify account details, roles, and override permissions.') }}</flux:text>
                </div>

                <form wire:submit.prevent="saveUser" class="space-y-4">
                    <flux:input wire:model="name" :label="__('Name')" required />
                    <flux:input wire:model="email" :label="__('Email Address')" type="email" required />
                    <flux:input wire:model="password" :label="__('New Password (leave blank to keep current)')" type="password" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label for="edit_role">{{ __('User Role') }}</flux:label>
                            <flux:select id="edit_role" wire:model.live="role" required class="mt-1">
                                <flux:select.option value="">{{ __('Select Role') }}</flux:select.option>
                                @foreach ($roles as $r)
                                    <flux:select.option value="{{ $r['name'] }}">{{ ucfirst(str_replace('_', ' ', $r['name'])) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="checkbox" wire:model="is_active" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                                <span>{{ __('Active User Account') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Role Permissions Summary -->
                    @if (! empty($role))
                        <div class="border rounded-lg border-zinc-100 dark:border-zinc-800 p-3 bg-zinc-50 dark:bg-zinc-800/40 space-y-2">
                            <span class="text-xs font-semibold text-zinc-500 block">{{ __('Permissions Inherited from Role:') }}</span>
                            <div class="flex flex-wrap gap-1 text-[10px] text-zinc-600 dark:text-zinc-300 font-sans">
                                @forelse ($this->getActiveRolePermissions() as $rp)
                                    <span class="bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded font-mono">{{ $rp }}</span>
                                @empty
                                    <span class="italic text-zinc-400">{{ __('No default permissions assigned to this role.') }}</span>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Direct Overrides Checkboxes -->
                    <div class="border-t border-zinc-150 dark:border-zinc-850 pt-3 space-y-2">
                        <span class="text-xs font-semibold text-zinc-500 block">{{ __('Direct Override Permissions (Optional):') }}</span>
                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-1">
                            @foreach ($permissionsList as $p)
                                <label class="flex items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer">
                                    <input type="checkbox" wire:model="userPermissions" value="{{ $p['name'] }}" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                                    <span class="font-mono text-[11px]">{{ $p['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <flux:button type="button" wire:click="$set('showEditModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</flux:main>
