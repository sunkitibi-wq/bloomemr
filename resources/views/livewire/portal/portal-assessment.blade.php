<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Assessments') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('All questions must be answered.') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
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
                    <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-2xl p-8">
                        <button wire:click="$set('activeAssessment', null)" class="flex items-center gap-1 text-xs text-slate-500 hover:text-slate-600 mb-6 transition-colors">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            {{ __('Back to assessments') }}
                        </button>

                        <h2 class="text-lg font-bold text-slate-800 mb-1">{{ $activeInstrument['name'] }}</h2>
                        <p class="text-sm text-slate-500 mb-8">{{ $activeInstrument['description'] }}</p>

                        <div class="space-y-6">
                            @foreach ($activeInstrument['questions'] as $qId => $question)
                                <div class="border-b border-slate-100 pb-5">
                                    <p class="text-sm font-medium text-slate-700 mb-3">
                                        <span class="text-slate-500 mr-1">{{ $qId }}.</span>
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
                                                <div class="w-full text-center py-2 px-2 rounded-lg border text-xs font-medium transition-all peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 border-slate-200 text-slate-500 group-hover:border-indigo-600/50">
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
                                class="px-8 py-3 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity"
                            >
                                {{ __('Submit Assessment') }}
                            </button>
                            <p class="text-xs text-slate-500">{{ __('All questions must be answered.') }}</p>
                        </div>
                    </div>

                @elseif ($isSubmitted)
                    <!-- Score Result -->
                    <div class="max-w-lg mx-auto bg-white border border-slate-200 rounded-2xl p-10 text-center">
                        <span class="material-symbols-outlined text-5xl text-green-500 block mb-4" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <h2 class="text-xl font-bold text-slate-800 mb-2">{{ __('Assessment Submitted') }}</h2>
                        <p class="text-sm text-slate-500 mb-6">{{ __('Your care team will review the results at your next visit.') }}</p>
                        <div class="bg-slate-50 rounded-xl p-5 mb-6">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">{{ __('Your Score') }}</p>
                            <p class="text-4xl font-bold text-slate-800 ">{{ $score }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ $severityBand }}</p>
                        </div>
                        <button wire:click="$set('isSubmitted', false); $set('activeAssessment', null)" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                            {{ __('Back to Assessments') }}
                        </button>
                    </div>

                @else
                    <!-- List view -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Pending -->
                        <div>
                            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                {{ __('Pending Assessments') }}
                                @if ($pendingAssessments->isNotEmpty())
                                    <span class="ml-auto px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $pendingAssessments->count() }}</span>
                                @endif
                            </h2>
                            @forelse ($pendingAssessments as $assessment)
                                <div class="bg-white border border-slate-200 rounded-xl p-5 mb-3 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-amber-600 text-xl">psychology</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-800 text-sm">{{ strtoupper($assessment->instrument) }}</p>
                                        <p class="text-xs text-slate-500">{{ __('Assigned :date', ['date' => $assessment->created_at->format('M j, Y')]) }}</p>
                                    </div>
                                    <button
                                        wire:click="startAssessment({{ $assessment->id }})"
                                        class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity"
                                    >
                                        {{ __('Begin') }}
                                    </button>
                                </div>
                            @empty
                                <div class="bg-white border border-slate-200 rounded-xl px-5 py-10 text-center text-slate-500 text-sm">
                                    {{ __('No pending assessments.') }}
                                </div>
                            @endforelse
                        </div>

                        <!-- Completed -->
                        <div>
                            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                {{ __('Completed Assessments') }}
                            </h2>
                            @forelse ($completedAssessments as $assessment)
                                <div class="bg-white border border-slate-200 rounded-xl p-5 mb-3 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-green-600 text-xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-800 text-sm">{{ strtoupper($assessment->instrument) }}</p>
                                        <p class="text-xs text-slate-500">{{ __('Completed :date', ['date' => $assessment->updated_at->format('M j, Y')]) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-slate-800 ">{{ $assessment->score }}</p>
                                        <p class="text-[10px] text-slate-500 max-w-[100px] text-right">{{ $assessment->severity_band }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white border border-slate-200 rounded-xl px-5 py-10 text-center text-slate-500 text-sm">
                                    {{ __('No completed assessments yet.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-24 text-slate-500">{{ __('No patient profile found.') }}</div>
            @endif
        </div>
</div>
