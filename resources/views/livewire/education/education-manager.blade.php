<flux:main class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ __('Education Library') }}</flux:heading>
            <flux:subheading>{{ __('Manage educational articles and resources for patients.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">{{ __('New Article') }}</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
            <flux:icon.check-circle variant="solid" class="w-5 h-5" />
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Category') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($articles as $article)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-medium text-slate-800">{{ $article->title }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="capitalize text-slate-600">{{ $article->category }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <button wire:click="togglePublish({{ $article->id }})" class="focus:outline-none">
                                @if ($article->is_published)
                                    <flux:badge color="green" size="sm" icon="check-circle">{{ __('Published') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="eye-slash">{{ __('Draft') }}</flux:badge>
                                @endif
                            </button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $article->id }})" />
                                <flux:button size="sm" variant="ghost" icon="trash" class="text-red-600 hover:text-red-700 hover:bg-red-50" wire:click="confirmDelete({{ $article->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8 text-slate-500">
                            {{ __('No educational articles found.') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-slate-100">
            {{ $articles->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <flux:modal wire:model="isModalOpen" class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? __('Edit Article') : __('New Article') }}</flux:heading>
                <flux:subheading>{{ __('Fill out the details for the educational resource.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="title" :label="__('Article Title')" placeholder="{{ __('e.g., Understanding Hypertension') }}" required />
                
                <flux:select wire:model="category" :label="__('Category')" required>
                    <option value="diagnosis">{{ __('Diagnosis') }}</option>
                    <option value="medication">{{ __('Medication') }}</option>
                    <option value="general">{{ __('General') }}</option>
                </flux:select>

                <flux:textarea wire:model="content" :label="__('Content (Markdown supported)')" rows="6" required />

                <flux:input wire:model="video_url" type="url" :label="__('Video Embed URL (Optional)')" placeholder="{{ __('e.g., https://www.youtube.com/embed/...') }}" />

                <flux:switch wire:model="is_published" :label="__('Publish immediately')" />
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Save Article') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="isDeleteModalOpen" class="max-w-sm">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Article') }}</flux:heading>
                <flux:subheading>{{ __('Are you sure you want to delete this article? This action cannot be undone.') }}</flux:subheading>
            </div>
            
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('isDeleteModalOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</flux:main>
