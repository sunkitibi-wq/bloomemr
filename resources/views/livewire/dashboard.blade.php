<flux:main class="space-y-8 bg-[#f7f9fb] dark:bg-zinc-950 min-h-screen">
    <!-- Header/Welcome Banner -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-trust-navy dark:text-zinc-100 font-bold tracking-tight">
                {{ __('Dashboard') }}
            </flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Welcome back, :name. Here is your clinical overview for today.', ['name' => auth()->user()->name]) }}
            </flux:text>
        </div>
        
        <!-- Quick Action -->
        <flux:button variant="primary" href="{{ route('patients.create') }}" wire:navigate>
            <flux:icon.plus class="size-4 mr-1.5" />
            {{ __('New Patient') }}
        </flux:button>
    </div>

    <!-- Clinical Alerts -->
    @if ($this->clinicalAlerts->isNotEmpty())
        <div class="space-y-3">
            @foreach ($this->clinicalAlerts as $alert)
                <div class="border-l-4 border-red-500 bg-red-50/50 p-4 dark:bg-red-950/20 rounded-r-xl border border-zinc-200/50 dark:border-zinc-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <flux:icon.exclamation-triangle class="size-5 text-red-500 shrink-0" />
                        <span class="text-sm font-medium text-red-800 dark:text-red-200">{{ $alert['message'] }}</span>
                    </div>
                    <flux:button variant="ghost" size="sm" href="{{ $alert['action_url'] }}" wire:navigate class="text-red-600 hover:text-red-700 dark:text-red-400">
                        {{ __('Resolve') }}
                    </flux:button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="p-3 bg-primary/5 dark:bg-primary/10 text-primary dark:text-zinc-100 rounded-xl">
                <flux:icon.calendar class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('Today\'s Encounters') }}</div>
                <div class="mt-1 text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $this->todayCount }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="p-3 bg-growth-sage/10 text-growth-sage rounded-xl">
                <flux:icon.users class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('Total Patients') }}</div>
                <div class="mt-1 text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $this->totalPatients }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="p-3 bg-amber-500/10 text-amber-500 rounded-xl">
                <flux:icon.document-text class="size-6" />
            </div>
            <div>
                <div class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('Pending Notes') }}</div>
                <div class="mt-1 text-3xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $this->pendingCount }}</div>
            </div>
        </div>
    </div>

    <!-- Reporting Summary -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Monthly Revenue') }}</div>
            <div class="mt-2 text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono">${{ number_format($this->monthlyRevenue, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Open Invoices') }}</div>
            <div class="mt-2 text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $this->unpaidInvoices }}</div>
        </div>
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('This Month Encounters') }}</div>
            <div class="mt-2 text-2xl font-bold text-trust-navy dark:text-zinc-100 font-mono">{{ $this->thisMonthEncounters }}</div>
        </div>
    </div>

    <!-- Bento-style Sections Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- 1. Recent Encounters -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">{{ __('Recent Encounters') }}</flux:heading>
                <flux:icon.calendar-days class="size-5 text-zinc-400" />
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($this->recentEncounters as $encounter)
                    <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                        <div>
                            <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $encounter->patient->full_name }}</div>
                            <div class="text-xs text-zinc-400 mt-0.5">{{ ucfirst($encounter->type) }} &middot; {{ $encounter->encounter_date->diffForHumans() }}</div>
                        </div>
                        <flux:badge size="sm" variant="{{ $encounter->status === 'signed' ? 'success' : 'warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $encounter->status)) }}
                        </flux:badge>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 py-4 italic text-center">{{ __('No recent encounters.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- 2. Today's Schedule -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">{{ __('Today\'s Schedule') }}</flux:heading>
                <flux:icon.calendar-days class="size-5 text-zinc-400" />
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($this->todayAppointments as $appt)
                    <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                        <div>
                            <a href="{{ route('patients.show', $appt->patient) }}" wire:navigate class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 hover:underline">
                                {{ $appt->patient->full_name }}
                            </a>
                            <div class="text-xs text-zinc-400 mt-0.5 font-mono">
                                {{ \Carbon\Carbon::parse($appt->start_time)->format('h:i A') }} &middot; {{ $appt->provider->name }}
                            </div>
                        </div>
                        @php
                            $statusColors = [
                                'scheduled' => 'warning',
                                'checked_in' => 'zinc',
                                'completed' => 'success',
                                'cancelled' => 'zinc',
                                'no_show' => 'danger',
                            ];
                            $variant = $statusColors[$appt->status] ?? 'zinc';
                        @endphp
                        <flux:badge size="sm" :variant="$variant">
                            {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                        </flux:badge>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 py-4 italic text-center">{{ __('No appointments scheduled for today.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- 3. Recent Patient Charts -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">{{ __('Recent Patient Charts') }}</flux:heading>
                <flux:icon.clock class="size-5 text-zinc-400" />
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($this->recentCharts as $patient)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-2.5">
                            @if ($patient->photo_path)
                                <img src="{{ Storage::url($patient->photo_path) }}" alt="" class="h-8 w-8 rounded-full object-cover border border-zinc-200 dark:border-zinc-700 shadow-sm" />
                            @else
                                <div class="h-8 w-8 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-[10px] text-zinc-500 font-bold font-mono">
                                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('patients.show', $patient) }}" wire:navigate class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 hover:underline">
                                    {{ $patient->full_name }}
                                </a>
                                <div class="text-xs text-zinc-400 mt-0.5 font-mono">{{ $patient->mrn }}</div>
                            </div>
                        </div>
                        <flux:button variant="ghost" size="sm" href="{{ route('patients.show', $patient) }}" wire:navigate>
                            {{ __('Open') }}
                        </flux:button>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 py-4 italic text-center">{{ __('No charts opened recently.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Co-sign Queue Section -->
    @if (auth()->user()->role === 'attending' && $this->coSignQueue->isNotEmpty())
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 bg-white dark:bg-zinc-900 shadow-xs">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">{{ __('Notes Awaiting Co-signature') }}</flux:heading>
                <flux:icon.pencil-square class="size-5 text-zinc-400" />
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($this->coSignQueue as $encounter)
                    <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                        <div>
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ $encounter->patient->full_name }}</div>
                            <div class="text-xs text-zinc-400 mt-0.5">
                                {{ __('Written by: :provider', ['provider' => $encounter->provider->name]) }} &middot; {{ $encounter->encounter_date->format('M j, Y') }}
                            </div>
                        </div>
                        <flux:button size="sm" href="{{ route('encounters.note', $encounter) }}" wire:navigate>
                            {{ __('Review & Sign') }}
                        </flux:button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</flux:main>
