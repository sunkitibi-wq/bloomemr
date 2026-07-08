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
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.assessments') }}"><span class="material-symbols-outlined text-xl">psychology</span><span class="text-sm">{{ __('Assessments') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy font-semibold bg-slate-100 rounded-lg" href="{{ route('portal.visit-summaries') }}"><span class="material-symbols-outlined text-xl">summarize</span><span class="text-sm">{{ __('Visit Summaries') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.education') }}"><span class="material-symbols-outlined text-xl">menu_book</span><span class="text-sm">{{ __('Education Library') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.documents') }}"><span class="material-symbols-outlined text-xl">upload_file</span><span class="text-sm">{{ __('Upload Documents') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.billing') }}"><span class="material-symbols-outlined text-xl">receipt_long</span><span class="text-sm">{{ __('Billing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.care-coordination') }}"><span class="material-symbols-outlined text-xl">share</span><span class="text-sm">{{ __('Care Sharing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.radiology') }}"><span class="material-symbols-outlined text-xl">biotech</span><span class="text-sm">{{ __('Imaging Reports') }}</span></a>
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
            <span class="material-symbols-outlined text-trust-navy text-2xl">summarize</span>
            <h1 class="font-bold text-trust-navy dark:text-zinc-100 text-lg">{{ __('Visit Summaries') }}</h1>
        </header>

        <main class="flex-1 p-8 overflow-y-auto">
            @if ($patient)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- List -->
                    <div class="lg:col-span-1 space-y-2">
                        @forelse ($encounters as $enc)
                            <button
                                wire:click="selectEncounter({{ $enc->id }})"
                                class="w-full text-left bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 hover:border-trust-navy/30 transition-colors"
                            >
                                <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100">{{ $enc->encounter_date->format('M j, Y') }}</p>
                                <p class="text-xs text-zinc-400">{{ $enc->provider?->name ?? __('Your Provider') }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-100 text-green-700">{{ __('Released') }}</span>
                            </button>
                        @empty
                            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-12 text-center text-zinc-400 text-sm">
                                {{ __('No visit summaries have been released yet.') }}
                            </div>
                        @endforelse
                    </div>

                    <!-- Detail -->
                    <div class="lg:col-span-2">
                        @if ($activeEncounter)
                            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-8">
                                <div class="mb-6">
                                    <p class="text-xs text-zinc-400 uppercase tracking-wide">{{ __('Visit Date') }}</p>
                                    <h2 class="text-xl font-bold text-trust-navy dark:text-zinc-100 mt-1">{{ $activeEncounter->encounter_date->format('l, F j, Y') }}</h2>
                                    <p class="text-sm text-zinc-500 mt-1">{{ __('Provider: :name', ['name' => $activeEncounter->provider?->name ?? __('Unknown')]) }}</p>
                                </div>

                                @if ($activeEncounter->chief_complaint)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-2">{{ __('Chief Complaint') }}</p>
                                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $activeEncounter->chief_complaint }}</p>
                                    </div>
                                @endif

                                @if ($activeEncounter->assessment)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-2">{{ __('Assessment') }}</p>
                                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $activeEncounter->assessment }}</p>
                                    </div>
                                @endif

                                @if ($activeEncounter->plan)
                                    <div class="mb-6">
                                        <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-2">{{ __('Plan') }}</p>
                                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $activeEncounter->plan }}</p>
                                    </div>
                                @endif

                                @foreach ($activeEncounter->clinicalNotes as $note)
                                    <div class="mb-4 p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl">
                                        <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-2">{{ __('Clinical Note') }}</p>
                                        <div class="text-sm text-zinc-700 dark:text-zinc-300 prose prose-sm max-w-none">{!! nl2br(e($note->body)) !!}</div>
                                    </div>
                                @endforeach

                                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-zinc-800 flex items-center gap-2 text-xs text-zinc-400">
                                    <span class="material-symbols-outlined text-sm">lock</span>
                                    {{ __('Released to portal on :date', ['date' => $activeEncounter->released_to_portal_at->format('M j, Y')]) }}
                                </div>
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl py-24">
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">summarize</span>
                                    <p class="text-zinc-400 text-sm">{{ __('Select a visit to view its summary') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-24 text-zinc-400">{{ __('No patient profile found.') }}</div>
            @endif
        </main>
    </div>
</div>
