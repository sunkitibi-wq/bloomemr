<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar Navigation -->
    <nav class="w-full md:w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col p-6 shrink-0">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">B</div>
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
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.messages') }}">
                <span class="material-symbols-outlined text-xl">mail</span>
                <span class="text-sm">{{ __('Secure Messages') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.refills') }}">
                <span class="material-symbols-outlined text-xl">vaccines</span>
                <span class="text-sm">{{ __('Refill Requests') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.telehealth') }}">
                <span class="material-symbols-outlined text-xl">videocam</span>
                <span class="text-sm">{{ __('Telehealth Room') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.forms') }}">
                <span class="material-symbols-outlined text-xl">description</span>
                <span class="text-sm">{{ __('Intake & Consents') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.assessments') }}">
                <span class="material-symbols-outlined text-xl">psychology</span>
                <span class="text-sm">{{ __('Assessments') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.visit-summaries') }}">
                <span class="material-symbols-outlined text-xl">summarize</span>
                <span class="text-sm">{{ __('Visit Summaries') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.education') }}">
                <span class="material-symbols-outlined text-xl">menu_book</span>
                <span class="text-sm">{{ __('Education Library') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.documents') }}">
                <span class="material-symbols-outlined text-xl">upload_file</span>
                <span class="text-sm">{{ __('Upload Documents') }}</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.billing') }}">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-sm">{{ __('Billing & Invoices') }}</span>
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

        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-zinc-800">
            <a class="flex items-center gap-3 px-4 py-2 text-zinc-500 dark:text-zinc-400 hover:text-trust-navy dark:hover:text-zinc-100 transition-colors" href="{{ route('dashboard') }}" wire:navigate>
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                <span class="text-xs">{{ __('Back to EMR') }}</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex items-center px-8 gap-4">
            <span class="material-symbols-outlined text-trust-navy dark:text-zinc-100 text-2xl">menu_book</span>
            <h1 class="font-bold text-trust-navy dark:text-zinc-100 text-lg">{{ __('Education Library') }}</h1>
        </header>

        <main class="flex-1 p-8 overflow-y-auto">
            @if (session('message'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('message') }}</div>
            @endif

            @if ($patient)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Article List -->
                    <div class="lg:col-span-1 space-y-4">
                        <!-- Category filter -->
                        <div class="flex gap-2 flex-wrap">
                            @foreach (['all', 'diagnosis', 'medication', 'general'] as $cat)
                                <button
                                    wire:click="$set('selectedCategory', '{{ $cat }}')"
                                    class="px-3 py-1 rounded-full text-xs font-semibold border transition-colors {{ $selectedCategory === $cat ? 'bg-trust-navy text-white border-trust-navy' : 'bg-white text-zinc-500 border-slate-200 hover:border-trust-navy' }}"
                                >
                                    {{ ucfirst($cat) }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Assigned articles first -->
                        @if ($assignments->isNotEmpty())
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-3">{{ __('Assigned to You') }}</p>
                                @foreach ($assignments as $assignment)
                                    <button
                                        wire:click="selectArticle({{ $assignment->article->id }})"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-amber-100 transition-colors flex items-center justify-between gap-2 mb-1"
                                    >
                                        <span class="text-sm font-medium text-amber-900 truncate">{{ $assignment->article->title }}</span>
                                        @if ($assignment->acknowledged_at)
                                            <span class="material-symbols-outlined text-green-600 text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <!-- Full library -->
                        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            @forelse ($articles as $article)
                                <button
                                    wire:click="selectArticle({{ $article->id }})"
                                    class="w-full text-left px-4 py-3 border-b border-slate-100 dark:border-zinc-800 last:border-0 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors flex items-start gap-3"
                                >
                                    <span class="material-symbols-outlined text-trust-navy dark:text-indigo-400 text-base mt-0.5">
                                        {{ $article->category === 'medication' ? 'medication' : ($article->category === 'diagnosis' ? 'psychiatry' : 'article') }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100 truncate">{{ $article->title }}</p>
                                        <p class="text-xs text-zinc-400 capitalize">{{ $article->category }}</p>
                                    </div>
                                </button>
                            @empty
                                <div class="px-4 py-8 text-center text-zinc-400 text-sm">{{ __('No articles available yet.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Article Viewer -->
                    <div class="lg:col-span-2">
                        @if ($activeArticle)
                            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-8">
                                <div class="flex items-start justify-between mb-6">
                                    <div>
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 capitalize mb-2">{{ $activeArticle->category }}</span>
                                        <h2 class="text-xl font-bold text-trust-navy dark:text-zinc-100">{{ $activeArticle->title }}</h2>
                                    </div>
                                </div>

                                @if ($activeArticle->video_url)
                                    <div class="mb-6 aspect-video rounded-xl overflow-hidden bg-black">
                                        <iframe src="{{ $activeArticle->video_url }}" class="w-full h-full" allowfullscreen></iframe>
                                    </div>
                                @endif

                                <div class="prose prose-sm dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                    {!! nl2br(e($activeArticle->content)) !!}
                                </div>

                                @php
                                    $assignment = $assignments->firstWhere('education_article_id', $activeArticle->id);
                                @endphp
                                @if ($assignment && ! $assignment->acknowledged_at)
                                    <div class="mt-8 border-t border-slate-100 dark:border-zinc-800 pt-6 flex items-center gap-4">
                                        <button
                                            wire:click="acknowledge({{ $assignment->id }})"
                                            class="px-6 py-2.5 bg-trust-navy text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
                                        >
                                            <span class="material-symbols-outlined text-base">check</span>
                                            {{ __('I have read this article') }}
                                        </button>
                                        <p class="text-xs text-zinc-400">{{ __('Your care team will be notified that you have acknowledged this resource.') }}</p>
                                    </div>
                                @elseif ($assignment && $assignment->acknowledged_at)
                                    <div class="mt-8 border-t border-slate-100 dark:border-zinc-800 pt-6 flex items-center gap-2 text-green-600">
                                        <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        <span class="text-sm font-semibold">{{ __('Acknowledged on :date', ['date' => $assignment->acknowledged_at->format('M j, Y')]) }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl py-24">
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-zinc-600 block mb-3">menu_book</span>
                                    <p class="text-zinc-400 text-sm">{{ __('Select an article to read') }}</p>
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
