<div class="space-y-8">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Order Entry and Pending Orders Column -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Order Laboratory Test') }}</flux:heading>

                <form wire:submit="orderLabs" class="space-y-4">
                    <flux:select wire:model="panel" :label="__('Select Test Panel')">
                        <flux:select.option value="Thyroid Panel">{{ __('Thyroid Panel (TSH, Free T4)') }}</flux:select.option>
                        <flux:select.option value="CBC">{{ __('CBC (WBC, RBC, Platelets)') }}</flux:select.option>
                        <flux:select.option value="Lithium Level">{{ __('Lithium Monitoring Panel') }}</flux:select.option>
                    </flux:select>

                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Generate Requisition PDF') }}
                    </flux:button>
                </form>
            </div>

            <!-- Outstanding Orders list (with HL7 simulation trigger) -->
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Pending Lab Orders') }}</flux:heading>

                <div class="space-y-3">
                    @forelse ($patient->labOrders->where('status', 'ordered') as $order)
                        <div class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-neutral-50 dark:bg-zinc-800 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $order->panel }}</div>
                                    <div class="text-xs text-zinc-500">{{ __('Ordered: ') }}{{ $order->created_at->format('M j, Y') }}</div>
                                </div>
                                <span class="rounded bg-amber-50 dark:bg-amber-950/20 px-1.5 py-0.5 text-[10px] font-medium text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                                    {{ __('Ordered') }}
                                </span>
                            </div>

                            <flux:button variant="ghost" size="sm" class="w-full" wire:click="simulateQuestResult({{ $order->id }})">
                                {{ __('Simulate Quest HL7 Delivery') }}
                            </flux:button>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 py-4 text-center">{{ __('No outstanding lab orders.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Lab Results list with Deltas and Trending Charts -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Lab result trending chart (Chart.js) -->
            @if ($patient->labResults->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-4">{{ __('Lab Value Trending') }}</flux:heading>
                    
                    <div class="border border-zinc-100 p-4 rounded-lg dark:border-zinc-800 bg-white dark:bg-zinc-950">
                        <div x-data="{
                            init() {
                                const ctx = document.getElementById('labs-trend-chart')?.getContext('2d');
                                if (!ctx) return;
                                
                                // Extract TSH test values
                                const dataPoints = [];
                                @foreach ($patient->labResults as $result)
                                    @foreach ($result->result_data as $obs)
                                        @if ($obs['name'] === 'TSH')
                                            dataPoints.push({
                                                date: '{{ $result->created_at->format('M j') }}',
                                                val: {{ (float)$obs['value'] }}
                                            });
                                        @endif
                                    @endforeach
                                @endforeach

                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: dataPoints.map(d => d.date),
                                        datasets: [{
                                            label: 'TSH (Thyroid Stimulating Hormone) (Ref: 0.40 - 4.00 uIU/mL)',
                                            data: dataPoints.map(d => d.val),
                                            borderColor: '#9333ea',
                                            backgroundColor: '#9333ea',
                                            tension: 0.1,
                                            fill: false
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            }
                        }" wire:ignore class="h-40">
                            <canvas id="labs-trend-chart"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Results details list -->
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900 space-y-6">
                <flux:heading size="lg">{{ __('Laboratory Observations') }}</flux:heading>

                <div class="space-y-6">
                    @forelse ($patient->labResults->sortByDesc('created_at') as $result)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden bg-white dark:bg-zinc-900">
                            <!-- Panel Header -->
                            <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                                <div>
                                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ $result->labOrder->panel }}</span>
                                    <span class="text-xs text-zinc-500 ml-2">{{ $result->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                @if ($result->is_critical)
                                    <flux:badge color="red" size="sm" class="animate-pulse">{{ __('CRITICAL') }}</flux:badge>
                                @endif
                            </div>

                            <!-- Observations Table -->
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-left text-xs font-sans">
                                <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 text-zinc-500 font-semibold">
                                    <tr>
                                        <th class="px-4 py-2">{{ __('Observation') }}</th>
                                        <th class="px-4 py-2">{{ __('Value') }}</th>
                                        <th class="px-4 py-2">{{ __('Reference Range') }}</th>
                                        <th class="px-4 py-2">{{ __('Delta (Prior)') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach ($result->result_data as $obs)
                                        @php
                                            $isAbnormal = in_array($obs['flag'], ['H', 'L', 'HH', 'LL']);
                                            $prior = $this->getPriorValue($result->labOrder->panel, $obs['name'], $result->id);
                                            $delta = '';
                                            if ($prior) {
                                                $diff = (float)$obs['value'] - (float)$prior['value'];
                                                if ($diff > 0) {
                                                    $delta = '(↑ +' . number_format($diff, 2) . ')';
                                                } elseif ($diff < 0) {
                                                    $delta = '(↓ ' . number_format($diff, 2) . ')';
                                                } else {
                                                    $delta = '(no change)';
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $isAbnormal ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}">
                                            <td class="px-4 py-2.5 font-medium text-zinc-800 dark:text-zinc-200">{{ $obs['name'] }}</td>
                                            <td class="px-4 py-2.5">
                                                <span class="{{ $isAbnormal ? 'text-red-600 dark:text-red-400 font-bold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                    {{ $obs['value'] }} {{ $obs['unit'] }}
                                                    @if ($isAbnormal)
                                                        [{{ $obs['flag'] }}]
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 text-zinc-500">{{ $obs['range'] }}</td>
                                            <td class="px-4 py-2.5 text-zinc-500 font-mono">{{ $delta ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 py-8 text-center">{{ __('No laboratory results received yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
