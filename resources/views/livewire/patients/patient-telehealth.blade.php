<div>
    <!-- Load Daily.co JS SDK -->
    <script crossorigin src="https://unpkg.com/@daily-co/daily-js"></script>

    <div class="mt-6 bg-zinc-950 rounded-3xl overflow-hidden border border-zinc-800 shadow-xl relative min-h-[500px] flex flex-col" x-data="{ callJoined: @entangle('isCallActive'), isConnecting: false, roomUrl: @entangle('roomUrl'), meetingToken: @entangle('meetingToken') }">
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
        <div class="flex-1 flex flex-col md:flex-row relative min-h-[500px]">
            <!-- Main Video Panel (Patient) -->
            <div class="flex-1 bg-zinc-900 relative flex items-center justify-center">
                <div class="absolute inset-0 bg-cover bg-center opacity-30 blur-md" style="background-image: url('https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=400');"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="h-24 w-24 rounded-full bg-slate-200 flex items-center justify-center text-2xl font-bold shadow-lg text-zinc-800 border-4 border-primary">
                        {{ strtoupper(substr($patient->first_name, 0, 2)) }}
                    </div>
                    <h4 class="text-white font-bold text-lg mt-4">{{ $patient->full_name }} (Patient)</h4>
                    <span class="px-3 py-1 bg-green-500/25 border border-green-500/30 text-green-300 text-xs rounded-full flex items-center gap-1.5 mt-2">
                        <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                        {{ __('Patient Connected') }}
                    </span>
                </div>

                <!-- Local Participant Overlay Video (Clinician/You) -->
                <div class="absolute bottom-6 right-6 w-32 h-44 bg-zinc-800 border-2 border-zinc-700 rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center z-25">
                    @if ($isCameraOff)
                        <span class="material-symbols-outlined text-zinc-500 text-2xl">videocam_off</span>
                    @else
                        <div class="h-full w-full bg-zinc-750 flex flex-col items-center justify-center text-center p-2">
                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold font-mono">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="text-[10px] text-zinc-400 mt-2 font-medium">{{ __('You (Doctor)') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Controls Toolbar -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-zinc-900/90 backdrop-blur-md px-6 py-3.5 rounded-full flex items-center gap-4 border border-zinc-800 shadow-2xl z-30">
                <button wire:click="toggleMute" class="h-11 w-11 rounded-full flex items-center justify-center transition-colors {{ $isMuted ? 'bg-red-500 hover:bg-red-650 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                    <span class="material-symbols-outlined text-lg">{{ $isMuted ? 'mic_off' : 'mic' }}</span>
                </button>
                <button wire:click="toggleCamera" class="h-11 w-11 rounded-full flex items-center justify-center transition-colors {{ $isCameraOff ? 'bg-red-500 hover:bg-red-650 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
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
        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-zinc-900 min-h-[500px]">
            <div class="h-20 w-20 rounded-3xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-6">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">emergency_recording</span>
            </div>
            
            <h3 class="text-white font-bold text-xl">{{ __('Clinician Telehealth Portal') }}</h3>
            <p class="text-zinc-400 text-sm max-w-sm mt-2">
                {{ __('Start the secure consultation room for patient :patient. The family will receive an automatic check-in notification.', ['patient' => $patient->full_name]) }}
            </p>

            <!-- Connecting State Simulation -->
            <div class="mt-8 space-y-4" x-show="isConnecting">
                <div class="flex items-center justify-center gap-2 text-zinc-400 text-xs font-semibold">
                    <span class="h-4 w-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                    <span>{{ __('Initializing room encryption keys...') }}</span>
                </div>
            </div>

            <div class="mt-8 flex gap-4" x-show="!isConnecting">
                <button x-on:click="isConnecting = true; setTimeout(() => { isConnecting = false; $wire.joinCall(); }, 2000)" 
                        class="px-6 py-3 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl inline-flex items-center gap-3 transition-all shadow-lg whitespace-nowrap">
                    <span class="material-symbols-outlined text-xl leading-none">video_call</span>
                    <span>{{ __('Start Telehealth Call') }}</span>
                </button>
            </div>
        </div>
    </template>
    </div>
</div>
