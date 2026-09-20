<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Smart Symptom Triage') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Describe your symptoms to receive automated routing advice') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            <div class="w-full max-w-2xl bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
                
                @if (empty($triageResult))
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <flux:icon.heart class="size-8" />
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 ">{{ __('How are you feeling today?') }}</h2>
                        <p class="text-sm text-slate-500 mt-2">{{ __('Please describe your symptoms in detail. Our AI assistant will help route you to the appropriate care setting.') }}</p>
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
                    
                    <div class="mt-8 p-4 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200 text-xs">
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
                        
                        <div class="w-20 h-20 bg-{{ $cfg['color'] }}-100 {{ $cfg['color'] }}-900/40 text-{{ $cfg['color'] }}-600 {{ $cfg['color'] }}-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <flux:icon.{{ $cfg['icon'] }} class="size-10" />
                        </div>
                        <h2 class="text-2xl font-bold text-{{ $cfg['color'] }}-700 {{ $cfg['color'] }}-400">{{ $cfg['title'] }}</h2>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-xl border border-slate-200 mb-8">
                        <p class="text-slate-800 text-sm leading-relaxed">
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
        </div>
</div>
