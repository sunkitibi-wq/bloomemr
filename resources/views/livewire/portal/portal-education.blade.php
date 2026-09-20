<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Education Library') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Your care team will be notified that you have acknowledged this resource.') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
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
                                    class="px-3 py-1 rounded-full text-xs font-semibold border transition-colors {{ $selectedCategory === $cat ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-500 border-slate-200 hover:border-indigo-600' }}"
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
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                            @forelse ($articles as $article)
                                <button
                                    wire:click="selectArticle({{ $article->id }})"
                                    class="w-full text-left px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 :bg-slate-50 transition-colors flex items-start gap-3"
                                >
                                    <span class="material-symbols-outlined text-slate-800 text-base mt-0.5">
                                        {{ $article->category === 'medication' ? 'medication' : ($article->category === 'diagnosis' ? 'psychiatry' : 'article') }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $article->title }}</p>
                                        <p class="text-xs text-slate-500 capitalize">{{ $article->category }}</p>
                                    </div>
                                </button>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-500 text-sm">{{ __('No articles available yet.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Article Viewer -->
                    <div class="lg:col-span-2">
                        @if ($activeArticle)
                            <div class="bg-white border border-slate-200 rounded-2xl p-8">
                                <div class="flex items-start justify-between mb-6">
                                    <div>
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 capitalize mb-2">{{ $activeArticle->category }}</span>
                                        <h2 class="text-xl font-bold text-slate-800 ">{{ $activeArticle->title }}</h2>
                                    </div>
                                </div>

                                @if ($activeArticle->video_url)
                                    <div class="mb-6 aspect-video rounded-xl overflow-hidden bg-black">
                                        <iframe src="{{ $activeArticle->video_url }}" class="w-full h-full" allowfullscreen></iframe>
                                    </div>
                                @endif

                                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                                    {!! nl2br(e($activeArticle->content)) !!}
                                </div>

                                @php
                                    $assignment = $assignments->firstWhere('education_article_id', $activeArticle->id);
                                @endphp
                                @if ($assignment && ! $assignment->acknowledged_at)
                                    <div class="mt-8 border-t border-slate-100 pt-6 flex items-center gap-4">
                                        <button
                                            wire:click="acknowledge({{ $assignment->id }})"
                                            class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
                                        >
                                            <span class="material-symbols-outlined text-base">check</span>
                                            {{ __('I have read this article') }}
                                        </button>
                                        <p class="text-xs text-slate-500">{{ __('Your care team will be notified that you have acknowledged this resource.') }}</p>
                                    </div>
                                @elseif ($assignment && $assignment->acknowledged_at)
                                    <div class="mt-8 border-t border-slate-100 pt-6 flex items-center gap-2 text-green-600">
                                        <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        <span class="text-sm font-semibold">{{ __('Acknowledged on :date', ['date' => $assignment->acknowledged_at->format('M j, Y')]) }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center bg-white border border-slate-200 rounded-2xl py-24">
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">menu_book</span>
                                    <p class="text-slate-500 text-sm">{{ __('Select an article to read') }}</p>
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
