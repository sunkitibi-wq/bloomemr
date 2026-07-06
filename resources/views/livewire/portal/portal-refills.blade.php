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
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.refills') }}">
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
                    {{ __('Prescription Refills') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('View active medications and request renewal refills') }}
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
        <main class="flex-1 p-8 overflow-y-auto space-y-8">
            @if ($patient)
                <div class="flex justify-between items-center">
                    <h3 class="font-headline-md text-trust-navy dark:text-zinc-100 text-base font-semibold">
                        {{ __('Medications for :name', ['name' => $patient->full_name]) }}
                    </h3>
                    <flux:button variant="primary" icon="plus" wire:click="openRequestForm">
                        {{ __('Request Refill') }}
                    </flux:button>
                </div>

                @if (session()->has('message'))
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Active Medications Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
                        <flux:heading size="lg">{{ __('Active Medications') }}</flux:heading>
                        
                        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse ($medications as $med)
                                <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-trust-navy dark:text-zinc-100">{{ $med->name }}</p>
                                        <p class="text-xs text-zinc-450 mt-1">
                                            {{ $med->dose }} • {{ $med->frequency }}
                                        </p>
                                        <p class="text-[10px] text-zinc-400 mt-0.5">
                                            {{ __('Prescriber: :presc', ['presc' => $med->prescriber?->name ?? __('Unknown')]) }}
                                        </p>
                                    </div>
                                    <flux:button variant="ghost" size="sm" wire:click="$set('medicationId', {{ $med->id }}); openRequestForm();" class="text-primary font-semibold">
                                        {{ __('Request Refill') }}
                                    </flux:button>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400 py-8 italic text-center">{{ __('No active medications found in EMR.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Refill Request History -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
                        <flux:heading size="lg">{{ __('Refill Request History') }}</flux:heading>
                        
                        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse ($refillRequests as $req)
                                <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-trust-navy dark:text-zinc-100">{{ $req->medication->name }}</p>
                                        <p class="text-xs text-zinc-400 mt-0.5">
                                            {{ __('Requested on: :date', ['date' => $req->created_at->format('M j, Y')]) }}
                                        </p>
                                        @if ($req->notes)
                                            <p class="text-xs text-zinc-450 mt-1.5 bg-zinc-50 dark:bg-zinc-800/40 p-2 rounded-lg italic">
                                                "{{ $req->notes }}"
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-305',
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-305',
                                                'denied' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-305',
                                            ];
                                            $color = $statusColors[$req->status] ?? 'bg-zinc-100 text-zinc-800';
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $color }}">
                                            {{ __(ucfirst($req->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400 py-8 italic text-center">{{ __('No refill requests submitted yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </main>
    </div>

    <!-- Request Refill Form Modal -->
    <flux:modal name="portal-refill-modal" wire:model="isFormOpen" class="max-w-md">
        <form wire:submit.prevent="submitRequest" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Request Medication Refill') }}</flux:heading>
                <flux:subheading>{{ __('Submit a renewal request for an active medication') }}</flux:subheading>
            </div>

            <flux:select wire:model="medicationId" :label="__('Select Medication')" required>
                <option value="">{{ __('Select Active Medication...') }}</option>
                @foreach ($medications as $med)
                    <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->dose }})</option>
                @endforeach
            </flux:select>
            <flux:error name="medicationId" />

            <flux:textarea wire:model="notes" :label="__('Pharmacy details or notes')" rows="3" placeholder="{{ __('e.g., Send to CVS on 5th Ave, or any new dosage notes...') }}" />
            <flux:error name="notes" />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button variant="ghost" wire:click="$set('isFormOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Submit Request') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
