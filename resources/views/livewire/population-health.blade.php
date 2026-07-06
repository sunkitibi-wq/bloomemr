<flux:main class="space-y-8 bg-[#f7f9fb] dark:bg-zinc-950 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-trust-navy dark:text-zinc-100 font-bold tracking-tight">
                {{ __('Population Health & Care Gap Registries') }}
            </flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Manage clinical cohorts, identify preventive care gaps, and track vaccine compliance.') }}
            </flux:text>
        </div>
        <flux:button wire:click="refreshRegistries" variant="primary">
            <flux:icon.arrow-path class="size-4 mr-1.5" />
            {{ __('Refresh Registries') }}
        </flux:button>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50/70 dark:bg-green-950/20 dark:border-green-800 p-4">
            <flux:icon.check-circle class="size-5 text-green-600 shrink-0" />
            <span class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-primary/5 dark:bg-primary/10 text-primary rounded-xl">
                <flux:icon.user-group class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Active Cohorts') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->cohortCount }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-growth-sage/10 text-growth-sage rounded-xl">
                <flux:icon.users class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Enrolled Patients') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->totalEnrolled }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 rounded-xl">
                <flux:icon.exclamation-triangle class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Open Care Gaps') }}</div>
                <div class="text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $this->openGapCount }}</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-1 bg-zinc-100 dark:bg-zinc-800/60 p-1 rounded-xl w-fit">
        <button
            wire:click="$set('activeTab', 'cohorts')"
            class="{{ $activeTab === 'cohorts' ? 'bg-white dark:bg-zinc-900 shadow-sm text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-4 py-2 text-sm font-medium rounded-lg transition-all"
        >
            {{ __('Cohort Registries') }}
        </button>
        <button
            wire:click="$set('activeTab', 'gaps')"
            class="{{ $activeTab === 'gaps' ? 'bg-white dark:bg-zinc-900 shadow-sm text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-4 py-2 text-sm font-medium rounded-lg transition-all"
        >
            {{ __('Care Gaps') }}
        </button>
    </div>

    <!-- Cohorts Tab -->
    @if ($activeTab === 'cohorts')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($this->cohorts as $cohort)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs space-y-3" wire:key="cohort-{{ $cohort->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base">{{ $cohort->name }}</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">{{ $cohort->description }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $cohort->members_count }}</div>
                            <div class="text-xs text-zinc-400 mt-0.5">{{ __('patients') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:badge size="sm" color="blue">{{ __('Active Registry') }}</flux:badge>
                        <span class="text-xs text-zinc-400">{{ __('Last updated: :date', ['date' => $cohort->updated_at->diffForHumans()]) }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 text-zinc-400 italic text-sm">
                    {{ __('No cohort registries yet. Click "Refresh Registries" to auto-populate based on patient data.') }}
                </div>
            @endforelse
        </div>
    @endif

    <!-- Care Gaps Tab -->
    @if ($activeTab === 'gaps')
        <div class="space-y-4">
            <!-- Filter -->
            <div class="flex gap-2 items-center">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Show:') }}</span>
                <div class="flex gap-1 bg-zinc-100 dark:bg-zinc-800/60 p-1 rounded-lg">
                    @foreach (['open' => 'Open', 'closed' => 'Resolved'] as $status => $label)
                        <button
                            wire:click="$set('gapStatusFilter', '{{ $status }}')"
                            class="{{ $gapStatusFilter === $status ? 'bg-white dark:bg-zinc-900 shadow-sm text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('Patient') }}</th>
                            <th class="px-6 py-3.5">{{ __('Gap Type') }}</th>
                            <th class="px-6 py-3.5">{{ __('Description') }}</th>
                            <th class="px-6 py-3.5">{{ __('Due Date') }}</th>
                            <th class="px-6 py-3.5">{{ __('Status') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($this->careGaps as $gap)
                            @php
                                $isDue = $gap->due_date && $gap->due_date->isPast() && $gap->status === 'open';
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors {{ $isDue ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}" wire:key="gap-{{ $gap->id }}">
                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                    <a href="{{ route('patients.show', $gap->patient) }}" wire:navigate class="hover:text-primary transition-colors">
                                        {{ $gap->patient->full_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm" color="purple">{{ ucwords(str_replace('_', ' ', $gap->gap_type)) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 text-zinc-500 text-xs max-w-xs">{{ $gap->description }}</td>
                                <td class="px-6 py-4 text-xs {{ $isDue ? 'text-red-500 font-bold' : 'text-zinc-500' }}">
                                    {{ $gap->due_date ? $gap->due_date->format('M j, Y') : '—' }}
                                    @if ($isDue)
                                        <span class="block text-xs font-normal text-red-400">{{ __('Overdue') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge size="sm" color="{{ $gap->status === 'open' ? 'amber' : 'green' }}">
                                        {{ ucfirst($gap->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($gap->status === 'open')
                                        <flux:button size="sm" variant="ghost" wire:click="resolveCareGap({{ $gap->id }})">
                                            {{ __('Resolve') }}
                                        </flux:button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic text-sm">
                                    {{ __('No :status care gaps found.', ['status' => $gapStatusFilter]) }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</flux:main>
