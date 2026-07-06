<div class="space-y-8">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Prescribing & Alerts Form Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('E-Prescribe Medication') }}</flux:heading>

                <form wire:submit="prescribe" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <flux:input 
                                wire:model.live="name" 
                                :label="__('Medication Name')" 
                                placeholder="e.g. Lexapro, Adderall, Ibuprofen" 
                                required 
                            />
                            @if (!empty($formularyDetails))
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-xs">
                                    <span class="font-medium text-zinc-500">{{ __('Formulary:') }}</span>
                                    <flux:badge size="sm" color="{{ $formularyDetails['pa_required'] ? 'amber' : 'green' }}">
                                        {{ $formularyDetails['status'] }} ({{ $formularyDetails['tier'] }})
                                    </flux:badge>
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $formularyDetails['copay'] }}</span>
                                    @if ($formularyDetails['pa_required'])
                                        <flux:badge size="sm" color="red" class="text-[9px] px-1 py-0">{{ __('PA Req') }}</flux:badge>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <flux:input 
                            wire:model="dose" 
                            :label="__('Dosage')" 
                            placeholder="e.g. 10mg, 5ml" 
                            required 
                        />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:input 
                            wire:model="frequency" 
                            :label="__('Frequency / Directions')" 
                            placeholder="e.g. Once daily, as needed" 
                            required 
                        />
                        <flux:input 
                            wire:model="ndc_code" 
                            :label="__('National Drug Code (NDC) (Optional)')" 
                            placeholder="e.g. 59148-008-13" 
                        />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="pharmacy_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Preferred Pharmacy') }}</label>
                            <select id="pharmacy_id" wire:model="pharmacyId" class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="">{{ __('Select pharmacy') }}</option>
                                @foreach ($this->pharmacies as $pharmacy)
                                    <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Clinical Warnings Block -->
                    @if ($allergyAlert || !empty($ddiAlerts))
                        <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/20 p-4 space-y-3">
                            <flux:heading size="sm" class="text-red-800 dark:text-red-200 flex items-center gap-1.5">
                                <flux:icon.exclamation-triangle class="size-4" />
                                {{ __('Clinical Decision Support Warnings') }}
                            </flux:heading>

                            @if ($allergyAlert)
                                <div class="text-sm font-medium text-red-700 dark:text-red-300">
                                    {{ $allergyAlert }}
                                </div>
                            @endif

                            @foreach ($ddiAlerts as $ddi)
                                <div class="text-sm text-red-600 dark:text-red-400">
                                    {{ $ddi['message'] }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <input id="is_controlled" type="checkbox" wire:model="is_controlled" class="rounded border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary" />
                        <flux:label for="is_controlled" class="cursor-pointer">{{ __('Controlled Substance (Requires EPCS 2FA signing)') }}</flux:label>
                    </div>

                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Transmit Prescription') }}
                    </flux:button>
                </form>
            </div>

            <!-- Active EMR Medications List -->
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Active Medications') }}</flux:heading>

                <div class="border rounded-lg border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($patient->medications->where('status', 'active') as $med)
                        <div class="flex items-center justify-between p-4 gap-4">
                            <div>
                                <div class="font-semibold text-zinc-800 dark:text-zinc-200 text-sm">
                                    {{ $med->name }} {{ $med->dose }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $med->frequency }} &middot; Prescribed by {{ $med->prescriber?->name ?? __('Unknown') }} on {{ $med->created_at->format('M j, Y') }}
                                </div>
                            </div>
                            <flux:button variant="ghost" size="sm" wire:click="discontinue({{ $med->id }})" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20">
                                {{ __('Discontinue') }}
                            </flux:button>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-zinc-500">
                            {{ __('No active medications on file.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Pharmacy Fulfillment') }}</flux:heading>

                <div class="space-y-3">
                    @forelse ($patient->prescriptions->sortByDesc('created_at')->take(5) as $prescription)
                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div>
                                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                    {{ $prescription->medication?->name ?? __('Medication') }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $prescription->pharmacy?->name ?? __('No pharmacy selected') }} &middot; {{ ucfirst($prescription->fulfillment_status ?? 'sent') }}
                                </div>
                            </div>
                            <select wire:change="updatePrescriptionStatus({{ $prescription->id }}, $event.target.value)" class="rounded border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                <option value="sent" {{ ($prescription->fulfillment_status ?? 'sent') === 'sent' ? 'selected' : '' }}>{{ __('Sent') }}</option>
                                <option value="filled" {{ ($prescription->fulfillment_status ?? 'sent') === 'filled' ? 'selected' : '' }}>{{ __('Filled') }}</option>
                                <option value="rejected" {{ ($prescription->fulfillment_status ?? 'sent') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                            </select>
                        </div>
                    @empty
                        <div class="text-sm text-zinc-500">
                            {{ __('No prescriptions have been sent yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Surescripts Reconciliation List Column -->
        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700 bg-neutral-50 dark:bg-zinc-800">
                <flux:heading size="lg" class="mb-1">{{ __('External Med Reconciliation') }}</flux:heading>
                <flux:text class="mb-4 italic">{{ __('Verify and add patient-reported or pharmacy data.') }}</flux:text>

                <div class="space-y-4">
                    @forelse ($externalMeds as $index => $ext)
                        <flux:card class="p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <div class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ $ext['name'] }} {{ $ext['dose'] }}</div>
                                    <div class="text-xs text-zinc-500">{{ $ext['frequency'] }}</div>
                                    <span class="inline-block mt-2 rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 text-[10px] font-medium text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        {{ $ext['source'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2 justify-end">
                                <flux:button variant="ghost" size="sm" wire:click="dismissExternalMed({{ $index }})">
                                    {{ __('Dismiss') }}
                                </flux:button>
                                <flux:button variant="primary" size="sm" wire:click="reconcileMed({{ $index }})">
                                    {{ __('Add to EMR') }}
                                </flux:button>
                            </div>
                        </flux:card>
                    @empty
                        <p class="text-xs text-zinc-500 py-4 text-center">{{ __('No pharmacy records remaining to reconcile.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Medication Education & Teaching Logs -->
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700 bg-white dark:bg-zinc-900 space-y-4">
                <div>
                    <flux:heading size="lg">{{ __('Medication Education & Teaching') }}</flux:heading>
                    <flux:subheading class="text-xs">{{ __('Assign Lexicomp info sheets and track delivery logs.') }}</flux:subheading>
                </div>

                <!-- Available Sheets to Assign -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-zinc-500 block">{{ __('Assign New Sheet:') }}</span>
                    <div class="flex flex-col gap-1">
                        @foreach ($teachingSheets as $sheet)
                            <button 
                                type="button" 
                                wire:click="assignTeachingSheet('{{ $sheet }}', '{{ $name }}')" 
                                class="text-left text-xs bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 px-2 py-1.5 rounded border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center justify-between cursor-pointer"
                            >
                                <span>{{ $sheet }}</span>
                                <flux:icon.plus class="size-3 text-zinc-400" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Teaching Logs History -->
                <div class="space-y-2 border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    <span class="text-xs font-semibold text-zinc-500 block">{{ __('Teaching Logs History:') }}</span>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse ($patient->medicationTeachingLogs as $log)
                            <div class="p-2 border rounded border-zinc-100 dark:border-zinc-800 bg-neutral-50 dark:bg-zinc-800/40 text-xs">
                                <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $log->material_title }}</div>
                                <div class="text-[10px] text-zinc-500 mt-0.5">
                                    {{ __('For: :med', ['med' => $log->medication_name]) }} &middot; {{ __('By: :name', ['name' => $log->giver->name ?? 'System']) }}
                                </div>
                                <div class="flex justify-between items-center mt-1.5">
                                    <span class="text-[9px] text-zinc-400">{{ $log->created_at->format('M j, Y g:i A') }}</span>
                                    <flux:badge size="sm" color="sky">{{ ucfirst($log->status) }}</flux:badge>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 italic text-center py-2">{{ __('No education materials assigned yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EPCS 2FA controlled prescribing security modal -->
    @if ($showEpcsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('EPCS Controlled Substance Signing') }}</flux:heading>
                    <flux:text class="mt-1">
                        {{ __('NYS DEA Mandate: Enter your 2FA token code from Google Authenticator to authorize this Schedule II-V prescription.') }}
                    </flux:text>
                </div>

                <div class="space-y-4">
                    <flux:input 
                        wire:model="two_factor_code" 
                        :label="__('2FA Token Code')" 
                        placeholder="e.g. 123456" 
                        maxlength="6" 
                        required 
                    />
                </div>

                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('showEpcsModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" wire:click="signPrescription">
                        {{ __('Authorize & Transmit') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
