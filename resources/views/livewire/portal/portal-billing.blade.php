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
            <a class="flex items-center gap-3 px-4 py-2.5 text-trust-navy dark:text-zinc-100 font-semibold bg-slate-100 dark:bg-zinc-800 rounded-lg" href="{{ route('portal.billing') }}">
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
                    {{ __('Billing & Financial Statements') }}
                </h1>
                <p class="text-xs text-zinc-400">
                    {{ __('View invoice balances, service details, and complete mock payments') }}
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
        <main class="flex-1 p-8 overflow-y-auto">
            @if ($patient)
                <h3 class="font-headline-md text-trust-navy dark:text-zinc-100 text-base font-semibold mb-6">
                    {{ __('Billing Ledger for :name', ['name' => $patient->full_name]) }}
                </h3>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Invoices List -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Invoice ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Due Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Services Included') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount Due') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($invoices as $inv)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-900 dark:text-zinc-100 font-mono">
                                        #INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $inv->due_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                                        {{ implode(', ', array_column($inv->cpt_codes, 'description')) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100 font-bold font-mono">
                                        ${{ number_format($inv->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-zinc-105 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'void' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            ];
                                            $color = $statusColors[$inv->status] ?? 'bg-zinc-105 text-zinc-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $color }}">
                                            {{ __(ucfirst($inv->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-1">
                                        <flux:button size="sm" variant="outline" wire:click="viewDetails({{ $inv->id }})">
                                            {{ __('View Details') }}
                                        </flux:button>
                                         @if ($inv->status !== 'paid')
                                             <flux:button size="sm" variant="primary" class="bg-green-600 hover:bg-green-700 text-white font-bold" wire:click="openPaymentModal({{ $inv->id }})">
                                                 {{ __('Pay Now') }}
                                             </flux:button>
                                         @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-400">
                                        {{ __('No invoices found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-zinc-400 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
        </main>
    </div>

    <!-- Invoice Details Modal -->
    <flux:modal name="portal-invoice-details-modal" wire:model="isDetailOpen" class="max-w-md">
        <div class="space-y-4">
            @if ($selectedInvoice)
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">{{ __('Invoice Summary') }}</flux:heading>
                        <flux:subheading class="font-mono mt-0.5">#INV-{{ str_pad($selectedInvoice->id, 5, '0', STR_PAD_LEFT) }}</flux:subheading>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-zinc-400 uppercase tracking-wider block">{{ __('Invoice Status') }}</span>
                        @php
                            $statusColors = [
                                'draft' => 'text-zinc-600 font-semibold',
                                'pending' => 'text-amber-600 font-semibold',
                                'paid' => 'text-green-600 font-semibold',
                                'void' => 'text-red-600 font-semibold',
                            ];
                            $color = $statusColors[$selectedInvoice->status] ?? 'text-zinc-650';
                        @endphp
                        <span class="text-sm font-bold {{ $color }}">{{ __(ucfirst($selectedInvoice->status)) }}</span>
                    </div>
                </div>

                <flux:separator />

                <!-- Invoice Billing Details -->
                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">{{ __('Patient Name:') }}</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $selectedInvoice->patient->full_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">{{ __('Due Date:') }}</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $selectedInvoice->due_date->format('M j, Y') }}</span>
                    </div>
                </div>

                <flux:separator />

                <!-- CPT Items -->
                <div>
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block mb-2">{{ __('Services & CPT Codes') }}</span>
                    <div class="space-y-2">
                        @foreach ($selectedInvoice->cpt_codes as $cpt)
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-mono bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-xs mr-2 font-bold text-zinc-700 dark:text-zinc-300">{{ $cpt['code'] }}</span>
                                    <span class="text-zinc-800 dark:text-zinc-200 font-medium">{{ $cpt['description'] }}</span>
                                </div>
                                <span class="font-mono text-zinc-900 dark:text-zinc-100 font-semibold">${{ number_format($cpt['fee'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <flux:separator />

                <!-- Total Summary -->
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-zinc-500">{{ __('Total Due Amount:') }}</span>
                    <span class="font-mono font-bold text-lg text-zinc-900 dark:text-zinc-100">${{ number_format($selectedInvoice->total_amount, 2) }}</span>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <flux:button variant="ghost" wire:click="$set('isDetailOpen', false)">{{ __('Close') }}</flux:button>
                    @if ($selectedInvoice->status !== 'paid')
                        <flux:button variant="primary" class="bg-green-600 hover:bg-green-700 text-white font-bold" wire:click="openPaymentModal({{ $selectedInvoice->id }})">
                            {{ __('Process Payment') }}
                        </flux:button>
                    @endif
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Payment Simulator Modal -->
    <flux:modal name="portal-payment-modal" wire:model="isPaymentModalOpen" class="max-w-md">
        <form wire:submit.prevent="processPayment" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Complete Bill Payment') }}</flux:heading>
                <flux:subheading>{{ __('Complete credit card checkout via Stripe') }}</flux:subheading>
            </div>

            @error('payment_error')
                <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg text-sm font-semibold flex items-start gap-2">
                    <span class="material-symbols-outlined text-sm mt-0.5" style="font-variation-settings: 'FILL' 1;">error</span>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            @if ($invoiceToPay)
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl flex justify-between items-center text-sm mb-2">
                    <span class="text-zinc-500 font-semibold">#INV-{{ str_pad($invoiceToPay->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($invoiceToPay->total_amount, 2) }}</span>
                </div>
            @endif

            <flux:radio.group wire:model="paymentGateway" label="Select Payment Gateway">
                <flux:radio value="stripe" label="Stripe (Credit Card)" />
                <flux:radio value="paystack" label="Paystack (Credit Card)" />
            </flux:radio.group>

            <flux:input wire:model="cardNumber" mask="4444-4444-4444-4444" :label="__('Card Number')" placeholder="4111 2222 3333 4444" required />
            <flux:error name="cardNumber" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="cardExpiry" mask="99/99" :label="__('Expiration Date')" placeholder="MM/YY" required />
                <flux:input wire:model="cardCvc" mask="999" :label="__('CVC / CVV')" placeholder="123" required />
            </div>
            <flux:error name="cardExpiry" />
            <flux:error name="cardCvc" />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button variant="ghost" wire:click="$set('isPaymentModalOpen', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold">{{ __('Submit Payment') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
