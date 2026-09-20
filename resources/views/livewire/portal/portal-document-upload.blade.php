<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Upload Documents') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Securely upload documents to your record.') }}</p>
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

            <!-- Upload Form -->
            <div class="bg-white border border-slate-200 rounded-2xl p-8 mb-6">
                <h2 class="font-semibold text-slate-800 mb-6">{{ __('Submit a Document') }}</h2>

                <div class="space-y-5">
                    <!-- File drop zone -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{{ __('File') }} <span class="text-red-500">*</span></label>
                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-600 transition-colors bg-slate-50">
                            @if ($file)
                                <span class="material-symbols-outlined text-3xl text-green-500 mb-2" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <p class="text-sm font-semibold text-slate-700 ">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
                            @else
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">cloud_upload</span>
                                <p class="text-sm text-slate-500">{{ __('Click to choose a file or drag & drop') }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ __('PDF, Word, JPG, PNG, TIFF — max 20 MB') }}</p>
                            @endif
                            <input type="file" wire:model="file" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.tiff">
                        </label>
                        @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{{ __('Document Category') }} <span class="text-red-500">*</span></label>
                        <select wire:model="category" class="w-full rounded-lg border border-slate-200 bg-white text-sm px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-trust-navy/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button
                        wire:click="upload"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        <span wire:loading wire:target="upload" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="upload" class="material-symbols-outlined text-base">upload</span>
                        {{ __('Upload Document') }}
                    </button>
                </div>
            </div>

            <!-- Recent Uploads -->
            @if ($recentUploads->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 ">
                        <h3 class="font-semibold text-slate-800 text-sm">{{ __('My Uploaded Documents') }}</h3>
                    </div>
                    @foreach ($recentUploads as $doc)
                        <div class="flex items-center gap-3 px-6 py-3 border-b border-slate-50 last:border-0">
                            <span class="material-symbols-outlined text-slate-400 text-xl">insert_drive_file</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $doc->original_name }}</p>
                                <p class="text-xs text-slate-500">{{ $doc->category }} · {{ $doc->created_at->format('M j, Y') }}</p>
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full bg-blue-100 text-blue-700">{{ __('Received') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
</div>
