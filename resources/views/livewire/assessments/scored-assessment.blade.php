<flux:main>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('patients.index') }}" wire:navigate>{{ __('Patients') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('patients.show', $patient) }}" wire:navigate>{{ $patient->full_name }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Scored Assessment') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Scored Assessment') }}</flux:heading>
            <flux:text class="mt-1">
                {{ $patient->full_name }} &middot; MRN: {{ $patient->mrn }}
            </flux:text>
        </div>
    </div>

    <div class="mt-8 max-w-4xl grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Configuration Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Configuration') }}</flux:heading>
                
                <div class="space-y-4">
                    <div>
                        <flux:label for="instrument">{{ __('Assessment Instrument') }}</flux:label>
                        <flux:select id="instrument" wire:model.live="instrument" class="mt-1">
                            <flux:select.option value="phq-9">{{ __('PHQ-9 (Depression)') }}</flux:select.option>
                            <flux:select.option value="gad-7">{{ __('GAD-7 (Anxiety)') }}</flux:select.option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label for="rater_type">{{ __('Rater Type') }}</flux:label>
                        <flux:select id="rater_type" wire:model="rater_type" class="mt-1">
                            <flux:select.option value="patient">{{ __('Patient (Self)') }}</flux:select.option>
                            <flux:select.option value="provider">{{ __('Provider') }}</flux:select.option>
                            <flux:select.option value="parent">{{ __('Parent') }}</flux:select.option>
                            <flux:select.option value="teacher">{{ __('Teacher') }}</flux:select.option>
                        </flux:select>
                    </div>
                </div>
            </div>

            <!-- Real-time Score Panel -->
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700 bg-neutral-50 dark:bg-zinc-800">
                <flux:heading size="lg" class="mb-2">{{ __('Real-time Score') }}</flux:heading>
                <div class="text-5xl font-bold text-neutral-800 dark:text-neutral-100">{{ $this->score }}</div>
                <div class="mt-2 text-sm font-medium text-neutral-600 dark:text-neutral-300">
                    {{ __('Severity: :severity', ['severity' => $this->severityBand]) }}
                </div>
                
                <flux:button variant="primary" class="mt-6 w-full" wire:click="save">
                    {{ __('Save Assessment') }}
                </flux:button>
            </div>
        </div>

        <!-- Questionnaire Panel -->
        <div class="lg:col-span-2 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-zinc-900 space-y-6">
            <div>
                <flux:heading size="lg">{{ $instruments[$instrument]['name'] }}</flux:heading>
                <flux:text class="mt-1 italic">{{ $instruments[$instrument]['description'] }}</flux:text>
            </div>

            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach ($instruments[$instrument]['questions'] as $qId => $questionText)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $qId }}. {{ $questionText }}</div>
                        
                        <div class="mt-3 flex flex-wrap gap-4">
                            @foreach ($instruments[$instrument]['choices'] as $val => $label)
                                <label class="inline-flex items-center text-xs cursor-pointer">
                                    <input type="radio" wire:model.live="responses.{{ $qId }}" value="{{ $val }}" class="mr-1.5 h-3.5 w-3.5 text-primary border-neutral-300 dark:border-neutral-700 focus:ring-primary" />
                                    <span class="text-neutral-600 dark:text-neutral-400">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</flux:main>
