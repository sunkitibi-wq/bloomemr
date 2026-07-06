<div class="mt-6 space-y-6">
    <div class="flex justify-between items-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl">
        <div>
            <flux:heading size="xl">{{ __('Care Coordination & Direct Messaging') }}</flux:heading>
            <flux:subheading>{{ __('Share standards-compliant FHIR R4 clinical summaries (CCD) with external providers via Direct Project secure messaging.') }}</flux:subheading>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" class="my-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger" class="my-4">
            {{ session('error') }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Sharing Logs -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6">
            <flux:heading size="lg" class="mb-4">{{ __('Clinical Sharing History') }}</flux:heading>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-left text-xs font-sans">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/50 text-zinc-500 font-semibold">
                        <tr>
                            <th class="px-4 py-3">{{ __('Recipient / Institution') }}</th>
                            <th class="px-4 py-3">{{ __('Direct Address') }}</th>
                            <th class="px-4 py-3">{{ __('Type / Scope') }}</th>
                            <th class="px-4 py-3">{{ __('Linked Consent') }}</th>
                            <th class="px-4 py-3">{{ __('Sent Date') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($messages as $msg)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-zinc-850 dark:text-zinc-100">
                                    {{ $msg->recipient_name }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500">
                                    {{ $msg->recipient_address }}
                                </td>
                                <td class="px-4 py-3">
                                    <flux:badge size="sm" color="slate">{{ $msg->scope }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    <span class="underline">{{ $msg->consent->template->title }}</span>
                                    <div class="text-[10px] text-zinc-400">Signed: {{ $msg->consent->completed_at?->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-550">
                                    {{ $msg->created_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button size="sm" variant="ghost" icon="eye" wire:click="viewPayload({{ $msg->id }})">
                                        {{ __('FHIR JSON') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-450 italic">
                                    {{ __('No records have been shared for this patient yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Share Form -->
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 flex flex-col justify-between">
            <form wire:submit.prevent="sendSummary" class="space-y-4">
                <flux:heading size="lg">{{ __('Share Clinical Record') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Recipient Name / Institution') }}</flux:label>
                    <flux:input type="text" wire:model="recipientName" placeholder="e.g. Dr. Jane Smith" />
                    <flux:error name="recipientName" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Recipient Direct Address') }}</flux:label>
                    <flux:input type="text" wire:model="recipientAddress" placeholder="e.g. jsmith@direct.health.org" />
                    <flux:error name="recipientAddress" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Subject') }}</flux:label>
                    <flux:input type="text" wire:model="subject" />
                    <flux:error name="subject" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Document Scope') }}</flux:label>
                    <flux:select wire:model="scope">
                        <flux:select.option value="CCD">{{ __('Continuity of Care Document (CCD)') }}</flux:select.option>
                        <flux:select.option value="referral">{{ __('Referral Note') }}</flux:select.option>
                        <flux:select.option value="care_plan">{{ __('Care Plan') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="scope" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Patient Consent Form (ROI)') }}</flux:label>
                    <flux:select wire:model="consentFormId" placeholder="Select Signed Consent">
                        @foreach ($consents as $c)
                            <flux:select.option value="{{ $c->id }}">
                                {{ $c->template->title }} (Signed: {{ $c->completed_at?->format('Y-m-d') }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="consentFormId" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Authorization Expiry (Optional)') }}</flux:label>
                    <flux:input type="date" wire:model="expiresAt" />
                    <flux:error name="expiresAt" />
                </flux:field>

                <div class="pt-4 flex justify-end">
                    <flux:button type="submit" variant="primary" icon="paper-airplane">
                        {{ __('Transmit Record') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payload Viewer Modal -->
    <flux:modal wire:model="showPayloadModal" class="md:w-[700px] lg:w-[900px]">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('FHIR R4 Composition payload') }}</flux:heading>
                <flux:subheading>{{ __('Continuity of Care Document JSON representation transmitted to the external HISP.') }}</flux:subheading>
            </div>
            
            @if ($selectedMessagePayload)
                <pre class="bg-zinc-950 dark:bg-black text-emerald-400 p-4 rounded-xl overflow-x-auto text-xs font-mono max-h-[500px]"><code>{{ json_encode($selectedMessagePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            @endif

            <div class="flex justify-end pt-2">
                <flux:button wire:click="closePayloadModal">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
