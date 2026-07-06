<flux:main>
    <section class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="xl">{{ __('Pharmacies') }}</flux:heading>
                <flux:subheading>{{ __('Manage preferred pharmacies for patient prescriptions.') }}</flux:subheading>
            </div>

            <flux:button wire:click="openCreateModal">
                {{ __('Add Pharmacy') }}
            </flux:button>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('NCPDP') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Phone') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Address') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                        @forelse ($pharmacies as $pharmacy)
                            <tr>
                                <td class="px-4 py-3 text-sm text-zinc-800 dark:text-zinc-200">{{ $pharmacy['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $pharmacy['ncpdp'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $pharmacy['phone'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $pharmacy['address'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $pharmacy['id'] }})">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="deletePharmacy({{ $pharmacy['id'] }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500">
                                    {{ __('No pharmacies configured yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($showCreateModal || $showEditModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-lg rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-4">
                        {{ $showEditModal ? __('Edit Pharmacy') : __('Add Pharmacy') }}
                    </flux:heading>

                    <div class="space-y-4">
                        <flux:input wire:model="name" :label="__('Name')" required />
                        <flux:input wire:model="ncpdp" :label="__('NCPDP')" />
                        <flux:input wire:model="phone" :label="__('Phone')" />
                        <flux:input wire:model="address" :label="__('Address')" />
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <flux:button variant="ghost" wire:click="resetForm">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" wire:click="savePharmacy">
                            {{ $showEditModal ? __('Save Changes') : __('Create Pharmacy') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </section>
</flux:main>
