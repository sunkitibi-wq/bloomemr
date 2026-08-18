<div class="flex flex-col gap-6 max-w-[1600px] w-full">
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Welcome back, {{ auth('portal')->user()->name ?? auth()->user()->name }} 👋</h1>
                <p class="text-[13px] text-slate-500 mt-1">Here is a shared view of your health records and care logs.</p>
            </div>
            
            <div class="shrink-0">
                <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <flux:icon.calendar class="w-4 h-4 text-slate-400" />
                    {{ now()->format('M d, Y') }}
                    <flux:icon.chevron-down class="w-4 h-4 text-slate-400 ml-1" />
                </button>
            </div>
        </div>

        @if ($patient)
            <!-- Content Row 1: Next Appointment & Care Team -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Upcoming Appointment (2 cols) -->
                <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div>
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-md uppercase tracking-wider inline-block">
                                {{ __('Next Scheduled Appointment') }}
                            </span>
                            <h3 class="text-[18px] text-slate-800 mt-3 font-bold">
                                {{ $upcomingAppointment ? __('Consultation Session') : __('No Upcoming Appointments Scheduled') }}
                            </h3>
                        </div>
                        @if ($upcomingAppointment)
                            <a href="{{ route('portal.appointments') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 bg-white">
                                {{ __('Manage') }} <flux:icon.arrow-top-right-on-square class="w-3.5 h-3.5" />
                            </a>
                        @endif
                    </div>

                    @if ($upcomingAppointment)
                        <div class="flex flex-wrap items-center gap-8 relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-slate-700">
                                    <span class="text-[10px] font-bold uppercase text-slate-400">{{ $upcomingAppointment->appointment_date->format('M') }}</span>
                                    <span class="text-xl font-bold leading-none mt-0.5">{{ $upcomingAppointment->appointment_date->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-[13px] text-slate-500 font-medium">{{ $upcomingAppointment->appointment_date->format('l') }}, {{ \Carbon\Carbon::parse($upcomingAppointment->start_time)->format('g:i A') }}</p>
                                    <p class="text-[14px] font-bold text-slate-800 mt-0.5">{{ __('In-Person / Telehealth visit') }}</p>
                                </div>
                            </div>
                            <div class="w-px h-10 bg-slate-100 hidden sm:block"></div>
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[12px] font-bold border-2 border-white shadow-sm">
                                    {{ strtoupper(substr($upcomingAppointment->provider->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">{{ __('Assigned Clinician') }}</p>
                                    <p class="text-[13px] font-bold text-slate-800">{{ $upcomingAppointment->provider->name }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative background element -->
                        <div class="absolute -right-6 -bottom-6 text-slate-50">
                            <flux:icon.calendar class="w-40 h-40" variant="solid" />
                        </div>
                    @else
                        <div class="flex-1 flex flex-col justify-center relative z-10">
                            <p class="text-[13px] text-slate-500 mb-3">
                                {{ __('Reach out to your clinic provider if you need to request a follow-up or routine evaluation.') }}
                            </p>
                            <a href="{{ route('portal.appointments') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-bold transition-colors w-fit shadow-sm">
                                <flux:icon.plus class="w-4 h-4" />
                                {{ __('Schedule an appointment') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Primary Care Team (1 col) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
                    <h3 class="font-bold text-[14px] text-slate-800 mb-5">{{ __('Primary Care Team') }}</h3>
                    
                    @if ($patient && $patient->primaryProvider)
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-[14px] font-bold shrink-0">
                                {{ strtoupper(substr($patient->primaryProvider->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-[14px] font-bold text-slate-800">{{ $patient->primaryProvider->name }}</h4>
                                <p class="text-[12px] text-slate-500 mt-1 font-medium">{{ __('Primary Care Physician') }}</p>
                                <p class="text-[11px] text-slate-400 mt-2 font-mono bg-slate-50 px-2 py-1 rounded w-fit">{{ __('NPI: :npi', ['npi' => $patient->primaryProvider->npi_number ?? '1234567890']) }}</p>
                            </div>
                        </div>
                        <div class="mt-auto pt-5">
                            <button class="w-full py-2.5 px-4 bg-white border border-slate-200 text-slate-700 text-[13px] font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                                Send Message
                            </button>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                <flux:icon.user class="w-6 h-6 text-slate-300" variant="solid" />
                            </div>
                            <p class="text-[13px] text-slate-400 italic">{{ __('No primary provider assigned.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Row 2: Assessments & Guides -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <!-- Completed Assessments -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Completed Assessments') }}</h3>
                        <a href="{{ route('portal.assessments') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        @forelse ($completedAssessments as $assessment)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-sm">
                                        <flux:icon.clipboard-document-check class="w-5 h-5 text-indigo-500" variant="outline" />
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-bold text-slate-800">
                                            {{ strtoupper($assessment->instrument) }} {{ __('Screening') }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium">
                                            {{ $assessment->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="text-[13px] font-bold text-slate-700">
                                        {{ __('Score: :score', ['score' => $assessment->score]) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold">
                                        {{ $assessment->severity ?: __('Completed') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                                <p class="text-[13px] text-slate-400 italic">{{ __('No assessments completed yet.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Care Resources & Guides -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Educational Guides & Teaching Logs') }}</h3>
                        <a href="{{ route('portal.education') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        @if ($patient && $patient->medicationTeachingLogs && $patient->medicationTeachingLogs->isNotEmpty())
                            @foreach ($patient->medicationTeachingLogs as $log)
                                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5">
                                            <flux:icon.document-text class="w-5 h-5 text-emerald-500" variant="outline" />
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                                {{ $log->medication_name }} - Lexicomp Info
                                            </p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 font-medium">
                                                {{ __('Assigned on: :date', ['date' => $log->assigned_at->format('M j, Y')]) }}
                                            </p>
                                        </div>
                                    </div>
                                    <button class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors shrink-0">
                                        <flux:icon.arrow-down-tray class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50 h-full">
                                <flux:icon.book-open class="w-8 h-8 text-slate-300 mb-3" variant="outline" />
                                <p class="text-[13px] text-slate-400">{{ __('No educational material has been assigned yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content Row 3: Invoices -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden pb-4">
                <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
                    <h3 class="font-bold text-[14px] text-slate-800">{{ __('Recent Invoices') }}</h3>
                    <a href="{{ route('portal.billing') }}" class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">All Invoices</a>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-[13px] whitespace-nowrap">
                        <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 font-medium">Invoice #</th>
                                <th class="px-6 py-3 font-medium">Due Date</th>
                                <th class="px-6 py-3 font-medium text-right">Amount</th>
                                <th class="px-6 py-3 font-medium text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($invoices as $inv)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-medium text-slate-600">
                                        #INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ $inv->due_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-slate-800">${{ number_format($inv->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-slate-100 text-slate-700',
                                                'pending' => 'bg-orange-100 text-orange-700',
                                                'paid' => 'bg-emerald-100 text-emerald-700',
                                                'void' => 'bg-red-100 text-red-700',
                                            ];
                                            $color = $statusColors[$inv->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full {{ $color }}">
                                            {{ __(ucfirst($inv->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-[13px] italic">No billing statements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        @else
            <!-- Empty State -->
            <div class="flex-1 flex flex-col items-center justify-center p-12 bg-white rounded-2xl border border-dashed border-slate-300">
                <flux:icon.user-circle class="w-12 h-12 text-slate-300 mb-4" variant="outline" />
                <h3 class="text-[16px] font-bold text-slate-800 mb-1">No Patient Records Found</h3>
                <p class="text-[14px] text-slate-500 text-center max-w-md">
                    {{ __('Your portal account is not yet linked to any patient records in the EMR. Please contact your clinic administrator.') }}
                </p>
            </div>
        @endif
        
    </div>
