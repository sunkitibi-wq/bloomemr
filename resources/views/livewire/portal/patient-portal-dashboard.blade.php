<div class="min-h-screen flex flex-col md:flex-row bg-[#f7f9fb] dark:bg-zinc-950">
    <style>
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }
        .widget-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .widget-card:hover {
            box-shadow: 0px 4px 16px rgba(15, 23, 42, 0.04);
        }
    </style>

    <!-- Sidebar Navigation -->
    <x-portal.sidebar />

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('You have a shared view of your child\'s health records and care logs.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-trust-navy dark:text-zinc-100">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-wider">{{ __('Primary Guardian') }}</p>
                </div>
                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                    {{ auth()->user()->initials() }}
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-8 overflow-y-auto">
            @if ($patient)
                <div class="bento-grid">
                    <!-- 1. Upcoming Appointment -->
                    <div class="col-span-12 lg:col-span-8 widget-card rounded-2xl p-6 bg-white dark:bg-zinc-900 relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="px-2 py-0.5 bg-primary-fixed text-trust-navy text-[10px] font-bold rounded uppercase tracking-wider inline-block">
                                    {{ __('Next Scheduled Appointment') }}
                                </span>
                                <h3 class="font-headline-md text-trust-navy dark:text-zinc-100 mt-2 text-base font-semibold">
                                    {{ $upcomingAppointment ? __('Consultation Session') : __('No Upcoming Appointments Scheduled') }}
                                </h3>
                            </div>
                            @if ($upcomingAppointment)
                                <a href="{{ route('portal.appointments') }}" class="text-trust-navy dark:text-zinc-300 hover:underline text-xs font-semibold flex items-center gap-1">
                                    {{ __('Manage') }} <span class="material-symbols-outlined text-sm">open_in_new</span>
                                </a>
                            @endif
                        </div>

                        @if ($upcomingAppointment)
                            <div class="flex flex-wrap items-center gap-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-zinc-800 flex flex-col items-center justify-center text-trust-navy dark:text-zinc-300">
                                        <span class="text-[9px] font-bold uppercase">{{ $upcomingAppointment->appointment_date->format('M') }}</span>
                                        <span class="text-lg font-bold leading-tight">{{ $upcomingAppointment->appointment_date->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-zinc-400">{{ $upcomingAppointment->appointment_date->format('l') }}, {{ \Carbon\Carbon::parse($upcomingAppointment->start_time)->format('g:i A') }}</p>
                                        <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100">{{ __('In-Person / Telehealth visit') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-zinc-850 flex items-center justify-center text-xs font-semibold font-bold">
                                        {{ strtoupper(substr($upcomingAppointment->provider->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-zinc-400 uppercase">{{ __('Assigned Clinician') }}</p>
                                        <p class="text-xs font-semibold text-trust-navy dark:text-zinc-100">{{ $upcomingAppointment->provider->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-zinc-400">
                                {{ __('Reach out to your clinic provider if you need to request a follow-up or routine evaluation.') }}
                                <a href="{{ route('portal.appointments') }}" class="text-primary hover:underline font-semibold ml-1">{{ __('Schedule an appointment') }} &rarr;</a>
                            </p>
                        @endif
                    </div>

                    <!-- 2. Care Team -->
                    <div class="col-span-12 lg:col-span-4 widget-card rounded-2xl p-6 bg-white dark:bg-zinc-900">
                        <flux:heading size="lg" class="mb-4">{{ __('Primary Care Team') }}</flux:heading>
                        
                        @if ($patient && $patient->primaryProvider)
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-full bg-growth-sage/10 text-growth-sage flex items-center justify-center text-base font-bold">
                                    {{ strtoupper(substr($patient->primaryProvider->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-trust-navy dark:text-zinc-100">{{ $patient->primaryProvider->name }}</h4>
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ __('NPI: :npi', ['npi' => $patient->primaryProvider->npi_number ?? '1234567890']) }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-zinc-400 italic">{{ __('No primary provider assigned.') }}</p>
                        @endif
                    </div>

                    <!-- 3. Completed Assessments -->
                    <div class="col-span-12 lg:col-span-6 widget-card rounded-2xl p-6 bg-white dark:bg-zinc-900">
                        <flux:heading size="lg" class="mb-4">{{ __('Completed Assessments') }}</flux:heading>
                        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse ($completedAssessments as $assessment)
                                <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100">
                                            {{ strtoupper($assessment->instrument) }} {{ __('Screening') }}
                                        </p>
                                        <p class="text-xs text-zinc-400 mt-0.5">
                                            {{ $assessment->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-trust-navy dark:text-zinc-200">
                                            {{ __('Score: :score', ['score' => $assessment->score]) }}
                                        </span>
                                        <flux:badge size="sm" color="zinc">
                                            {{ $assessment->severity ?: __('Completed') }}
                                        </flux:badge>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400 py-4 italic text-center">{{ __('No assessments completed yet.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- 4. Care Resources & Guides -->
                    <div class="col-span-12 lg:col-span-6 widget-card rounded-2xl p-6 bg-white dark:bg-zinc-900">
                        <flux:heading size="lg" class="mb-4">{{ __('Educational Guides & Teaching Logs') }}</flux:heading>
                        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @if ($patient && $patient->medicationTeachingLogs && $patient->medicationTeachingLogs->isNotEmpty())
                                @foreach ($patient->medicationTeachingLogs as $log)
                                    <div class="flex items-start justify-between py-3 first:pt-0 last:pb-0">
                                        <div class="flex items-start gap-2.5">
                                            <flux:icon.document-text class="size-5 text-growth-sage shrink-0 mt-0.5" />
                                            <div>
                                                <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100">
                                                    {{ $log->medication_name }} - Lexicomp Information Sheet
                                                </p>
                                                <p class="text-xs text-zinc-400 mt-0.5">
                                                    {{ __('Assigned on: :date', ['date' => $log->assigned_at->format('M j, Y')]) }}
                                                </p>
                                            </div>
                                        </div>
                                        <flux:button variant="ghost" size="sm" class="text-primary">
                                            {{ __('View PDF') }}
                                        </flux:button>
                                    </div>
                                @endforeach
                            @else
                                <div class="py-6 text-center">
                                    <flux:icon.book-open class="size-8 text-zinc-300 mx-auto mb-2" />
                                    <p class="text-xs text-zinc-400">{{ __('No educational material has been assigned yet.') }}</p>
                                </div>
                            @endif
                    </div>

                    <!-- 5. Recent Invoices -->
                    <div class="col-span-12 widget-card rounded-2xl p-6 bg-white dark:bg-zinc-900 mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <flux:heading size="lg">{{ __('Recent Invoices') }}</flux:heading>
                            <a href="{{ route('portal.billing') }}" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                                {{ __('All Invoices') }} &rarr;
                            </a>
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse ($invoices as $inv)
                                <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100 font-mono">
                                            #INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p class="text-xs text-zinc-400 mt-0.5">
                                            {{ __('Due Date: :date', ['date' => $inv->due_date->format('M j, Y')]) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-sm text-zinc-950 dark:text-zinc-100">
                                            ${{ number_format($inv->total_amount, 2) }}
                                        </span>
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-zinc-100 text-zinc-800',
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'void' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$inv->status] ?? 'bg-zinc-105 text-zinc-800';
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $color }}">
                                            {{ __(ucfirst($inv->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400 py-4 italic text-center">{{ __('No billing statements found.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic">
                    {{ __('No patient records found in EMR. Create a patient first to load portal view.') }}
                </div>
            @endif
        </main>
    </div>
</div>
