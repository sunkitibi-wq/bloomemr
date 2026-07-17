<flux:main class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ __('Global AI Assistant') }}</flux:heading>
            <flux:subheading>{{ __('Generate clinical documentation from rough dictations or transcripts.') }}</flux:subheading>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:toast variant="success" text="{{ session('message') }}" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Input Panel -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Patient & Context') }}</flux:heading>
            
            <div class="space-y-4 mb-6">
                <flux:field>
                    <flux:label>{{ __('Select Patient') }}</flux:label>
                    <flux:select wire:model.live="patientId">
                        <option value="">No Patient Selected</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }} (MRN: {{ $patient->mrn }})</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                @if($patientId)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg space-y-4">
                        <flux:heading size="sm">{{ __('Patient Clinical Context') }}</flux:heading>
                        <flux:subheading size="sm" class="mb-2">{{ __('These details will be passed to the AI. Update them here to keep the patient record current.') }}</flux:subheading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>{{ __('Allergies') }}</flux:label>
                                <flux:input wire:model="allergies" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('Problem List') }}</flux:label>
                                <flux:input wire:model="problem_list" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('Past Medical History') }}</flux:label>
                                <flux:input wire:model="past_medical_history" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('Surgical History') }}</flux:label>
                                <flux:input wire:model="surgical_history" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('Family History') }}</flux:label>
                                <flux:input wire:model="family_history" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('Social History') }}</flux:label>
                                <flux:input wire:model="social_history" />
                            </flux:field>
                        </div>
                        
                        <div class="flex justify-end mt-4">
                            <flux:button type="button" wire:click="savePatientDetails" size="sm" variant="outline">{{ __('Save to Patient Record') }}</flux:button>
                        </div>
                    </div>
                @endif
            </div>

            <flux:separator class="my-6" />

            <flux:heading size="lg" class="mb-4">{{ __('Raw Transcript / Dictation') }}</flux:heading>
            <form wire:submit="generateDraft" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Target Template') }}</flux:label>
                    <flux:select wire:model="templateType" required>
                        <option value="SOAP">SOAP Note</option>
                        <option value="DAP">DAP Note</option>
                        <option value="Intake">Psychiatric Intake Eval</option>
                        <option value="Narrative">Narrative Free-text</option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Transcript Text') }}</flux:label>
                    <flux:textarea wire:model="transcript" rows="10" placeholder="Patient came in today reporting poor sleep..." required />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full" icon="sparkles">
                    <span wire:loading.remove wire:target="generateDraft">
                        {{ __('Generate Draft') }}
                    </span>
                    <span wire:loading wire:target="generateDraft">
                        {{ __('Processing AI...') }}
                    </span>
                </flux:button>
            </form>
        </flux:card>

        <!-- Draft Result Panel -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Generated Draft') }}</flux:heading>
            
            @if($draft)
                <div class="space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-800 p-4 rounded-lg space-y-4">
                        @foreach ($draft as $section => $text)
                            <div>
                                <span class="font-bold text-xs uppercase tracking-wider text-zinc-500">{{ str_replace('_', ' ', $section) }}</span>
                                <p class="text-sm mt-1">{{ $text }}</p>
                            </div>
                        @endforeach
                    </div>

                    <flux:separator />

                    <form wire:submit="saveAsNote" class="space-y-4">
                        @if(!$patientId)
                            <div class="text-sm text-red-500 mb-2">{{ __('Please select a patient on the left panel before saving.') }}</div>
                        @endif

                        <flux:button type="submit" variant="primary" class="w-full" :disabled="!$patientId">
                            {{ __('Save as Draft Clinical Note') }}
                        </flux:button>
                    </form>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-zinc-400">
                    <flux:icon.sparkles class="size-8 mb-2 opacity-50" />
                    <p>{{ __('Submit a transcript to generate an AI draft.') }}</p>
                </div>
            @endif
        </flux:card>
    </div>
</flux:main>
