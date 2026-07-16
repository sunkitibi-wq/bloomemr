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
                        <flux:field>
                            <flux:label>{{ __('Assign to Patient') }}</flux:label>
                            <flux:select wire:model="patientId" required>
                                <option value="">Select a Patient...</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->full_name }} (MRN: {{ $patient->mrn }})</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:button type="submit" variant="primary" class="w-full">
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
