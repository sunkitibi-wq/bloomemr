<flux:main>
    <section class="w-full">
        @include('partials.settings-heading')

        <flux:heading class="sr-only">{{ __('Smart Phrases settings') }}</flux:heading>

        <x-settings.layout :heading="__('Smart Phrases')" :subheading="__('Manage clinical shortcuts and templates that expand inline when typing a dot (e.g. .adhd)')">
            @if ($showForm)
                <!-- Add / Edit Form -->
                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <flux:input 
                        wire:model="trigger" 
                        :label="__('Trigger Shortcut (without dot prefix)')" 
                        placeholder="e.g. adhd, depression, exam" 
                        type="text" 
                        required 
                        autofocus 
                        autocomplete="off" 
                    />

                    <flux:textarea 
                        wire:model="expansion" 
                        :label="__('Expanded Text')" 
                        rows="8" 
                        placeholder="{{ __('Type the text to replace the trigger...') }}" 
                        required 
                    />

                    <flux:input 
                        wire:model="category" 
                        :label="__('Category')" 
                        placeholder="e.g. Psychiatry, Demographics" 
                        type="text" 
                        autocomplete="off" 
                    />

                    @if (auth()->user()->isSuperAdmin())
                        <div class="flex items-center gap-3">
                            <input id="is_global" type="checkbox" wire:model="is_global" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                            <flux:label for="is_global" class="cursor-pointer">{{ __('Make Global (available to all providers)') }}</flux:label>
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                        <flux:button variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                    </div>
                </form>
            @else
                <!-- List View -->
                <div class="my-6 w-full space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <flux:heading size="lg">{{ __('My Shortcuts') }}</flux:heading>
                            <flux:subheading>{{ __('Your personal and global clinical shortcuts') }}</flux:subheading>
                        </div>
                        <flux:button variant="primary" size="sm" wire:click="create">
                            {{ __('New Phrase') }}
                        </flux:button>
                    </div>

                    <div class="border rounded-lg border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-900">
                        @forelse ($this->phrases as $phrase)
                            <div class="flex items-start justify-between p-4 {{ ! $loop->last ? 'border-b border-zinc-200 dark:border-zinc-700' : '' }} gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center rounded bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-semibold font-mono text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">
                                            .<span x-text="'{{ $phrase->trigger }}'"></span>
                                        </span>
                                        @if ($phrase->category)
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ $phrase->category }}</span>
                                        @endif
                                        @if ($phrase->is_global)
                                            <flux:badge size="sm" color="sky">{{ __('Global') }}</flux:badge>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2 leading-relaxed">{{ $phrase->expansion }}</p>
                                </div>
                                
                                <div class="flex items-center gap-2 shrink-0">
                                    @if ($phrase->is_global && !auth()->user()->isSuperAdmin())
                                        <!-- Read-only for standard users -->
                                    @else
                                        <flux:button variant="ghost" size="sm" wire:click="edit({{ $phrase->id }})">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button variant="ghost" size="sm" wire:click="delete({{ $phrase->id }})" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                                    <flux:icon.book-open-text class="size-7 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ __('No smart phrases yet') }}</p>
                                <flux:text class="mt-1">{{ __('Click New Phrase to create your first clinical shortcut.') }}</flux:text>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </x-settings.layout>
    </section>
</flux:main>
