<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Order Entry Form -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-zinc-900 space-y-4">
                <flux:heading size="lg">{{ __('Request Radiology Imaging') }}</flux:heading>
                <flux:subheading>{{ __('Order X-rays, MRI, CT scans, or ultrasounds.') }}</flux:subheading>
                
                <form wire:submit.prevent="placeOrder" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Procedure Name') }}</flux:label>
                        <flux:input wire:model="procedureName" placeholder="e.g. MRI Brain without contrast" required />
                        @error('procedureName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Clinical Indication') }}</flux:label>
                        <flux:textarea wire:model="clinicalIndication" placeholder="e.g. Chronic headaches, rule out structural lesion" rows="3" required />
                        @error('clinicalIndication') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">
                        {{ __('Place Imaging Order') }}
                    </flux:button>
                </form>
            </div>
        </div>

        <!-- Orders & Reports List -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-zinc-900 space-y-4">
                <flux:heading size="lg">{{ __('Imaging Orders & Reports') }}</flux:heading>

                <div class="border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">{{ __('Procedure') }}</th>
                                <th class="px-6 py-3.5">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5">{{ __('Ordered By') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="ro-{{ $order->id }}">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $order->procedure_name }}</div>
                                        <div class="text-xs text-zinc-400 mt-0.5">{{ __('Indication: :ind', ['ind' => $order->clinical_indication]) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $colors = [
                                                'ordered' => 'amber',
                                                'reported' => 'green',
                                                'completed' => 'blue',
                                                'cancelled' => 'red',
                                            ];
                                            $color = $colors[$order->status] ?? 'zinc';
                                        @endphp
                                        <flux:badge size="sm" color="{{ $color }}">
                                            {{ ucfirst($order->status) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-400">
                                        <div>{{ $order->orderedBy->name }}</div>
                                        <div class="mt-0.5">{{ $order->order_date->format('M j, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        @if ($order->status === 'ordered')
                                            <flux:button size="sm" variant="ghost" class="text-primary hover:text-primary-hover" wire:click="openReportModal({{ $order->id }})">
                                                {{ __('Upload Report') }}
                                            </flux:button>
                                        @elseif ($order->status === 'reported' && $order->report)
                                            <flux:button size="sm" variant="ghost" wire:click="viewReport({{ $order->report->id }})">
                                                {{ __('View Report') }}
                                            </flux:button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-zinc-400 italic text-sm">
                                        {{ __('No radiology orders on file.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Report Modal -->
    <div x-data="{ open: @entangle('showReportModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                <div>
                    <flux:heading size="lg">{{ __('Submit Radiology Report') }}</flux:heading>
                    @if ($selectedOrder)
                        <flux:subheading>{{ __('Publish findings and impressions for :proc', ['proc' => $selectedOrder->procedure_name]) }}</flux:subheading>
                    @endif
                </div>

                <form wire:submit.prevent="submitReport" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Findings') }}</flux:label>
                        <flux:textarea wire:model="findings" placeholder="{{ __('Describe what is seen on the scan images...') }}" rows="4" required />
                        @error('findings') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Impression') }}</flux:label>
                        <flux:textarea wire:model="impression" placeholder="{{ __('Main diagnostic takeaways/summary...') }}" rows="3" required />
                        @error('impression') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Scan Attachment (PDF / Image)') }}</flux:label>
                        <input type="file" wire:model="scanFile" class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-50 file:text-zinc-700 hover:file:bg-zinc-100" />
                        @error('scanFile') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <flux:button type="button" wire:click="$set('showReportModal', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary">{{ __('Publish Report') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Report Modal -->
    <div x-data="{ open: @entangle('showViewModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                @if ($selectedReport)
                    <div>
                        <flux:heading size="lg">{{ __('Radiology Diagnostics Report') }}</flux:heading>
                        <flux:subheading class="font-mono mt-0.5">{{ $selectedReport->order->procedure_name }}</flux:subheading>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <div>
                            <span class="text-zinc-400 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Ordered By') }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedReport->order->orderedBy->name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Interpreted By') }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedReport->radiologist->name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Order Date') }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedReport->order->order_date->format('M j, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Report Date') }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedReport->reported_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ __('Findings') }}</h4>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400 whitespace-pre-line leading-relaxed">{{ $selectedReport->findings }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ __('Impression') }}</h4>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400 whitespace-pre-line leading-relaxed font-semibold">{{ $selectedReport->impression }}</p>
                        </div>

                        @if ($selectedReport->attachment_path)
                            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                <flux:button size="sm" variant="outline" href="{{ \Illuminate\Support\Facades\Storage::url($selectedReport->attachment_path) }}" target="_blank">
                                    <flux:icon.arrow-down-tray class="size-4 mr-1.5" />
                                    {{ __('View Scan Image/Document') }}
                                </flux:button>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="button" wire:click="$set('showViewModal', false)">{{ __('Close') }}</flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
