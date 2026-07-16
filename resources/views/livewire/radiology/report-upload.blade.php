<form wire:submit="save" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <flux:textarea wire:model="findings" label="{{ __('Findings') }}" placeholder="Detailed radiologic findings..." rows="6" />
            
            <flux:textarea wire:model="impression" label="{{ __('Impression') }}" placeholder="Summary or impression of the study..." rows="4" />
        </div>
        
        <div class="space-y-6">
            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                <flux:heading size="sm" class="mb-4">{{ __('Upload Original Report') }}</flux:heading>
                
                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file-{{ $order->id }}" class="flex flex-col items-center justify-center w-full h-48 border-2 border-zinc-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 dark:border-zinc-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <flux:icon.arrow-up-tray class="w-8 h-8 mb-3 text-zinc-400" />
                            <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="font-semibold">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">PDF, JPG, or PNG (MAX. 10MB)</p>
                        </div>
                        <input id="dropzone-file-{{ $order->id }}" type="file" class="hidden" wire:model="attachment" accept=".pdf,image/jpeg,image/png" />
                    </label>
                </div>
                
                <div class="mt-2 text-sm text-zinc-500">
                    <div wire:loading wire:target="attachment" class="text-blue-500">
                        {{ __('Uploading...') }}
                    </div>
                    @if ($attachment)
                        <div class="text-green-500 font-medium mt-2 flex items-center gap-1">
                            <flux:icon.check-circle class="w-4 h-4" />
                            {{ $attachment->getClientOriginalName() }}
                        </div>
                    @endif
                    @error('attachment')
                        <div class="text-red-500 mt-2 flex items-center gap-1">
                            <flux:icon.exclamation-triangle class="w-4 h-4" />
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
        <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
        </flux:modal.close>
        <flux:button type="submit" variant="primary">{{ __('Save Report') }}</flux:button>
    </div>
</form>
