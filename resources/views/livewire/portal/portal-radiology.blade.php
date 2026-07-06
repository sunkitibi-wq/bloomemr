<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar Navigation -->
    <nav class="w-full md:w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col p-6 shrink-0">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">
                B
            </div>
            <div>
                <span class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 tracking-tight text-base">Bloom</span>
                <span class="text-[9px] text-zinc-400 block -mt-1 uppercase tracking-wider">{{ __('Patient Portal') }}</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.dashboard') }}">
                <span class="material-symbols-outlined text-xl">home</span>
                <span class="text-sm">{{ __('Home') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.appointments') }}">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                <span class="text-sm">{{ __('Appointments') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.billing') }}">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-sm">{{ __('Billing & Invoices') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.messages') }}">
                <span class="material-symbols-outlined text-xl">mail</span>
                <span class="text-sm">{{ __('Secure Messages') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.refills') }}">
                <span class="material-symbols-outlined text-xl">vaccines</span>
                <span class="text-sm">{{ __('Refill Requests') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.telehealth') }}">
                <span class="material-symbols-outlined text-xl">videocam</span>
                <span class="text-sm">{{ __('Telehealth Room') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.forms') }}">
                <span class="material-symbols-outlined text-xl">description</span>
                <span class="text-sm">{{ __('Intake & Consents') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.care-coordination') }}">
                <span class="material-symbols-outlined text-xl">share</span>
                <span class="text-sm">{{ __('Care Sharing Log') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.radiology') }}">
                <span class="material-symbols-outlined text-xl">biotech</span>
                <span class="text-sm">{{ __('Imaging Reports') }}</span>
            </a>
        </div>

        <!-- CTA & Exit -->
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-zinc-800 space-y-3">
            <button class="w-full py-2.5 px-4 bg-status-critical text-white text-xs font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">emergency</span>
                <span>{{ __('Emergency Contact') }}</span>
            </button>
            
            <a class="flex items-center gap-3 px-4 py-2 text-zinc-500 dark:text-zinc-400 hover:text-trust-navy dark:hover:text-zinc-100 transition-colors" href="{{ route('dashboard') }}" wire:navigate>
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                <span class="text-xs">{{ __('Back to EMR') }}</span>
            </a>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-8 flex flex-col overflow-y-auto">
        <div class="max-w-6xl w-full mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold font-heading text-trust-navy dark:text-zinc-100">{{ __('Radiology & Imaging Reports') }}</h1>
                    <p class="text-sm text-zinc-500 mt-1">{{ __('Access diagnostic scan results, findings, and interpretation reports.') }}</p>
                </div>
            </div>

            <!-- Disclaimer alert box -->
            <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800 rounded-2xl flex gap-3 text-sm text-blue-700 dark:text-blue-300">
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
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-slate-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Procedure Name') }}</th>
                                    <th class="px-6 py-4">{{ __('Ordered By') }}</th>
                                    <th class="px-6 py-4">{{ __('Order Date') }}</th>
                                    <th class="px-6 py-4">{{ __('Report Date') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-zinc-850">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $order->procedure_name }}</span>
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
                                                <span class="italic text-xs text-zinc-400">{{ __('Processing...') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400 italic">
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
    </main>

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
