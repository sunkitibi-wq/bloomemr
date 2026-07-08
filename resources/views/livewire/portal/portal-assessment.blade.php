<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar -->
    <nav class="w-full md:w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col p-6 shrink-0">
        <div class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">B</div>
            <div>
                <span class="font-bold text-trust-navy dark:text-zinc-100 text-base">Bloom</span>
                <span class="text-[9px] text-zinc-400 block -mt-1 uppercase tracking-wider">{{ __('Patient Portal') }}</span>
            </div>
        </div>
        <div class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.dashboard') }}"><span class="material-symbols-outlined text-xl">home</span><span class="text-sm">{{ __('Home') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.appointments') }}"><span class="material-symbols-outlined text-xl">calendar_today</span><span class="text-sm">{{ __('Appointments') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.messages') }}"><span class="material-symbols-outlined text-xl">mail</span><span class="text-sm">{{ __('Secure Messages') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.refills') }}"><span class="material-symbols-outlined text-xl">vaccines</span><span class="text-sm">{{ __('Refill Requests') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.telehealth') }}"><span class="material-symbols-outlined text-xl">videocam</span><span class="text-sm">{{ __('Telehealth Room') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.forms') }}"><span class="material-symbols-outlined text-xl">description</span><span class="text-sm">{{ __('Intake & Consents') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy font-semibold bg-slate-100 rounded-lg" href="{{ route('portal.assessments') }}"><span class="material-symbols-outlined text-xl">psychology</span><span class="text-sm">{{ __('Assessments') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.visit-summaries') }}"><span class="material-symbols-outlined text-xl">summarize</span><span class="text-sm">{{ __('Visit Summaries') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.education') }}"><span class="material-symbols-outlined text-xl">menu_book</span><span class="text-sm">{{ __('Education Library') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.documents') }}"><span class="material-symbols-outlined text-xl">upload_file</span><span class="text-sm">{{ __('Upload Documents') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.billing') }}"><span class="material-symbols-outlined text-xl">receipt_long</span><span class="text-sm">{{ __('Billing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.care-coordination') }}"><span class="material-symbols-outlined text-xl">share</span><span class="text-sm">{{ __('Care Sharing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.radiology') }}"><span class="material-symbols-outlined text-xl">biotech</span><span class="text-sm">{{ __('Imaging') }}</span></a>
        </div>
        <div class="mt-auto pt-4 border-t border-slate-100">
            <a class="flex items-center gap-3 px-4 py-2 text-zinc-500 hover:text-trust-navy transition-colors" href="{{ route('dashboard') }}" wire:navigate>
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                <span class="text-xs">{{ __('Back to EMR') }}</span>
            </a>
        </div>
    </nav>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex items-center px-8 gap-4">
            <span class="material-symbols-outlined text-trust-navy text-2xl">psychology</span>
            <h1 class="font-bold text-trust-navy dark:text-zinc-100 text-lg">{{ __('Assessments') }}</h1>
        </header>

        <main class="flex-1 p-8 overflow-y-auto">
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
            @endif

            @if ($patient)
                @if ($activeAssessment && $activeInstrument && ! $isSubmitted)
                    <!-- Active Assessment Form -->
                    <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-8">
                        <button wire:click="$set('activeAssessment', null)" class="flex items-center gap-1 text-xs text-zinc-400 hover:text-zinc-600 mb-6 transition-colors">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            {{ __('Back to assessments') }}
                        </button>

                        <h2 class="text-lg font-bold text-trust-navy dark:text-zinc-100 mb-1">{{ $activeInstrument['name'] }}</h2>
                        <p class="text-sm text-zinc-500 mb-8">{{ $activeInstrument['description'] }}</p>

                        <div class="space-y-6">
                            @foreach ($activeInstrument['questions'] as $qId => $question)
                                <div class="border-b border-slate-100 dark:border-zinc-800 pb-5">
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-3">
                                        <span class="text-zinc-400 mr-1">{{ $qId }}.</span>
                                        {{ $question }}
                                    </p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        @foreach ($activeInstrument['choices'] as $value => $label)
                                            <label class="flex flex-col items-center cursor-pointer group">
                                                <input
                                                    type="radio"
                                                    wire:model="responses.{{ $qId }}"
                                                    value="{{ $value }}"
                                                    class="sr-only peer"
                                                >
                                                <div class="w-full text-center py-2 px-2 rounded-lg border text-xs font-medium transition-all peer-checked:bg-trust-navy peer-checked:text-white peer-checked:border-trust-navy border-slate-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 group-hover:border-trust-navy/50">
                                                    {{ $label }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex items-center gap-4">
                            <button
                                wire:click="submitAssessment"
                                class="px-8 py-3 bg-trust-navy text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity"
                            >
                                {{ __('Submit Assessment') }}
                            </button>
                            <p class="text-xs text-zinc-400">{{ __('All questions must be answered.') }}</p>
                        </div>
                    </div>

                @elseif ($isSubmitted)
                    <!-- Score Result -->
                    <div class="max-w-lg mx-auto bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-10 text-center">
                        <span class="material-symbols-outlined text-5xl text-green-500 block mb-4" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <h2 class="text-xl font-bold text-trust-navy dark:text-zinc-100 mb-2">{{ __('Assessment Submitted') }}</h2>
                        <p class="text-sm text-zinc-500 mb-6">{{ __('Your care team will review the results at your next visit.') }}</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-5 mb-6">
                            <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-1">{{ __('Your Score') }}</p>
                            <p class="text-4xl font-bold text-trust-navy dark:text-zinc-100">{{ $score }}</p>
                            <p class="text-sm text-zinc-500 mt-1">{{ $severityBand }}</p>
                        </div>
                        <button wire:click="$set('isSubmitted', false); $set('activeAssessment', null)" class="px-6 py-2.5 bg-trust-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                            {{ __('Back to Assessments') }}
                        </button>
                    </div>

                @else
                    <!-- List view -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Pending -->
                        <div>
                            <h2 class="font-semibold text-trust-navy dark:text-zinc-100 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                {{ __('Pending Assessments') }}
                                @if ($pendingAssessments->isNotEmpty())
                                    <span class="ml-auto px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $pendingAssessments->count() }}</span>
                                @endif
                            </h2>
                            @forelse ($pendingAssessments as $assessment)
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl p-5 mb-3 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-amber-600 text-xl">psychology</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-trust-navy dark:text-zinc-100 text-sm">{{ strtoupper($assessment->instrument) }}</p>
                                        <p class="text-xs text-zinc-400">{{ __('Assigned :date', ['date' => $assessment->created_at->format('M j, Y')]) }}</p>
                                    </div>
                                    <button
                                        wire:click="startAssessment({{ $assessment->id }})"
                                        class="px-4 py-2 bg-trust-navy text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity"
                                    >
                                        {{ __('Begin') }}
                                    </button>
                                </div>
                            @empty
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-10 text-center text-zinc-400 text-sm">
                                    {{ __('No pending assessments.') }}
                                </div>
                            @endforelse
                        </div>

                        <!-- Completed -->
                        <div>
                            <h2 class="font-semibold text-trust-navy dark:text-zinc-100 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                {{ __('Completed Assessments') }}
                            </h2>
                            @forelse ($completedAssessments as $assessment)
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl p-5 mb-3 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-green-600 text-xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-trust-navy dark:text-zinc-100 text-sm">{{ strtoupper($assessment->instrument) }}</p>
                                        <p class="text-xs text-zinc-400">{{ __('Completed :date', ['date' => $assessment->updated_at->format('M j, Y')]) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-trust-navy dark:text-zinc-100">{{ $assessment->score }}</p>
                                        <p class="text-[10px] text-zinc-400 max-w-[100px] text-right">{{ $assessment->severity_band }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-10 text-center text-zinc-400 text-sm">
                                    {{ __('No completed assessments yet.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-24 text-zinc-400">{{ __('No patient profile found.') }}</div>
            @endif
        </main>
    </div>
</div>
