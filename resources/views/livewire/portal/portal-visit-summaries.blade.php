<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Visit Summaries') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ $enc->provider?->name ?? __('Your Provider') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- List -->
                    <div class="lg:col-span-1 space-y-2">
                        @forelse ($encounters as $enc)
                            <button
                                wire:click="selectEncounter({{ $enc->id }})"
                                class="w-full text-left bg-white border border-slate-200 rounded-xl px-4 py-3 hover:border-indigo-600/30 transition-colors"
                            >
                                <p class="text-sm font-semibold text-slate-800 ">{{ $enc->encounter_date->format('M j, Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $enc->provider?->name ?? __('Your Provider') }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-100 text-green-700">{{ __('Released') }}</span>
                            </button>
                        @empty
                            <div class="bg-white border border-slate-200 rounded-xl px-4 py-12 text-center text-slate-500 text-sm">
                                {{ __('No visit summaries have been released yet.') }}
                            </div>
                        @endforelse
                    </div>

                    <!-- Detail -->
                    <div class="lg:col-span-2">
                        @if ($activeEncounter)
                            <div class="bg-white border border-slate-200 rounded-2xl p-8">
                                <div class="mb-6">
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">{{ __('Visit Date') }}</p>
                                    <h2 class="text-xl font-bold text-slate-800 mt-1">{{ $activeEncounter->encounter_date->format('l, F j, Y') }}</h2>
                                    <p class="text-sm text-slate-500 mt-1">{{ __('Provider: :name', ['name' => $activeEncounter->provider?->name ?? __('Unknown')]) }}</p>
                                </div>

                                @if ($activeEncounter->chief_complaint)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('Chief Complaint') }}</p>
                                        <p class="text-sm text-slate-700 ">{{ $activeEncounter->chief_complaint }}</p>
                                    </div>
                                @endif

                                @if ($activeEncounter->assessment)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('Assessment') }}</p>
                                        <p class="text-sm text-slate-700 ">{{ $activeEncounter->assessment }}</p>
                                    </div>
                                @endif

                                @if ($activeEncounter->plan)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('Plan') }}</p>
                                        <p class="text-sm text-slate-700 ">{{ $activeEncounter->plan }}</p>
                                    </div>
                                @endif

                                @foreach ($activeEncounter->clinicalNotes as $note)
                                    <div class="mb-4 p-4 bg-slate-50 rounded-xl">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('Clinical Note') }}</p>
                                        <div class="text-sm text-slate-700 prose prose-sm max-w-none">{!! nl2br(e($note->body)) !!}</div>
                                    </div>
                                @endforeach

                                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                                    <span class="material-symbols-outlined text-sm">lock</span>
                                    {{ __('Released to portal on :date', ['date' => $activeEncounter->released_to_portal_at->format('M j, Y')]) }}
                                </div>
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center bg-white border border-slate-200 rounded-2xl py-24">
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">summarize</span>
                                    <p class="text-slate-500 text-sm">{{ __('Select a visit to view its summary') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-24 text-slate-500">{{ __('No patient profile found.') }}</div>
            @endif
        </div>
</div>
