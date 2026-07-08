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
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.visit-summaries') }}"><span class="material-symbols-outlined text-xl">summarize</span><span class="text-sm">{{ __('Visit Summaries') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.education') }}"><span class="material-symbols-outlined text-xl">menu_book</span><span class="text-sm">{{ __('Education Library') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy font-semibold bg-slate-100 rounded-lg" href="{{ route('portal.documents') }}"><span class="material-symbols-outlined text-xl">upload_file</span><span class="text-sm">{{ __('Upload Documents') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.billing') }}"><span class="material-symbols-outlined text-xl">receipt_long</span><span class="text-sm">{{ __('Billing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.care-coordination') }}"><span class="material-symbols-outlined text-xl">share</span><span class="text-sm">{{ __('Care Sharing') }}</span></a>
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 hover:bg-slate-50 rounded-lg transition-colors" href="{{ route('portal.radiology') }}"><span class="material-symbols-outlined text-xl">biotech</span><span class="text-sm">{{ __('Imaging') }}</span></a>
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
            <span class="material-symbols-outlined text-trust-navy text-2xl">upload_file</span>
            <h1 class="font-bold text-trust-navy dark:text-zinc-100 text-lg">{{ __('Upload Documents') }}</h1>
        </header>

        <main class="flex-1 p-8 overflow-y-auto max-w-2xl mx-auto w-full">
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Upload Form -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-8 mb-6">
                <h2 class="font-semibold text-trust-navy dark:text-zinc-100 mb-6">{{ __('Submit a Document') }}</h2>

                <div class="space-y-5">
                    <!-- File drop zone -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-2">{{ __('File') }} <span class="text-red-500">*</span></label>
                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-trust-navy transition-colors bg-slate-50 dark:bg-zinc-800/50">
                            @if ($file)
                                <span class="material-symbols-outlined text-3xl text-green-500 mb-2" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-xs text-zinc-400 mt-1">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
                            @else
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">cloud_upload</span>
                                <p class="text-sm text-zinc-500">{{ __('Click to choose a file or drag & drop') }}</p>
                                <p class="text-xs text-zinc-400 mt-1">{{ __('PDF, Word, JPG, PNG, TIFF — max 20 MB') }}</p>
                            @endif
                            <input type="file" wire:model="file" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.tiff">
                        </label>
                        @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-2">{{ __('Document Category') }} <span class="text-red-500">*</span></label>
                        <select wire:model="category" class="w-full rounded-lg border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm px-3 py-2.5 text-zinc-700 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-trust-navy/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button
                        wire:click="upload"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-trust-navy text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        <span wire:loading wire:target="upload" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="upload" class="material-symbols-outlined text-base">upload</span>
                        {{ __('Upload Document') }}
                    </button>
                </div>
            </div>

            <!-- Recent Uploads -->
            @if ($recentUploads->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800">
                        <h3 class="font-semibold text-trust-navy dark:text-zinc-100 text-sm">{{ __('My Uploaded Documents') }}</h3>
                    </div>
                    @foreach ($recentUploads as $doc)
                        <div class="flex items-center gap-3 px-6 py-3 border-b border-slate-50 dark:border-zinc-800 last:border-0">
                            <span class="material-symbols-outlined text-slate-400 text-xl">insert_drive_file</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200 truncate">{{ $doc->original_name }}</p>
                                <p class="text-xs text-zinc-400">{{ $doc->category }} · {{ $doc->created_at->format('M j, Y') }}</p>
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full bg-blue-100 text-blue-700">{{ __('Received') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</div>
