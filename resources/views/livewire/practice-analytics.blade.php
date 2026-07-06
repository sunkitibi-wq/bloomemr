<flux:main class="space-y-8 bg-[#f7f9fb] dark:bg-zinc-950 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-trust-navy dark:text-zinc-100 font-bold tracking-tight">
                {{ __('Practice Analytics') }}
            </flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Clinician utilisation, patient demographics, encounter trends, and financial dashboards.') }}
            </flux:text>
        </div>

        <!-- Month selector -->
        <div class="flex items-center gap-2">
            <flux:label class="text-xs text-zinc-500">{{ __('Period') }}</flux:label>
            <flux:select wire:model.live="month" class="text-sm">
                @foreach ($this->availableMonths as $m)
                    <option value="{{ $m['value'] }}" @if($m['value'] === $month && $m['year'] === $year) selected @endif>
                        {{ $m['label'] }}
                    </option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Revenue KPI Row -->
    @php $revenue = $this->revenue; @endphp
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-green-50 dark:bg-green-950/30 text-green-600 rounded-xl">
                <flux:icon.currency-dollar class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('This Month Revenue') }}</div>
                <div class="text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">${{ number_format($revenue['month_revenue'], 2) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-primary/5 dark:bg-primary/10 text-primary rounded-xl">
                <flux:icon.chart-bar class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('YTD Revenue') }}</div>
                <div class="text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">${{ number_format($revenue['year_revenue'], 2) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 rounded-xl">
                <flux:icon.clock class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Outstanding Balance') }}</div>
                <div class="text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">${{ number_format($revenue['outstanding_balance'], 2) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-red-50 dark:bg-red-950/30 text-red-500 rounded-xl">
                <flux:icon.document-text class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ __('Unpaid Invoices') }}</div>
                <div class="text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono mt-1">{{ $revenue['unpaid_count'] }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Encounter Trend Chart -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
            <flux:heading size="lg">{{ __('Encounter Volume (Last 6 Months)') }}</flux:heading>
            @php $trend = $this->encounterTrend; $max = $trend->max('count') ?: 1; @endphp
            <div class="space-y-3 pt-2">
                @foreach ($trend as $row)
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs text-zinc-500 shrink-0 font-medium">{{ $row['label'] }}</div>
                        <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full h-5 overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-r from-primary to-primary/70 rounded-full transition-all duration-700"
                                style="width: {{ $max > 0 ? round(($row['count'] / $max) * 100) : 0 }}%"
                            ></div>
                        </div>
                        <div class="w-8 text-xs text-right font-mono font-bold text-zinc-700 dark:text-zinc-300 shrink-0">{{ $row['count'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Clinician Utilisation -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
            <flux:heading size="lg">{{ __('Clinician Utilisation') }}</flux:heading>
            <div class="space-y-3 pt-2">
                @forelse ($this->clinicianUtilization as $clinician)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0">
                            {{ collect(explode(' ', $clinician->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $clinician->name }}</div>
                            <div class="text-xs text-zinc-400 capitalize">{{ str_replace('_', ' ', $clinician->role) }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-lg font-bold font-mono text-trust-navy dark:text-zinc-100">{{ $clinician->encounter_count }}</div>
                            <div class="text-xs text-zinc-400">{{ __('encounters') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 italic text-center py-6">{{ __('No encounter data for the selected period.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Demographics Row -->
    @php $demo = $this->demographics; @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Age Groups -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Age Distribution') }}</flux:heading>
                <flux:badge size="sm" color="blue">{{ $demo['total'] }} {{ __('patients') }}</flux:badge>
            </div>
            <div class="space-y-2.5">
                @foreach ($demo['age_groups'] as $group => $count)
                    @php $pct = $demo['total'] > 0 ? round(($count / $demo['total']) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group }}</span>
                            <span class="text-zinc-500">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-primary/60 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Gender Identity -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
            <flux:heading size="lg">{{ __('Gender Identity') }}</flux:heading>
            <div class="space-y-2.5">
                @forelse ($demo['by_gender'] as $gender => $count)
                    @php $pct = $demo['total'] > 0 ? round(($count / $demo['total']) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300 capitalize">{{ $gender ?: 'Not Specified' }}</span>
                            <span class="text-zinc-500">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-growth-sage/70 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 italic">{{ __('No patient data.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Race / Ethnicity -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
            <flux:heading size="lg">{{ __('Race / Ethnicity') }}</flux:heading>
            <div class="space-y-2.5">
                @forelse ($demo['by_ethnicity'] as $ethnicity => $count)
                    @php $pct = $demo['total'] > 0 ? round(($count / $demo['total']) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300 capitalize">{{ $ethnicity ?: 'Not Specified' }}</span>
                            <span class="text-zinc-500">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400/70 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 italic">{{ __('No patient data.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</flux:main>
