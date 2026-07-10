<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <x-portal.sidebar />

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Smart Symptom Triage') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('Describe your symptoms to receive automated routing advice') }}
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
        <main class="flex-1 p-6 md:p-8 overflow-y-auto flex flex-col items-center justify-start">
            <div class="w-full max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-8 shadow-sm">
                
                @if (empty($triageResult))
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <flux:icon.heart class="size-8" />
                        </div>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('How are you feeling today?') }}</h2>
                        <p class="text-sm text-zinc-500 mt-2">{{ __('Please describe your symptoms in detail. Our AI assistant will help route you to the appropriate care setting.') }}</p>
                    </div>

                    <form wire:submit.prevent="evaluateSymptoms" class="space-y-4">
                        <flux:textarea wire:model="symptoms" :label="__('Symptoms')" rows="5" placeholder="{{ __('e.g., I have had a headache for two days and a mild fever...') }}" required />
                        
                        <div class="flex justify-end pt-2">
                            <flux:button type="submit" variant="primary" icon="sparkles" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="evaluateSymptoms">{{ __('Evaluate Symptoms') }}</span>
                                <span wire:loading wire:target="evaluateSymptoms">{{ __('Analyzing...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                    
                    <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400 rounded-lg border border-yellow-200 dark:border-yellow-800/50 text-xs">
                        <strong>{{ __('Disclaimer:') }}</strong> {{ __('This tool provides routing recommendations only and does not provide medical diagnoses. If you are experiencing a life-threatening emergency, please call 911 immediately.') }}
                    </div>
                @else
                    <div class="text-center mb-6">
                        @php
                            $action = $triageResult['action'] ?? 'routine';
                            $config = [
                                'emergency' => ['color' => 'red', 'icon' => 'exclamation-triangle', 'title' => __('Seek Emergency Care')],
                                'urgent' => ['color' => 'orange', 'icon' => 'clock', 'title' => __('Urgent Care Recommended')],
                                'telehealth' => ['color' => 'blue', 'icon' => 'video-camera', 'title' => __('Telehealth Visit Suitable')],
                                'routine' => ['color' => 'green', 'icon' => 'calendar', 'title' => __('Routine Care')],
                            ];
                            $cfg = $config[$action] ?? $config['routine'];
                        @endphp
                        
                        <div class="w-20 h-20 bg-{{ $cfg['color'] }}-100 dark:bg-{{ $cfg['color'] }}-900/40 text-{{ $cfg['color'] }}-600 dark:text-{{ $cfg['color'] }}-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <flux:icon.{{ $cfg['icon'] }} class="size-10" />
                        </div>
                        <h2 class="text-2xl font-bold text-{{ $cfg['color'] }}-700 dark:text-{{ $cfg['color'] }}-400">{{ $cfg['title'] }}</h2>
                    </div>

                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                        <p class="text-zinc-800 dark:text-zinc-200 text-sm leading-relaxed">
                            {{ $triageResult['advice'] ?? '' }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <flux:button wire:click="resetTriage" variant="ghost">
                            {{ __('Start Over') }}
                        </flux:button>
                        
                        @if ($action === 'emergency')
                            <flux:button variant="danger" class="w-full sm:w-auto">
                                {{ __('Call 911') }}
                            </flux:button>
                        @elseif ($action === 'urgent')
                            <flux:button variant="primary" class="w-full sm:w-auto">
                                {{ __('Find Nearest Urgent Care') }}
                            </flux:button>
                        @elseif ($action === 'telehealth')
                            <flux:button href="{{ route('portal.telehealth') }}" variant="primary" class="w-full sm:w-auto">
                                {{ __('Join Walk-in Telehealth') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('portal.appointments') }}" variant="primary" class="w-full sm:w-auto">
                                {{ __('Schedule Appointment') }}
                            </flux:button>
                        @endif
                    </div>
                @endif
                
            </div>
        </main>
    </div>
</div>
