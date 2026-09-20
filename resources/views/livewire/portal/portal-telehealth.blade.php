<div>
    <!-- Load Daily.co JS SDK -->
    <script crossorigin src="https://unpkg.com/@daily-co/daily-js"></script>

    <div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Telehealth Video Room') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Secure, HIPAA-compliant telehealth consultations') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <div x-data="{ callJoined: $wire.entangle('callJoined'), roomUrl: $wire.entangle('roomUrl'), meetingToken: $wire.entangle('meetingToken'), isConnecting: false }" class="flex-1 flex flex-col bg-slate-50 rounded-3xl overflow-hidden border border-slate-200 shadow-xl relative min-h-[500px]">
                    <!-- Real Daily.co Video Room -->
                    <div 
                        x-show="callJoined && roomUrl" 
                        x-init="
                            $watch('roomUrl', url => {
                                if (url) {
                                    const container = $el;
                                    container.innerHTML = '';
                                    const callFrame = window.DailyIframe.createFrame(container, {
                                        showLeaveButton: true,
                                        showFullscreenButton: true,
                                        iframeStyle: {
                                            width: '100%',
                                            height: '100%',
                                            minHeight: '500px',
                                            border: '0',
                                            borderRadius: '24px'
                                        }
                                    });
                                    callFrame.join({
                                        url: url,
                                        token: meetingToken
                                    });
                                    callFrame.on('left-meeting', () => {
                                        $wire.endCall();
                                    });
                                }
                            });
                            if (roomUrl) {
                                const container = $el;
                                container.innerHTML = '';
                                const callFrame = window.DailyIframe.createFrame(container, {
                                    showLeaveButton: true,
                                    showFullscreenButton: true,
                                    iframeStyle: {
                                        width: '100%',
                                        height: '100%',
                                        minHeight: '500px',
                                        border: '0',
                                        borderRadius: '24px'
                                    }
                                });
                                callFrame.join({
                                    url: roomUrl,
                                    token: meetingToken
                                });
                                callFrame.on('left-meeting', () => {
                                    $wire.endCall();
                                });
                            }
                        "
                        class="flex-1 w-full h-full min-h-[500px]"
                        wire:ignore
                    ></div>

                    <!-- Call Connected screen (Simulation Fallback) -->
                    <template x-if="callJoined && !roomUrl">
                        <div class="flex-1 flex flex-col md:flex-row relative">
                            <!-- Main Video Panel (Doctor) -->
                            <div class="flex-1 bg-white relative flex items-center justify-center">
                                <div class="absolute inset-0 bg-cover bg-center opacity-30 blur-md" style="background-image: url('https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=400');"></div>
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="h-24 w-24 rounded-full bg-slate-200 flex items-center justify-center text-2xl font-bold shadow-lg text-slate-800 border-4 border-primary">
                                        {{ $patient->primaryProvider ? strtoupper(substr($patient->primaryProvider->name, 0, 2)) : 'DR' }}
                                    </div>
                                    <h4 class="text-white font-bold text-lg mt-4">{{ $patient->primaryProvider ? $patient->primaryProvider->name : __('Primary Care Clinician') }}</h4>
                                    <span class="px-3 py-1 bg-green-500/25 border border-green-500/30 text-green-300 text-xs rounded-full flex items-center gap-1.5 mt-2">
                                        <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                        {{ __('Connected') }}
                                    </span>
                                </div>

                                <!-- Local Participant Overlay Video -->
                                <div class="absolute bottom-6 right-6 w-32 h-44 bg-slate-100 border-2 border-slate-300 rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center z-25">
                                    @if ($isCameraOff)
                                        <span class="material-symbols-outlined text-slate-500 text-2xl">videocam_off</span>
                                    @else
                                        <div class="h-full w-full bg-slate-800 flex flex-col items-center justify-center text-center p-2">
                                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold font-mono">
                                                {{ strtoupper(substr($patient->first_name, 0, 2)) }}
                                            </div>
                                            <span class="text-[10px] text-slate-500 mt-2 font-medium">{{ __('You') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Controls Toolbar -->
                            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-white/90 backdrop-blur-md px-6 py-3.5 rounded-full flex items-center gap-4 border border-slate-200 shadow-2xl z-30">
                                <button wire:click="toggleMute" class="h-11 w-11 rounded-full flex items-center justify-center transition-colors {{ $isMuted ? 'bg-red-500 hover:bg-red-650 text-white' : 'bg-slate-100 hover:bg-slate-700 text-slate-600' }}">
                                    <span class="material-symbols-outlined text-lg">{{ $isMuted ? 'mic_off' : 'mic' }}</span>
                                </button>
                                <button wire:click="toggleCamera" class="h-11 w-11 rounded-full flex items-center justify-center transition-colors {{ $isCameraOff ? 'bg-red-500 hover:bg-red-650 text-white' : 'bg-slate-100 hover:bg-slate-700 text-slate-600' }}">
                                    <span class="material-symbols-outlined text-lg">{{ $isCameraOff ? 'videocam_off' : 'videocam' }}</span>
                                </button>
                                <button wire:click="endCall" class="h-11 w-20 rounded-full bg-red-600 hover:bg-red-750 flex items-center justify-center text-white transition-colors">
                                    <span class="material-symbols-outlined text-lg mr-1">call_end</span>
                                    <span class="text-xs font-bold">{{ __('End') }}</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Pre-call Join Screen -->
                    <template x-if="!callJoined">
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
                            <div class="h-20 w-20 rounded-3xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-6">
                                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">videocam</span>
                            </div>
                            
                            <h3 class="text-white font-bold text-xl">{{ __('Secure Telehealth Session') }}</h3>
                            <p class="text-slate-500 text-sm max-w-sm mt-2">
                                {{ __('Please ensure your camera and microphone permissions are granted. You are joining a secure consultation with :provider.', ['provider' => $patient->primaryProvider?->name ?? __('your clinician')]) }}
                            </p>

                            <!-- Connecting State Simulation -->
                            <div class="mt-8 space-y-4" x-show="isConnecting">
                                <div class="flex items-center justify-center gap-2 text-slate-500 text-xs font-semibold">
                                    <span class="h-4 w-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    <span>{{ __('Connecting to secure server...') }}</span>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-4" x-show="!isConnecting">
                                <flux:button variant="ghost" class="text-slate-500 border-slate-200 hover:bg-slate-50" href="{{ route('portal.dashboard') }}">
                                    {{ __('Cancel') }}
                                </flux:button>
                                <button x-on:click="isConnecting = true; setTimeout(() => { isConnecting = false; $wire.joinCall(); }, 2000)" 
                                        class="px-5 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl flex items-center gap-2 transition-all shadow-lg">
                                    <span class="material-symbols-outlined text-lg">video_call</span>
                                    <span>{{ __('Join Call') }}</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic bg-white border border-slate-200 rounded-2xl">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </div>
    </div>
</div>
