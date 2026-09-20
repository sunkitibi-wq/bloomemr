<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Billing & Financial Statements') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('View invoice balances, service details, and complete mock payments') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <h3 class="text-slate-800 text-base font-semibold mb-6">
                    {{ __('Billing Ledger for :name', ['name' => $patient->full_name]) }}
                </h3>

                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Invoices List -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="min-w-full divide-y divide-slate-200 ">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Invoice ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Due Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Services Included') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Amount Due') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 ">
                            @forelse ($invoices as $inv)
                                <tr class="hover:bg-slate-50/50 :bg-slate-100/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono">
                                        #INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 ">
                                        {{ $inv->due_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">
                                        {{ implode(', ', array_column($inv->cpt_codes, 'description')) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold font-mono">
                                        ${{ number_format($inv->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-slate-100 text-slate-800 ',
                                                'pending' => 'bg-amber-100 text-amber-800 ',
                                                'paid' => 'bg-green-100 text-green-800 ',
                                                'void' => 'bg-red-100 text-red-800 ',
                                            ];
                                            $color = $statusColors[$inv->status] ?? 'bg-slate-100 text-slate-800';
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
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        {{ __('No invoices found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
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
                        <span class="text-xs text-slate-500 uppercase tracking-wider block">{{ __('Invoice Status') }}</span>
                        @php
                            $statusColors = [
                                'draft' => 'text-slate-600 font-semibold',
                                'pending' => 'text-amber-600 font-semibold',
                                'paid' => 'text-green-600 font-semibold',
                                'void' => 'text-red-600 font-semibold',
                            ];
                            $color = $statusColors[$selectedInvoice->status] ?? 'text-slate-600';
                        @endphp
                        <span class="text-sm font-bold {{ $color }}">{{ __(ucfirst($selectedInvoice->status)) }}</span>
                    </div>
                </div>

                <flux:separator />

                <!-- Invoice Billing Details -->
                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">{{ __('Patient Name:') }}</span>
                        <span class="font-semibold text-slate-800 ">{{ $selectedInvoice->patient->full_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">{{ __('Due Date:') }}</span>
                        <span class="font-semibold text-slate-800 ">{{ $selectedInvoice->due_date->format('M j, Y') }}</span>
                    </div>
                </div>

                <flux:separator />

                <!-- CPT Items -->
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">{{ __('Services & CPT Codes') }}</span>
                    <div class="space-y-2">
                        @foreach ($selectedInvoice->cpt_codes as $cpt)
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-mono bg-slate-100 px-1 py-0.5 rounded text-xs mr-2 font-bold text-slate-700 ">{{ $cpt['code'] }}</span>
                                    <span class="text-slate-800 font-medium">{{ $cpt['description'] }}</span>
                                </div>
                                <span class="font-mono text-slate-900 font-semibold">${{ number_format($cpt['fee'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <flux:separator />

                <!-- Total Summary -->
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-slate-500">{{ __('Total Due Amount:') }}</span>
                    <span class="font-mono font-bold text-lg text-slate-900 ">${{ number_format($selectedInvoice->total_amount, 2) }}</span>
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
                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm font-semibold flex items-start gap-2">
                    <span class="material-symbols-outlined text-sm mt-0.5" style="font-variation-settings: 'FILL' 1;">error</span>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            @if ($invoiceToPay)
                <div class="p-4 bg-slate-50 rounded-xl flex justify-between items-center text-sm mb-2">
                    <span class="text-slate-500 font-semibold">#INV-{{ str_pad($invoiceToPay->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="font-mono font-bold text-slate-900 ">${{ number_format($invoiceToPay->total_amount, 2) }}</span>
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
