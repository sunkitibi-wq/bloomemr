<div class="flex flex-col gap-6 max-w-[1600px] w-full">
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Welcome back, {{ auth()->user()->name }} 👋</h1>
                <p class="text-[13px] text-slate-500 mt-1">Here's what's happening today.</p>
            </div>
            
            <div class="shrink-0">
                <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <flux:icon.calendar class="w-4 h-4 text-slate-400" />
                    {{ now()->format('M d, Y') }}
                    <flux:icon.chevron-down class="w-4 h-4 text-slate-400 ml-1" />
                </button>
            </div>
        </div>

        <!-- Clinical Alerts (from existing logic) -->
        @if ($this->clinicalAlerts->isNotEmpty())
            <div class="space-y-3">
                @foreach ($this->clinicalAlerts as $alert)
                    <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-xl border border-red-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <flux:icon.exclamation-triangle class="size-5 text-red-500 shrink-0" />
                            <span class="text-[13px] font-medium text-red-800">{{ $alert['message'] }}</span>
                        </div>
                        <flux:button variant="ghost" size="sm" href="{{ $alert['action_url'] }}" wire:navigate class="text-red-600 hover:text-red-700">
                            {{ __('Resolve') }}
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <!-- Total Patients -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Total Patients</h3>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($this->totalPatients) }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                        <flux:icon.user-group class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-emerald-600 gap-0.5"><flux:icon.arrow-up class="w-3 h-3" /> Active</span> system total
                </div>
            </div>

            <!-- Appointments -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Today's Encounters</h3>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($this->todayCount) }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <flux:icon.calendar-days class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-slate-500 gap-0.5">Scheduled for today</span>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Monthly Revenue</h3>
                        <div class="text-2xl font-bold text-slate-800">${{ number_format($this->monthlyRevenue, 2) }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <flux:icon.banknotes class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-emerald-600 gap-0.5">Paid this month</span>
                </div>
            </div>

            <!-- Outstanding Bills (Unpaid Invoices) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Open Invoices</h3>
                        <div class="text-2xl font-bold text-slate-800">{{ $this->unpaidInvoices }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                        <flux:icon.document-text class="w-5 h-5" variant="solid" />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center text-red-500 gap-0.5">Pending payment</span>
                </div>
            </div>
        </div>

        <!-- Content Row 1: Recent Patients & Today's Schedule -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- Recent Patients (2 cols) -->
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100 bg-white">
                    <h3 class="font-bold text-[14px] text-slate-800">Recent Patients / Customers</h3>
                    <a href="{{ route('patients.index') }}" wire:navigate class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-[13px] whitespace-nowrap">
                        <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-white">
                            <tr>
                                <th class="px-5 py-3 font-medium">Patient ID</th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Primary Provider</th>
                                <th class="px-5 py-3 font-medium">Created</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($this->recentPatients as $patient)
                                @php
                                    $initials = strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1));
                                    $colors = ['purple', 'blue', 'emerald', 'orange', 'amber'];
                                    $color = $colors[crc32($patient->id) % count($colors)];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5 text-slate-500 font-medium font-mono">{{ $patient->mrn }}</td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('patients.show', $patient) }}" wire:navigate class="flex items-center gap-3 group">
                                            @if ($patient->photo_path)
                                                <img src="{{ Storage::url($patient->photo_path) }}" alt="" class="w-7 h-7 rounded-full object-cover" />
                                            @else
                                                <div class="w-7 h-7 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 flex items-center justify-center text-[10px] font-bold">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $patient->full_name }}</span>
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $patient->primaryProvider?->name ?? 'Unassigned' }}</td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $patient->created_at->format('M j, Y') }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[12px] font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-[13px] italic">No recent patients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-[14px] text-slate-800">Today's Schedule</h3>
                    <a href="{{ route('scheduling') }}" wire:navigate class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                
                <div class="flex flex-col gap-4">
                    @forelse($this->todayAppointments as $appt)
                        @php
                            $colors = ['purple', 'blue', 'emerald', 'amber', 'slate'];
                            $color = $colors[crc32($appt->id) % count($colors)];
                        @endphp
                        <div class="flex gap-4">
                            <div class="w-16 text-right shrink-0">
                                <span class="text-[13px] font-bold text-slate-800">{{ \Carbon\Carbon::parse($appt->start_time)->format('h:i A') }}</span>
                            </div>
                            <div class="flex-1 flex items-start justify-between pb-4 border-l-2 border-{{ $color }}-200 pl-4 relative">
                                <div class="absolute w-2 h-2 bg-{{ $color }}-500 rounded-full -left-[5px] top-1.5"></div>
                                <div>
                                    <div class="text-[13px] font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</div>
                                    <a href="{{ route('patients.show', $appt->patient) }}" wire:navigate class="text-[12px] text-slate-500 mt-0.5 hover:text-indigo-600 hover:underline">
                                        {{ $appt->patient->full_name }}
                                    </a>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-{{ $color }}-50 text-{{ $color }}-600 text-[10px] font-bold tracking-wide">
                                    {{ explode(' ', $appt->provider->name)[0] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 text-[13px] italic py-8">No appointments scheduled for today.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Content Row 2: Charts & Tasks -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 pb-6">
            
            <!-- Revenue Overview (Placeholder) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Revenue Overview</h3>
                    <select wire:model.live="revenuePeriod" class="text-[11px] font-medium text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm px-3 py-1.5 pr-8 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 hover:bg-slate-50 transition-colors cursor-pointer appearance-none">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                    </select>
                </div>
                <div class="flex-1 min-h-[200px] relative overflow-hidden rounded-lg bg-gradient-to-t from-indigo-50/50 to-transparent p-2 mt-4"
                    x-data="{
                        chart: null,
                        init() {
                            let ctx = this.$refs.canvas.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: @js($this->revenueChartData['labels']),
                                    datasets: [{
                                        data: @js($this->revenueChartData['data']),
                                        borderColor: '#4f46e5',
                                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 2,
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: '#4f46e5',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        x: { display: false },
                                        y: { display: false, min: 0 }
                                    }
                                }
                            });
                            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                                succeed(({ snapshot, effect }) => {
                                    if (component.name === 'dashboard') {
                                        this.chart.data.labels = component.ephemeral.revenueChartData.labels;
                                        this.chart.data.datasets[0].data = component.ephemeral.revenueChartData.data;
                                        this.chart.update();
                                    }
                                });
                            });
                        }
                    }"
                >
                    <canvas x-ref="canvas" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Department Stats (Placeholder) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Patient Demographics</h3>
                </div>
                <div class="flex-1 flex items-center justify-center gap-8"
                    x-data="{
                        chart: null,
                        init() {
                            let ctx = this.$refs.canvas.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: @js(collect($this->demographicStats)->pluck('label')),
                                    datasets: [{
                                        data: @js(collect($this->demographicStats)->pluck('count')),
                                        backgroundColor: @js(collect($this->demographicStats)->pluck('color')),
                                        borderWidth: 0,
                                        cutout: '75%',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: { enabled: true } }
                                }
                            });
                        }
                    }"
                >
                    <div class="relative w-40 h-40 shrink-0">
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <flux:icon.users class="w-6 h-6 text-slate-400" />
                        </div>
                    </div>
                    
                    <!-- Legend -->
                    <div class="flex flex-col gap-3 w-full max-w-[140px]">
                        @forelse($this->demographicStats as $stat)
                        <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full" style="background-color: {{ $stat['color'] }}"></span> {{ $stat['label'] }}</div>
                            <span class="font-bold text-slate-800">{{ $stat['percentage'] }}%</span>
                        </div>
                        @empty
                        <div class="text-[11px] font-medium text-slate-400">No data available</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Clinical Notes Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Clinical Notes</h3>
                    <a href="{{ route('encounters.index') }}" wire:navigate class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                
                <div class="flex flex-col gap-5 flex-1 justify-center pl-2">
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-orange-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#f97316" stroke-width="4" stroke-dasharray="60, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.clock class="w-4 h-4 text-orange-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">{{ $this->pendingCount }}</div>
                            <div class="text-[12px] font-medium text-slate-500">Pending Notes</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-emerald-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#10b981" stroke-width="4" stroke-dasharray="100, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.check-circle class="w-4 h-4 text-emerald-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">{{ $this->thisMonthEncounters }}</div>
                            <div class="text-[12px] font-medium text-slate-500">Encounters this Month</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[14px] text-slate-800">Tasks Overview</h3>
                    <a href="{{ route('tasks.index') }}" wire:navigate class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                
                <div class="flex flex-col gap-5 flex-1 justify-center pl-2">
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-blue-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#3b82f6" stroke-width="4" stroke-dasharray="60, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.list-bullet class="w-4 h-4 text-blue-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">{{ $this->pendingTasksCount }}</div>
                            <div class="text-[12px] font-medium text-slate-500">Active Tasks</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5">
                        <div class="relative w-10 h-10 flex items-center justify-center">
                            <svg viewBox="0 0 36 36" class="w-full h-full absolute inset-0 text-indigo-100 transform -rotate-90">
                                <path stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke="#6366f1" stroke-width="4" stroke-dasharray="100, 100" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <flux:icon.check-circle class="w-4 h-4 text-indigo-500 relative z-10" variant="outline" />
                        </div>
                        <div>
                            <div class="text-[15px] font-bold text-slate-800 leading-none mb-1.5">{{ $this->completedTasksCount }}</div>
                            <div class="text-[12px] font-medium text-slate-500">Completed Tasks</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
