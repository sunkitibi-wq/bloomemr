<flux:main class="space-y-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Claims Center</flux:heading>
            <flux:subheading>Batch, review, and submit X12 837P insurance claims.</flux:subheading>
        </div>
        
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <flux:button wire:click="batchSubmit" variant="primary" icon="paper-airplane">
                Submit Selected Claims
            </flux:button>
        </div>
    </div>

    <flux:card>
        <div class="mb-6 flex justify-between items-end">
            <flux:field class="w-64">
                <flux:label>Filter Status</flux:label>
                <flux:select wire:model.live="filterStatus">
                    <option value="unsubmitted">Unsubmitted</option>
                    <option value="submitted">Submitted</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </flux:select>
            </flux:field>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800/50 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="p-4 w-4">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th scope="col" class="px-4 py-3">Reference</th>
                        <th scope="col" class="px-4 py-3">Patient</th>
                        <th scope="col" class="px-4 py-3">Date of Service</th>
                        <th scope="col" class="px-4 py-3">Amount</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        <tr class="border-b dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="p-4">
                                <flux:checkbox wire:model.live="selectedInvoices" value="{{ $claim->id }}" />
                            </td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                <a href="{{ route('billing.claims.show', $claim) }}" class="hover:underline">
                                    {{ $claim->claim_reference ?? ('INV-'.str_pad($claim->id, 5, '0', STR_PAD_LEFT)) }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('patients.show', $claim->patient_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $claim->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                {{ $claim->encounter ? $claim->encounter->created_at->format('M d, Y') : $claim->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                ${{ number_format($claim->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($claim->insurance_claim_status === 'unsubmitted')
                                    <flux:badge color="zinc">Unsubmitted</flux:badge>
                                @elseif($claim->insurance_claim_status === 'submitted')
                                    <flux:badge color="blue">Submitted</flux:badge>
                                @elseif($claim->insurance_claim_status === 'accepted')
                                    <flux:badge color="green">Accepted</flux:badge>
                                @elseif($claim->insurance_claim_status === 'rejected')
                                    <flux:badge color="red">Rejected</flux:badge>
                                @else
                                    <flux:badge color="zinc">{{ ucfirst($claim->insurance_claim_status) }}</flux:badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                                No claims found matching the selected status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $claims->links() }}
        </div>
    </flux:card>
</flux:main>
