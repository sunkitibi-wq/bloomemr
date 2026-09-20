<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Radiology & Imaging Reports') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Access diagnostic scan results, findings, and interpretation reports.') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1 space-y-6">

            <!-- Disclaimer alert box -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex gap-3 text-sm text-blue-700 ">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">info</span>
                <div>
                    <p class="font-semibold">{{ __('Accessing Your Diagnostics') }}</p>
                    <p class="mt-1 text-xs opacity-90 leading-relaxed">
                        {{ __('These reports detail diagnostic scans ordered by your clinicians and interpreted by Board Certified radiologists. If you have questions about these results, please send a message to your clinical team.') }}
                    </p>
                </div>
            </div>

            <!-- Reported Scans List -->
            @if ($patient)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-500 ">
                            <thead class="bg-slate-50 text-xs text-slate-700 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Procedure Name') }}</th>
                                    <th class="px-6 py-4">{{ __('Ordered By') }}</th>
                                    <th class="px-6 py-4">{{ __('Order Date') }}</th>
                                    <th class="px-6 py-4">{{ __('Report Date') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 ">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-slate-50/50 :bg-slate-100/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-900 ">{{ $order->procedure_name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            {{ $order->orderedBy->name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono">
                                            {{ $order->order_date->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono">
                                            {{ $order->report ? $order->report->reported_at->format('M j, Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if ($order->report)
                                                <flux:button size="sm" variant="ghost" wire:click="viewReport({{ $order->report->id }})">
                                                    {{ __('View Report') }}
                                                </flux:button>
                                            @else
                                                <span class="italic text-xs text-slate-500">{{ __('Processing...') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                                            {{ __('No imaging reports available yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    <!-- View Report Modal -->
    <div x-data="{ open: @entangle('showViewModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl bg-white rounded-xl border border-slate-200 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                @if ($selectedReport)
                    <div>
                        <flux:heading size="lg">{{ __('Radiology Diagnostics Report') }}</flux:heading>
                        <flux:subheading class="font-mono mt-0.5">{{ $selectedReport->order->procedure_name }}</flux:subheading>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-3 rounded-lg border border-slate-100 ">
                        <div>
                            <span class="text-slate-500 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Ordered By') }}</span>
                            <span class="font-medium text-slate-900 ">{{ $selectedReport->order->orderedBy->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Interpreted By') }}</span>
                            <span class="font-medium text-slate-900 ">{{ $selectedReport->radiologist->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Order Date') }}</span>
                            <span class="font-medium text-slate-900 ">{{ $selectedReport->order->order_date->format('M j, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-semibold uppercase tracking-wider block mb-0.5">{{ __('Report Date') }}</span>
                            <span class="font-medium text-slate-900 ">{{ $selectedReport->reported_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 ">{{ __('Findings') }}</h4>
                            <p class="mt-1 text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $selectedReport->findings }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-800 ">{{ __('Impression') }}</h4>
                            <p class="mt-1 text-sm text-slate-600 whitespace-pre-line leading-relaxed font-semibold">{{ $selectedReport->impression }}</p>
                        </div>

                        @if ($selectedReport->attachment_path)
                            <div class="pt-4 border-t border-slate-100 ">
                                <flux:button size="sm" variant="outline" href="{{ \Illuminate\Support\Facades\Storage::url($selectedReport->attachment_path) }}" target="_blank">
                                    <flux:icon.arrow-down-tray class="size-4 mr-1.5" />
                                    {{ __('View Scan Image/Document') }}
                                </flux:button>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100 ">
                        <flux:button type="button" wire:click="$set('showViewModal', false)">{{ __('Close') }}</flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
