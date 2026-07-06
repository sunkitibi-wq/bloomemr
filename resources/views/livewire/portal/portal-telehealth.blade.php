<!-- Load Daily.co JS SDK -->
<script crossorigin src="https://unpkg.com/@daily-co/daily-js"></script>

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
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.telehealth') }}">
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
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.radiology') }}">
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

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Telehealth Video Room') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('Secure, HIPAA-compliant telehealth consultations') }}
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
        <main class="flex-1 p-6 md:p-8 flex flex-col overflow-y-auto" x-data="{ callJoined: @entangle('isCallActive'), isConnecting: false }">
            @if ($patient)
                <div class="flex-1 flex flex-col bg-zinc-950 rounded-3xl overflow-hidden border border-zinc-800 shadow-xl relative min-h-[500px]">
                    <!-- Real Daily.co Video Room -->
                    <div 
                        x-show="callJoined && $wire.roomUrl" 
                        x-init="
                            $watch('$wire.roomUrl', url => {
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
                                        token: $wire.meetingToken
                                    });
                                    callFrame.on('left-meeting', () => {
                                        $wire.endCall();
                                    });
                                }
                            });
                            if ($wire.roomUrl) {
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
                                    url: $wire.roomUrl,
                                    token: $wire.meetingToken
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
                    <template x-if="callJoined && !$wire.roomUrl">
                        <div class="flex-1 flex flex-col md:flex-row relative">
                            <!-- Main Video Panel (Doctor) -->
                            <div class="flex-1 bg-zinc-900 relative flex items-center justify-center">
                                <div class="absolute inset-0 bg-cover bg-center opacity-30 blur-md" style="background-image: url('https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=400');"></div>
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="h-24 w-24 rounded-full bg-slate-200 flex items-center justify-center text-2xl font-bold shadow-lg text-zinc-800 border-4 border-primary">
                                        {{ $patient->primaryProvider ? strtoupper(substr($patient->primaryProvider->name, 0, 2)) : 'DR' }}
                                    </div>
                                    <h4 class="text-white font-bold text-lg mt-4">{{ $patient->primaryProvider ? $patient->primaryProvider->name : __('Primary Care Clinician') }}</h4>
                                    <span class="px-3 py-1 bg-green-500/25 border border-green-500/30 text-green-300 text-xs rounded-full flex items-center gap-1.5 mt-2">
                                        <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                        {{ __('Connected') }}
                                    </span>
                                </div>

                                <!-- Local Participant Overlay Video -->
                                <div class="absolute bottom-6 right-6 w-32 h-44 bg-zinc-800 border-2 border-zinc-700 rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center z-25">
                                    @if ($isCameraOff)
                                        <span class="material-symbols-outlined text-zinc-500 text-2xl">videocam_off</span>
                                    @else
                                        <div class="h-full w-full bg-zinc-750 flex flex-col items-center justify-center text-center p-2">
                                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold font-mono">
                                                {{ strtoupper(substr($patient->first_name, 0, 2)) }}
                                            </div>
                                            <span class="text-[10px] text-zinc-400 mt-2 font-medium">{{ __('You') }}</span>
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
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-zinc-900">
                            <div class="h-20 w-20 rounded-3xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-6">
                                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">videocam</span>
                            </div>
                            
                            <h3 class="text-white font-bold text-xl">{{ __('Secure Telehealth Session') }}</h3>
                            <p class="text-zinc-400 text-sm max-w-sm mt-2">
                                {{ __('Please ensure your camera and microphone permissions are granted. You are joining a secure consultation with :provider.', ['provider' => $patient->primaryProvider?->name ?? __('your clinician')]) }}
                            </p>

                            <!-- Connecting State Simulation -->
                            <div class="mt-8 space-y-4" x-show="isConnecting">
                                <div class="flex items-center justify-center gap-2 text-zinc-400 text-xs font-semibold">
                                    <span class="h-4 w-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    <span>{{ __('Connecting to secure server...') }}</span>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-4" x-show="!isConnecting">
                                <flux:button variant="ghost" class="text-zinc-400 border-zinc-800 hover:bg-zinc-800/50" href="{{ route('portal.dashboard') }}">
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
                <div class="p-8 text-center text-zinc-400 italic bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </main>
    </div>
</div>
