<flux:main>
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Billing Manager') }}</flux:heading>
            <flux:subheading>{{ __('Manage patient invoices, insurance claims, and CPT service codes') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreateForm">
            {{ __('New Invoice') }}
        </flux:button>
    </div>

    <!-- Alert / Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
        <flux:select wire:model.live="filterStatus" :label="__('Invoice Status')">
            <option value="">{{ __('All Invoices') }}</option>
            <option value="draft">{{ __('Draft') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="paid">{{ __('Paid') }}</option>
            <option value="void">{{ __('Void') }}</option>
        </flux:select>

        <flux:input type="search" wire:model.live="filterPatient" placeholder="{{ __('Search patient name or MRN...') }}" :label="__('Patient Search')" />
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Invoice ID') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Patient') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('CPT Codes') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Due Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Insurance Claim') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($invoices as $inv)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-900 dark:text-zinc-100 font-mono">
                            #INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                            <a href="{{ route('patients.show', $inv->patient) }}" class="hover:underline font-semibold text-primary">
                                {{ $inv->patient->full_name }}
                            </a>
                            <span class="text-xs text-zinc-400 block font-mono">{{ $inv->patient->mrn }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($inv->cpt_codes as $cpt)
                                    <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300 rounded font-mono" title="{{ $cpt['description'] }}">
                                        {{ $cpt['code'] }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                            {{ $inv->due_date->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100 font-bold font-mono">
                            ${{ number_format($inv->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'void' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $color = $statusColors[$inv->status] ?? 'bg-zinc-100 text-zinc-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $color }}">
                                {{ __(ucfirst($inv->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $claimColors = [
                                    'unsubmitted' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                    'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $claimColor = $claimColors[$inv->insurance_claim_status] ?? 'bg-zinc-100 text-zinc-800';
                            @endphp
                            <div class="space-y-1">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $claimColor }}">
                                    {{ __(ucfirst($inv->insurance_claim_status)) }}
                                </span>
                                @if ($inv->claim_reference)
                                    <div class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400">{{ $inv->claim_reference }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-1">
                            @if ($inv->status !== 'paid')
                                <flux:button size="sm" variant="outline" class="text-green-600 dark:text-green-400" wire:click="updateStatus({{ $inv->id }}, 'paid')">
                                    {{ __('Mark Paid') }}
                                </flux:button>
                            @endif

                            @if ($inv->insurance_claim_status === 'unsubmitted')
                                <flux:button size="sm" variant="outline" wire:click="submitClaim({{ $inv->id }})">
                                    {{ __('Submit Claim') }}
                                </flux:button>
                            @else
                                <flux:button size="sm" variant="outline" wire:click="viewEdiPayload({{ $inv->id }})">
                                    {{ __('View EDI') }}
                                </flux:button>
                            @endif

                            <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $inv->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" wire:confirm="{{ __('Are you sure you want to delete this invoice?') }}" wire:click="delete({{ $inv->id }})" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-zinc-400">
                            {{ __('No invoices found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if ($invoices->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Invoice Form Modal -->
    <flux:modal name="invoice-form-modal" wire:model="isFormOpen" class="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $invoiceId ? __('Edit Invoice') : __('New Invoice') }}</flux:heading>
                <flux:subheading>{{ __('Generate clinical invoice and configure billing details') }}</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="patientId" :label="__('Patient')" required>
                    <option value="">{{ __('Select Patient...') }}</option>
                    @foreach ($patients as $pat)
                        <option value="{{ $pat->id }}">{{ $pat->last_name }}, {{ $pat->first_name }} ({{ $pat->mrn }})</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="encounterId" :label="__('Encounter Reference (Optional)')">
                    <option value="">{{ __('No Encounter...') }}</option>
                    @foreach ($encounters as $enc)
                        <option value="{{ $enc->id }}">{{ $enc->patient->full_name }} - {{ $enc->encounter_date->format('M j, Y') }} ({{ $enc->status }})</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <flux:input type="date" wire:model="dueDate" :label="__('Due Date')" required />
                
                <flux:select wire:model="status" :label="__('Invoice Status')" required>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                    <option value="void">{{ __('Void') }}</option>
                </flux:select>

                <flux:select wire:model="claimStatus" :label="__('Claim Status')" required>
                    <option value="unsubmitted">{{ __('Unsubmitted') }}</option>
                    <option value="submitted">{{ __('Submitted') }}</option>
                    <option value="accepted">{{ __('Accepted') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </flux:select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="claimReference" :label="__('Claim Reference')" placeholder="e.g. CLM-00001" />
                <flux:input wire:model="claimNote" :label="__('Claim Notes')" placeholder="e.g. Awaiting payer response" />
            </div>

            <flux:separator />

            <!-- CPT Code Management -->
            <div>
                <flux:label>{{ __('Selected CPT Billing Codes') }}</flux:label>
                <div class="space-y-2 mt-2">
                    @forelse ($cptCodes as $index => $cpt)
                        <div class="flex items-center justify-between p-2.5 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-lg">
                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                <span class="font-mono bg-zinc-200 dark:bg-zinc-850 px-1.5 py-0.5 rounded text-xs mr-2 font-bold">{{ $cpt['code'] }}</span>
                                {{ $cpt['description'] }}
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold">${{ number_format($cpt['fee'], 2) }}</span>
                                <button type="button" class="text-red-500 hover:text-red-700" wire:click="removeCpt({{ $index }})">
                                    <flux:icon.trash class="size-4" />
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-400 italic">{{ __('No CPT billing codes selected yet. Add one below.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- CPT Selection / Adder -->
            <div class="bg-zinc-50 dark:bg-zinc-800/20 p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl space-y-4">
                <!-- Default CPT Fast Selection -->
                <div>
                    <flux:label>{{ __('Standard Service CPT Codes') }}</flux:label>
                    <div class="flex flex-wrap gap-2 mt-1.5">
                        @foreach ($defaultCpts as $def)
                            <button type="button" class="px-2 py-1.5 text-xs font-semibold bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-300 hover:bg-primary/5 rounded-lg transition-colors" wire:click="selectDefaultCpt('{{ $def['code'] }}')">
                                <span class="font-mono text-primary font-bold mr-1">{{ $def['code'] }}</span>
                                ${{ $def['fee'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <flux:separator />

                <!-- Custom CPT Code Entry -->
                <div class="grid grid-cols-3 gap-2 items-end">
                    <flux:input wire:model="newCptCode" placeholder="90837" :label="__('Custom Code')" />
                    <flux:input wire:model="newCptDesc" placeholder="{{ __('Psychotherapy 60 mins') }}" :label="__('Description')" />
                    <div class="flex gap-2">
                        <flux:input type="number" step="0.01" wire:model="newCptFee" placeholder="120.00" :label="__('Fee ($)')" class="flex-1" />
                        <flux:button type="button" variant="outline" class="mb-0.5" wire:click="addCustomCpt">
                            {{ __('Add') }}
                        </flux:button>
                    </div>
                </div>
                <flux:error name="cptCodes" />
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-zinc-200 dark:border-zinc-850">
                <div class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">
                    {{ __('Total Invoice Amount:') }}
                    <span class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 ml-2">${{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="flex space-x-2">
                    <flux:button variant="ghost" wire:click="$set('isFormOpen', false)">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- EDI Viewer Modal -->
    <flux:modal name="edi-viewer-modal" wire:model="showEdiModal" class="max-w-4xl">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Clearinghouse EDI 837 Claim File & Response') }}</flux:heading>
                @if ($ediInvoice)
                    <flux:subheading class="font-mono mt-0.5">#INV-{{ str_pad($ediInvoice->id, 5, '0', STR_PAD_LEFT) }} - {{ $ediInvoice->patient->full_name }}</flux:subheading>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- EDI Request -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-zinc-500 block">{{ __('Generated ANSI ASC X12 837P Transaction') }}</span>
                    <pre class="p-3 bg-zinc-950 text-emerald-400 rounded-lg text-[11px] font-mono overflow-auto max-h-[350px] leading-relaxed select-all">{{ $ediRequestPayload }}</pre>
                </div>

                <!-- EDI Response -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-zinc-500 block">{{ __('Simulated Clearinghouse Response (ANSI X12 997)') }}</span>
                    <pre class="p-3 bg-zinc-950 text-blue-400 rounded-lg text-[11px] font-mono overflow-auto max-h-[350px] leading-relaxed select-all">{{ $ediResponsePayload }}</pre>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button variant="ghost" wire:click="$set('showEdiModal', false)">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</flux:main>
