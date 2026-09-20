<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Prescription Refills') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('View active medications and request renewal refills') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if ($patient)
                <div class="flex justify-between items-center">
                    <h3 class="text-slate-800 text-base font-semibold">
                        {{ __('Medications for :name', ['name' => $patient->full_name]) }}
                    </h3>
                    <flux:button variant="primary" icon="plus" wire:click="openRequestForm">
                        {{ __('Request Refill') }}
                    </flux:button>
                </div>

                @if (session()->has('message'))
                    <div class="p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Active Medications Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                        <flux:heading size="lg">{{ __('Active Medications') }}</flux:heading>
                        
                        <div class="divide-y divide-slate-100 ">
                            @forelse ($medications as $med)
                                <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 ">{{ $med->name }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $med->dose }} • {{ $med->frequency }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                            {{ __('Prescriber: :presc', ['presc' => $med->prescriber?->name ?? __('Unknown')]) }}
                                        </p>
                                    </div>
                                    <flux:button variant="ghost" size="sm" wire:click="$set('medicationId', {{ $med->id }}); openRequestForm();" class="text-primary font-semibold">
                                        {{ __('Request Refill') }}
                                    </flux:button>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-8 italic text-center">{{ __('No active medications found in EMR.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Refill Request History -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                        <flux:heading size="lg">{{ __('Refill Request History') }}</flux:heading>
                        
                        <div class="divide-y divide-slate-100 ">
                            @forelse ($refillRequests as $req)
                                <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 ">{{ $req->medication->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ __('Requested on: :date', ['date' => $req->created_at->format('M j, Y')]) }}
                                        </p>
                                        @if ($req->notes)
                                            <p class="text-xs text-slate-500 mt-1.5 bg-slate-50 p-2 rounded-lg italic">
                                                "{{ $req->notes }}"
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-800 ',
                                                'approved' => 'bg-green-100 text-green-800 ',
                                                'denied' => 'bg-red-100 text-red-800 ',
                                            ];
                                            $color = $statusColors[$req->status] ?? 'bg-slate-100 text-slate-800';
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $color }}">
                                            {{ __(ucfirst($req->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-8 italic text-center">{{ __('No refill requests submitted yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic">
                    {{ __('No patient records found.') }}
                </div>
            @endif
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
