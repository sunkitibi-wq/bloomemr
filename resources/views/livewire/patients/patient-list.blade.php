<flux:main>
    <flux:heading size="xl" level="1">{{ __('Patients') }}</flux:heading>

    <div class="mt-6 flex items-center justify-between gap-4">
        <flux:input wire:model.live.debounce="search" placeholder="{{ __('Search patients...') }}" class="max-w-sm" />

        <flux:button variant="primary" href="{{ route('patients.create') }}" wire:navigate>
            {{ __('New Patient') }}
        </flux:button>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500">{{ __('MRN') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500">{{ __('DOB') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500">{{ __('Phone') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500">{{ __('Provider') }}</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-neutral-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($this->patients as $patient)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <td class="px-4 py-3 text-sm font-mono">{{ $patient->mrn }}</td>
                        <td class="px-4 py-3 text-sm font-medium flex items-center gap-2.5">
                            @if ($patient->photo_path)
                                <img src="{{ Storage::url($patient->photo_path) }}" alt="" class="h-6 w-6 rounded-full object-cover border border-zinc-200 dark:border-zinc-700 shadow-sm" />
                            @else
                                <div class="h-6 w-6 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-250 dark:border-zinc-705 flex items-center justify-center text-[9px] text-zinc-500 dark:text-zinc-400 font-bold font-mono">
                                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $patient->full_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $patient->date_of_birth?->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $patient->phone }}</td>
                        <td class="px-4 py-3 text-sm">{{ $patient->primaryProvider?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <flux:button variant="ghost" size="sm" href="{{ route('patients.show', $patient) }}" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-neutral-500">
                            {{ __('No patients found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->patients->links() }}
    </div>
</flux:main>
