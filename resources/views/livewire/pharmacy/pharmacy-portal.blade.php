<flux:main>
    <section class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="xl">{{ __('Pharmacy Portal') }}</flux:heading>
                <flux:subheading>{{ __('Review pending prescriptions and update fulfillment status.') }}</flux:subheading>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Patient') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Medication') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Pharmacy') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                        @forelse ($prescriptions as $prescription)
                            <tr>
                                <td class="px-4 py-3 text-sm text-zinc-800 dark:text-zinc-200">
                                    {{ $prescription->patient?->full_name ?? __('Unknown patient') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $prescription->medication?->name ?? __('Medication unavailable') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $prescription->pharmacy?->name ?? __('Unassigned') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ ucfirst((string) ($prescription->fulfillment_status ?? 'pending')) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="updateFulfillmentStatus({{ $prescription->id }}, 'pending')">
                                            {{ __('Pending') }}
                                        </flux:button>
                                        <flux:button size="sm" variant="primary" wire:click="updateFulfillmentStatus({{ $prescription->id }}, 'filled')">
                                            {{ __('Filled') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500">
                                    {{ __('No prescriptions found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</flux:main>
