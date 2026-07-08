<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-base font-bold text-trust-navy dark:text-zinc-100">{{ __('Education Library') }}</h2>
            <p class="text-xs text-zinc-400 mt-0.5">{{ __('Assign curated articles to this patient\'s portal.') }}</p>
        </div>
        <button
            wire:click="$toggle('showArticleForm')"
            class="flex items-center gap-2 px-4 py-2 bg-trust-navy text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity"
        >
            <span class="material-symbols-outlined text-sm">add</span>
            {{ __('New Article') }}
        </button>
    </div>

    <!-- New Article Form -->
    @if ($showArticleForm)
        <div class="bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-xl p-6 mb-6">
            <h3 class="font-semibold text-sm text-trust-navy dark:text-zinc-100 mb-4">{{ __('Create Education Article') }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">{{ __('Title') }}</label>
                    <input wire:model="newTitle" type="text" class="w-full border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-trust-navy/20">
                    @error('newTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">{{ __('Category') }}</label>
                    <select wire:model="newCategory" class="w-full border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-trust-navy/20">
                        <option value="diagnosis">{{ __('Diagnosis') }}</option>
                        <option value="medication">{{ __('Medication') }}</option>
                        <option value="general">{{ __('General') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">{{ __('Video URL') }} <span class="text-zinc-300 font-normal">(optional)</span></label>
                    <input wire:model="newVideoUrl" type="url" placeholder="https://youtube.com/..." class="w-full border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-trust-navy/20">
                    @error('newVideoUrl') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">{{ __('Content') }}</label>
                    <textarea wire:model="newContent" rows="6" class="w-full border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-trust-navy/20 resize-y"></textarea>
                    @error('newContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3">
                    <button wire:click="createArticle" class="px-5 py-2 bg-trust-navy text-white text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity">{{ __('Publish Article') }}</button>
                    <button wire:click="$set('showArticleForm', false)" class="px-5 py-2 bg-slate-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-lg hover:opacity-90 transition-opacity">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assigned Articles -->
    @if ($assignments->isNotEmpty())
        <div class="mb-6">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-3">{{ __('Assigned to This Patient') }}</p>
            <div class="space-y-2">
                @foreach ($assignments as $assignment)
                    <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 flex items-center gap-3">
                        <span class="material-symbols-outlined text-indigo-500 text-xl">article</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100 truncate">{{ $assignment->article->title }}</p>
                            <p class="text-xs text-zinc-400 capitalize">{{ $assignment->article->category }} · {{ __('Assigned :date', ['date' => $assignment->created_at->format('M j, Y')]) }}</p>
                        </div>
                        @if ($assignment->acknowledged_at)
                            <span class="flex items-center gap-1 text-xs text-green-600 font-semibold">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                {{ __('Acknowledged') }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400">{{ __('Pending') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Library Grid -->
    <div>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-wide mb-3">{{ __('Education Library') }}</p>
        @forelse ($library as $article)
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-trust-navy dark:text-indigo-400 text-xl">
                    {{ $article->category === 'medication' ? 'medication' : ($article->category === 'diagnosis' ? 'psychiatry' : 'article') }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-trust-navy dark:text-zinc-100 truncate">{{ $article->title }}</p>
                    <p class="text-xs text-zinc-400 capitalize">{{ $article->category }}</p>
                </div>
                @if ($activeArticle?->id === $article->id)
                    <div class="text-xs text-zinc-500 max-w-xs line-clamp-2 hidden xl:block mr-3">{{ Str::limit($article->content, 80) }}</div>
                @endif
                <button
                    wire:click="assignArticle({{ $article->id }})"
                    class="px-3 py-1.5 text-xs font-semibold text-trust-navy border border-trust-navy/20 rounded-lg hover:bg-trust-navy hover:text-white transition-colors shrink-0"
                >
                    {{ __('Assign') }}
                </button>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-10 text-center text-zinc-400 text-sm">
                {{ __('No education articles exist yet. Create one above.') }}
            </div>
        @endforelse
    </div>
</div>
