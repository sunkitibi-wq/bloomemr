<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Intake & Consent Forms') }}</h1>
            <p class="text-[13px] text-slate-500 mt-1">{{ __('Review, complete, and electronically sign clinical documents') }}</p>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
            @if (session()->has('message'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            @if ($patient)
                <!-- Pending Forms Card -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        {{ __('Pending Documents to Complete') }}
                    </flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($pendingForms as $pf)
                            <div class="border rounded-xl border-slate-200 p-4 bg-slate-50 flex flex-col justify-between" wire:key="pending-card-{{ $pf->id }}">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <flux:heading size="md">{{ $pf->template->title }}</flux:heading>
                                        <flux:badge size="sm" color="amber">{{ __('Required') }}</flux:badge>
                                    </div>
                                    <flux:text class="text-xs mt-2 line-clamp-2">{{ $pf->template->description }}</flux:text>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-200/60 flex justify-between items-center">
                                    <span class="text-[10px] text-slate-500 font-mono">{{ __('Assigned: :date', ['date' => $pf->created_at->format('M j, Y')]) }}</span>
                                    <flux:button variant="primary" size="sm" wire:click="openForm({{ $pf->id }})">
                                        {{ __('Complete & Sign') }}
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-slate-500 italic text-sm">
                                <span class="material-symbols-outlined text-2xl block mb-1">done_all</span>
                                {{ __('All assigned forms are complete! No pending forms.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Forms Card -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        {{ __('Signed Documents & Agreements') }}
                    </flux:heading>

                    <div class="border rounded-xl border-slate-200 overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-500 ">
                            <thead class="bg-slate-50 text-xs text-slate-700 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3.5">{{ __('Document') }}</th>
                                    <th class="px-6 py-3.5">{{ __('E-Signed By') }}</th>
                                    <th class="px-6 py-3.5">{{ __('Signed At') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white ">
                                @forelse ($completedForms as $cf)
                                    <tr class="hover:bg-slate-50 :bg-slate-100/40 transition-colors" wire:key="completed-row-{{ $cf->id }}">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-900 ">{{ $cf->template->title }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $cf->template->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono text-slate-700 ">
                                            {{ $cf->signature_name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500 font-mono">
                                            {{ $cf->signed_at?->format('M j, Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-slate-500 italic text-sm">
                                            {{ __('No signed documents on file yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-500 italic bg-white border border-slate-200 rounded-2xl">
                    {{ __('No patient records associated with this portal account.') }}
                </div>
            @endif
        </div>

    <!-- Interactive Form Modal Overlay -->
    @if ($isFormOpen && $selectedForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-xl bg-white rounded-2xl border border-slate-200 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
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
                                        <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="responses.{{ $field['name'] }}"
                                                class="rounded border-slate-200 text-primary focus:ring-primary mt-0.5"
                                            />
                                            <span>
                                                {{ $field['label'] }}
                                                @if (isset($field['required']) && $field['required'])
                                                    <span class="text-red-500 font-bold">*</span>
                                                @endif
                                            </span>
                                        </label>
                                        @if ($field['type'] === 'yes_no')
                                            <div class="flex items-center gap-2 text-[11px] text-slate-500">
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
                    <div class="border-t border-slate-200 pt-5 space-y-4">
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

                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-[10px] text-slate-500 font-mono space-y-1">
                            <div>{{ __('Date/Time: :date', ['date' => now()->format('Y-m-d H:i:s T')]) }}</div>
                            <div>{{ __('IP Address: :ip', ['ip' => request()->ip() ?? '127.0.0.1']) }}</div>
                            <div>{{ __('Legal Standard: Uniform Electronic Transactions Act (UETA) compliant') }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
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
