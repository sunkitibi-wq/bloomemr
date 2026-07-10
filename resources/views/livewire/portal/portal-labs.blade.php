<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <x-portal.sidebar />

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Lab Results') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('Review your laboratory test results and get AI explanations') }}
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
        <main class="flex-1 p-6 md:p-8 overflow-y-auto flex flex-col">
            @if ($patient)
                <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden min-h-[500px]">
                    <!-- Left: Labs List -->
                    <div class="lg:col-span-1 border-r border-zinc-200 dark:border-zinc-800 flex flex-col">
                        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                            <flux:heading size="sm">{{ __('Recent Labs') }}</flux:heading>
                        </div>
                        <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($labs as $lab)
                                @php
                                    $isActive = $selectedLab && $selectedLab->id === $lab->id;
                                @endphp
                                <button wire:click="selectLab({{ $lab->id }})" 
                                        class="w-full text-left p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/20 flex flex-col gap-1 transition-colors {{ $isActive ? 'bg-slate-50 dark:bg-zinc-800/40' : '' }}">
                                    <div class="flex justify-between items-center w-full">
                                        <span class="text-sm font-semibold truncate text-zinc-800 dark:text-zinc-100">
                                            {{ $lab->labOrder->panel ?? 'General Lab' }}
                                        </span>
                                        <span class="text-[10px] text-zinc-400">
                                            {{ $lab->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-400">
                                        {{ __('Status: ') }} {{ ucfirst($lab->status) }}
                                    </p>
                                </button>
                            @empty
                                <p class="text-xs text-zinc-400 py-8 italic text-center">{{ __('No lab results found.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right: Lab Detail -->
                    <div class="lg:col-span-2 flex flex-col bg-zinc-50/50 dark:bg-zinc-900/20">
                        @if ($selectedLab)
                            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex justify-between items-center">
                                <div>
                                    <flux:heading size="lg">{{ $selectedLab->labOrder->panel ?? 'Lab Result' }}</flux:heading>
                                    <p class="text-xs text-zinc-450 mt-1">
                                        {{ __('Result received on: :date', ['date' => $selectedLab->created_at->format('F j, Y')]) }}
                                    </p>
                                </div>
                                <div>
                                    <flux:button wire:click="explainWithAi" variant="primary" icon="sparkles" wire:loading.attr="disabled">
                                        {{ __('Explain with AI') }}
                                    </flux:button>
                                </div>
                            </div>
                            
                            <div class="flex-1 p-6 overflow-y-auto space-y-6">
                                <!-- AI Explanation Box -->
                                @if ($aiExplanation)
                                    <div class="p-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/50 rounded-xl shadow-sm">
                                        <div class="flex items-center gap-2 mb-3">
                                            <flux:icon.sparkles class="size-4 text-blue-600 dark:text-blue-400" />
                                            <h4 class="font-semibold text-blue-800 dark:text-blue-300">{{ __('AI Explanation') }}</h4>
                                        </div>
                                        <div class="text-sm text-blue-900 dark:text-blue-200 whitespace-pre-wrap leading-relaxed">
                                            {{ $aiExplanation }}
                                        </div>
                                        <p class="mt-4 text-[10px] text-blue-600/70 dark:text-blue-400/70 italic">
                                            * {{ __('Disclaimer: This explanation is generated by AI and does not constitute medical advice. Please consult your physician for a formal diagnosis.') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Lab Data Table -->
                                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Test Name') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Value') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Flag') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Reference Range') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                            @foreach ($selectedLab->result_data as $obs)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-zinc-800 dark:text-zinc-200">{{ $obs['name'] ?? 'Unknown' }}</td>
                                                    <td class="px-4 py-3 text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $obs['value'] ?? '-' }} {{ $obs['unit'] ?? '' }}</td>
                                                    <td class="px-4 py-3 text-sm">
                                                        @if(isset($obs['flag']) && in_array(strtoupper($obs['flag']), ['H', 'HH', 'L', 'LL']))
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">{{ $obs['flag'] }}</span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800">{{ $obs['flag'] ?? 'N' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-zinc-500">{{ $obs['range'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center text-zinc-400 p-8">
                                <flux:icon.document-text class="size-12 text-zinc-300 mb-3" />
                                <p class="text-sm font-semibold">{{ __('No lab result selected') }}</p>
                                <p class="text-xs mt-1 text-center max-w-xs">{{ __('Select a lab report from the list on the left to view details and get an AI explanation.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </main>
    </div>
</div>
