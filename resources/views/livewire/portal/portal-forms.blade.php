<div class="min-h-screen bg-slate-50 dark:bg-zinc-950 flex flex-col md:flex-row">
    <!-- Sidebar Navigation -->
    <nav class="w-full md:w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col p-6 shrink-0">
        <!-- Logo -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white font-bold">
                B
            </div>
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
            <a class="flex items-center gap-3 px-4 py-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors" href="{{ route('portal.billing') }}">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-sm">{{ __('Billing & Invoices') }}</span>
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
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.forms') }}">
                <span class="material-symbols-outlined text-xl">description</span>
                <span class="text-sm">{{ __('Intake & Consents') }}</span>
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

        <!-- CTA & Exit -->
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-zinc-800 space-y-3">
            <button class="w-full py-2.5 px-4 bg-status-critical text-white text-xs font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">emergency</span>
                <span>{{ __('Emergency Contact') }}</span>
            </button>
            
            <a class="flex items-center gap-3 px-4 py-2 text-zinc-500 dark:text-zinc-400 hover:text-trust-navy dark:hover:text-zinc-100 transition-colors" href="{{ route('dashboard') }}" wire:navigate>
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                <span class="text-xs">{{ __('Back to EMR') }}</span>
            </a>
        </div>
    </nav>

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-20 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-800 flex justify-between items-center px-8">
            <div>
                <h1 class="font-headline-md font-bold text-trust-navy dark:text-zinc-100 text-lg">
                    {{ __('Intake & Consent Forms') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('Review, complete, and electronically sign clinical documents') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-trust-navy dark:text-zinc-100">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-wider">{{ __('Primary Guardian') }}</p>
                </div>
                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                    {{ auth()->user()->initials() }}
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 md:p-8 flex flex-col overflow-y-auto space-y-8">
            @if (session()->has('message'))
                <div class="p-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-800 dark:text-green-300 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            @if ($patient)
                <!-- Pending Forms Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 space-y-4">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        {{ __('Pending Documents to Complete') }}
                    </flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($pendingForms as $pf)
                            <div class="border rounded-xl border-zinc-200 dark:border-zinc-800 p-4 bg-zinc-50 dark:bg-zinc-850/50 flex flex-col justify-between" wire:key="pending-card-{{ $pf->id }}">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <flux:heading size="md">{{ $pf->template->title }}</flux:heading>
                                        <flux:badge size="sm" color="amber">{{ __('Required') }}</flux:badge>
                                    </div>
                                    <flux:text class="text-xs mt-2 line-clamp-2">{{ $pf->template->description }}</flux:text>
                                </div>
                                <div class="mt-4 pt-4 border-t border-zinc-200/60 dark:border-zinc-700/60 flex justify-between items-center">
                                    <span class="text-[10px] text-zinc-400 font-mono">{{ __('Assigned: :date', ['date' => $pf->created_at->format('M j, Y')]) }}</span>
                                    <flux:button variant="primary" size="sm" wire:click="openForm({{ $pf->id }})">
                                        {{ __('Complete & Sign') }}
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-zinc-400 italic text-sm">
                                <span class="material-symbols-outlined text-2xl block mb-1">done_all</span>
                                {{ __('All assigned forms are complete! No pending forms.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Forms Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 space-y-4">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        {{ __('Signed Documents & Agreements') }}
                    </flux:heading>

                    <div class="border rounded-xl border-zinc-200 dark:border-zinc-800 overflow-hidden">
                        <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3.5">{{ __('Document') }}</th>
                                    <th class="px-6 py-3.5">{{ __('E-Signed By') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Signed At') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                                @forelse ($completedForms as $cf)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="completed-row-{{ $cf->id }}">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $cf->template->title }}</div>
                                            <div class="text-xs text-zinc-400 mt-0.5 line-clamp-1">{{ $cf->template->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono text-zinc-700 dark:text-zinc-300">
                                            {{ $cf->signature_name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-zinc-400 font-mono">
                                            {{ $cf->signed_at?->format('M j, Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-zinc-400 italic text-sm">
                                            {{ __('No signed documents on file yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-850 rounded-2xl">
                    {{ __('No patient records associated with this portal account.') }}
                </div>
            @endif
        </main>
    </div>

    <!-- Interactive Form Modal Overlay -->
    @if ($isFormOpen && $selectedForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-xl bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                <div>
                    <flux:heading size="lg">{{ $selectedForm->template->title }}</flux:heading>
                    <flux:text class="text-xs mt-1">{{ $selectedForm->template->description }}</flux:text>
                </div>

                <form wire:submit.prevent="submitForm" class="space-y-6">
                    <!-- Dynamic Fields -->
                    @if (is_array($selectedForm->template->fields))
                        @foreach ($selectedForm->template->fields as $field)
                            <div class="space-y-1.5" wire:key="field-{{ $field['name'] }}">
                                @if (in_array($field['type'], ['checkbox', 'yes_no'], true))
                                    <div class="space-y-2">
                                        <label class="flex items-start gap-2.5 text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="responses.{{ $field['name'] }}"
                                                class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary mt-0.5"
                                            />
                                            <span>
                                                {{ $field['label'] }}
                                                @if (isset($field['required']) && $field['required'])
                                                    <span class="text-red-500 font-bold">*</span>
                                                @endif
                                            </span>
                                        </label>
                                        @if ($field['type'] === 'yes_no')
                                            <div class="flex items-center gap-2 text-[11px] text-zinc-400">
                                                <span>{{ __('Yes') }}</span>
                                                <span>/</span>
                                                <span>{{ __('No') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @error('responses.' . $field['name'])
                                        <span class="text-xs text-red-500 block">{{ $message }}</span>
                                    @enderror
                                @else
                                    <flux:field>
                                        <flux:label>
                                            {{ $field['label'] }}
                                            @if (isset($field['required']) && $field['required'])
                                                <span class="text-red-500 font-bold">*</span>
                                            @endif
                                        </flux:label>
                                        <flux:input
                                            wire:model="responses.{{ $field['name'] }}"
                                            placeholder="Enter response"
                                        />
                                        @error('responses.' . $field['name'])
                                            <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                        @enderror
                                    </flux:field>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <!-- E-Signature Section -->
                    <div class="border-t border-zinc-200 dark:border-zinc-800 pt-5 space-y-4">
                        <div>
                            <flux:heading size="md">{{ __('Electronic Signature') }}</flux:heading>
                            <flux:text class="text-xs mt-1">
                                {{ __('By typing your full legal name below, you certify that you are the legal representative/guardian authorized to sign this agreement.') }}
                            </flux:text>
                        </div>

                        <flux:field>
                            <flux:label>{{ __('Legal Signature (Type Full Name)') }}</flux:label>
                            <flux:input
                                wire:model="signature"
                                placeholder="e.g. Johnathan Doe"
                                class="font-sans font-medium"
                                required
                            />
                            <flux:error name="signature" />
                        </flux:field>

                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 text-[10px] text-zinc-400 font-mono space-y-1">
                            <div>{{ __('Date/Time: :date', ['date' => now()->format('Y-m-d H:i:s T')]) }}</div>
                            <div>{{ __('IP Address: :ip', ['ip' => request()->ip() ?? '127.0.0.1']) }}</div>
                            <div>{{ __('Legal Standard: Uniform Electronic Transactions Act (UETA) compliant') }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <flux:button type="button" wire:click="$set('isFormOpen', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ __('Sign & Submit Form') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
