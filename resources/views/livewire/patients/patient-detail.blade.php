<flux:main>
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            @if ($patient->photo_path)
                <img src="{{ Storage::url($patient->photo_path) }}" alt="{{ $patient->full_name }}" class="h-16 w-16 rounded-full object-cover border border-zinc-200 dark:border-zinc-700 shadow-sm" />
            @else
                <div class="h-16 w-16 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 text-lg font-bold font-mono">
                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <flux:heading size="xl" level="1">{{ $patient->full_name }}</flux:heading>
                <flux:text class="mt-1">{{ __('MRN: :mrn', ['mrn' => $patient->mrn]) }}</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button variant="primary" href="{{ route('encounters.create', $patient) }}" wire:navigate>
                {{ __('New Encounter') }}
            </flux:button>
            <flux:button variant="ghost" href="{{ route('patients.assessment.create', $patient) }}" wire:navigate>
                {{ __('New Assessment') }}
            </flux:button>
            <flux:button variant="ghost" href="{{ route('patients.edit', $patient) }}" wire:navigate>
                {{ __('Edit') }}
            </flux:button>
            <flux:button variant="ghost" href="{{ route('documents.upload', $patient) }}" wire:navigate>
                {{ __('Upload Document') }}
            </flux:button>
            @can('delete', $patient)
                <flux:button variant="danger" wire:click="deletePatient" wire:confirm="{{ __('Are you sure you want to delete this patient record?') }}">
                    {{ __('Delete') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <!-- Tabs Menu -->
    <div class="mt-6 border-b border-zinc-200 dark:border-zinc-700 flex gap-6 text-sm">
        <button 
            wire:click="$set('activeTab', 'encounters')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'encounters' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Encounters & History') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'medications')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'medications' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Medications & Prescribing') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'labs')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'labs' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Labs & Monitoring') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'messages')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'messages' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Secure Messages') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'refills')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'refills' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Refill Requests') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'telehealth')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'telehealth' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Telehealth Room') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'engagements')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'engagements' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Engagement') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'forms')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'forms' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Intake & Consents') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'care_coordination')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'care_coordination' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Care Coordination') }}
        </button>
        <button 
            wire:click="$set('activeTab', 'radiology')" 
            class="pb-3 font-medium cursor-pointer focus:outline-none {{ $activeTab === 'radiology' ? 'border-b-2 border-primary text-primary' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('Radiology & Imaging') }}
        </button>
    </div>

    @if ($activeTab === 'encounters')
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg">{{ __('Demographics') }}</flux:heading>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('DOB') }}</dt>
                            <dd>{{ $patient->date_of_birth?->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Gender') }}</dt>
                            <dd>{{ $patient->gender_identity ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Pronouns') }}</dt>
                            <dd>{{ $patient->pronouns ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Language') }}</dt>
                            <dd>{{ $patient->preferred_language }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Provider') }}</dt>
                            <dd>{{ $patient->primaryProvider?->name ?? '—' }}</dd>
                        </div>
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">{{ __('Insurance & Eligibility') }}</flux:heading>
                        <flux:button size="xs" variant="outline" wire:click="checkInsuranceEligibility" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="checkInsuranceEligibility">{{ __('Verify') }}</span>
                            <span wire:loading wire:target="checkInsuranceEligibility">{{ __('Verifying...') }}</span>
                        </flux:button>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Primary Payer') }}</dt>
                            <dd class="font-medium">{{ $patient->primary_insurance ?? 'None On File' }}</dd>
                        </div>
                        @if ($latestEligibilityCheck)
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">{{ __('Status') }}</dt>
                                <dd>
                                    @if ($latestEligibilityCheck->status === 'eligible')
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            {{ __('Active / Eligible') }}
                                        </span>
                                    @elseif ($latestEligibilityCheck->status === 'ineligible')
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                            {{ __('Inactive / Ineligible') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            {{ __('Error') }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            @if ($latestEligibilityCheck->status === 'eligible')
                                <div class="flex justify-between">
                                    <dt class="text-neutral-500">{{ __('Payer Name') }}</dt>
                                    <dd>{{ $latestEligibilityCheck->payer_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-neutral-500">{{ __('Co-Pay') }}</dt>
                                    <dd class="font-bold">${{ number_format($latestEligibilityCheck->copay_amount, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-neutral-500">{{ __('Deductible') }}</dt>
                                    <dd class="font-bold">${{ number_format($latestEligibilityCheck->deductible_amount, 2) }}</dd>
                                </div>
                            @endif
                            <div class="text-[10px] text-zinc-400 mt-2 text-right">
                                {{ __('Checked: :time', ['time' => $latestEligibilityCheck->checked_at->diffForHumans()]) }}
                            </div>
                        @else
                            <p class="text-xs italic text-neutral-400 mt-2">{{ __('No eligibility checks have been performed yet.') }}</p>
                        @endif
                    </dl>
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg">{{ __('Contacts') }}</flux:heading>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Phone') }}</dt>
                            <dd>{{ $patient->phone ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Email') }}</dt>
                            <dd class="truncate max-w-[150px]">{{ $patient->email ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">{{ __('Emergency') }}</dt>
                            <dd>{{ $patient->emergency_contact_name ?? '—' }} ({{ $patient->emergency_contact_phone ?? '—' }})</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg">{{ __('Clinical Summary') }}</flux:heading>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-neutral-500">{{ __('Problem List') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->problem_list ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-neutral-500">{{ __('Allergies') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->allergies ?: 'NKDA' }}</dd>
                        </div>
                        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800">
                            <dt class="font-medium text-neutral-500">{{ __('Past Medical History') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->past_medical_history ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-neutral-500">{{ __('Surgical History') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->surgical_history ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-neutral-500">{{ __('Family History') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->family_history ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-neutral-500">{{ __('Social History') }}</dt>
                            <dd class="mt-1 whitespace-pre-line">{{ $patient->social_history ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg" class="mb-4">{{ __('Recent Encounters') }}</flux:heading>

                    @forelse ($patient->encounters as $encounter)
                        <div class="flex items-center justify-between border-b border-neutral-100 py-3 last:border-0 dark:border-neutral-700">
                            <div>
                                <div class="text-sm font-medium">{{ $encounter->encounter_date->format('M j, Y') }}</div>
                                <div class="text-xs text-neutral-500">
                                    {{ ucfirst($encounter->type) }} &middot; {{ $encounter->provider->name }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:badge variant="{{ $encounter->status === 'signed' ? 'success' : 'warning' }}" size="sm">
                                    {{ ucfirst($encounter->status) }}
                                </flux:badge>
                                <flux:button variant="ghost" size="sm" href="{{ route('encounters.note', $encounter) }}" wire:navigate>
                                    {{ __('View Note') }}
                                </flux:button>
                                @can('delete', $encounter)
                                    <flux:button variant="ghost" size="sm" wire:click="deleteEncounter({{ $encounter->id }})" wire:confirm="{{ __('Are you sure you want to delete this encounter?') }}" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50">
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500">{{ __('No encounters yet.') }}</p>
                    @endforelse
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg" class="mb-4">{{ __('Completed Assessments') }}</flux:heading>

                    @if ($patient->assessments->isNotEmpty())
                        <div class="mb-6 border border-neutral-100 p-4 rounded-lg dark:border-neutral-800 bg-white dark:bg-zinc-950">
                            <div x-data="{
                                init() {
                                    const ctx = document.getElementById('assessments-trend-chart')?.getContext('2d');
                                    if (!ctx) return;
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: @json($patient->assessments->sortBy('created_at')->values()->map(fn($a) => $a->created_at->format('M j'))),
                                            datasets: [
                                                {
                                                    label: 'PHQ-9',
                                                    data: @json($patient->assessments->where('instrument', 'phq-9')->sortBy('created_at')->values()->map(fn($a) => ['x' => $a->created_at->format('M j'), 'y' => $a->score])),
                                                    borderColor: '#ef4444',
                                                    backgroundColor: '#ef4444',
                                                    tension: 0.1,
                                                    fill: false
                                                },
                                                {
                                                    label: 'GAD-7',
                                                    data: @json($patient->assessments->where('instrument', 'gad-7')->sortBy('created_at')->values()->map(fn($a) => ['x' => $a->created_at->format('M j'), 'y' => $a->score])),
                                                    borderColor: '#0ea5e9',
                                                    backgroundColor: '#0ea5e9',
                                                    tension: 0.1,
                                                    fill: false
                                                }
                                            ]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    max: 27
                                                }
                                            }
                                        }
                                    });
                                }
                            }" wire:ignore class="h-40">
                                <canvas id="assessments-trend-chart"></canvas>
                            </div>
                        </div>
                    @endif

                    @forelse ($patient->assessments as $assessment)
                        <div class="flex items-center justify-between border-b border-neutral-100 py-3 last:border-0 dark:border-neutral-700">
                            <div>
                                <div class="text-sm font-medium">{{ strtoupper($assessment->instrument) }} (Score: {{ $assessment->score }})</div>
                                <div class="text-xs text-neutral-500">
                                    {{ __('Rater: :rater', ['rater' => ucfirst($assessment->rater_type)]) }} &middot; {{ $assessment->severity_band }}
                                </div>
                            </div>
                            <div class="text-xs text-neutral-500">
                                {{ $assessment->created_at->format('M j, Y') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500">{{ __('No assessments completed yet.') }}</p>
                    @endforelse
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="lg" class="mb-4">{{ __('Documents') }}</flux:heading>

                    @forelse ($patient->documents as $document)
                        <div class="flex items-center justify-between border-b border-neutral-100 py-3 last:border-0 dark:border-neutral-700">
                            <div>
                                <div class="text-sm font-medium">{{ $document->original_name }}</div>
                                <div class="text-xs text-neutral-500">
                                    {{ ucfirst(str_replace('_', ' ', $document->category)) }} &middot; {{ number_format($document->size / 1024, 1) }} KB
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-xs text-neutral-500 mr-2">
                                    {{ $document->created_at->format('M j, Y') }}
                                </div>
                                @can('delete', $document)
                                    <flux:button variant="ghost" size="sm" wire:click="deleteDocument({{ $document->id }})" wire:confirm="{{ __('Are you sure you want to delete this document?') }}" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50">
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500">{{ __('No documents yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif ($activeTab === 'medications')
        <div class="mt-6">
            <livewire:patients.patient-medications :patient="$patient" />
        </div>
    @elseif ($activeTab === 'labs')
        <div class="mt-6">
            <livewire:patients.patient-labs :patient="$patient" />
        </div>
    @elseif ($activeTab === 'messages')
        <div class="mt-6">
            <livewire:patients.patient-messages :patient="$patient" />
        </div>
    @elseif ($activeTab === 'refills')
        <div class="mt-6">
            <livewire:patients.patient-refills :patient="$patient" />
        </div>
    @elseif ($activeTab === 'telehealth')
        <div class="mt-6">
            <livewire:patients.patient-telehealth :patient="$patient" />
        </div>
    @elseif ($activeTab === 'engagements')
        <div class="mt-8">
            <livewire:patients.patient-engagements :patient="$patient" />
        </div>
    @elseif ($activeTab === 'forms')
        <div class="mt-8 space-y-8">
            <!-- Create or assign forms -->
            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-zinc-900 space-y-6">
                <div>
                    <flux:heading size="lg" class="mb-2">{{ __('Create or Assign Intake / Consent Forms') }}</flux:heading>
                    <flux:subheading class="mb-4">{{ __('Create a new template for your team or select an existing one to assign to the patient portal.') }}</flux:subheading>
                </div>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Template Title') }}</flux:label>
                            <flux:input wire:model="templateTitle" placeholder="e.g. New Patient Intake" />
                            @error('templateTitle') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Template Type') }}</flux:label>
                            <select wire:model="templateType" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <option value="consent">{{ __('Consent') }}</option>
                                <option value="intake">{{ __('Intake') }}</option>
                            </select>
                            @error('templateType') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="templateDescription" rows="2" placeholder="Optional summary for the form" />
                        @error('templateDescription') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </flux:field>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <flux:subheading>{{ __('Questions') }}</flux:subheading>
                            <flux:button variant="ghost" size="sm" wire:click="addTemplateField">
                                {{ __('Add Question') }}
                            </flux:button>
                        </div>

                        @foreach ($templateFields as $index => $field)
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 space-y-3" wire:key="template-field-{{ $index }}">
                                <div class="grid gap-4 md:grid-cols-[1.2fr,1fr,auto]">
                                    <flux:field>
                                        <flux:label>{{ __('Field Name') }}</flux:label>
                                        <flux:input wire:model="templateFields.{{ $index }}.name" placeholder="e.g. reason_for_visit" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>{{ __('Question Label') }}</flux:label>
                                        <flux:input wire:model="templateFields.{{ $index }}.label" placeholder="e.g. What brings you in today?" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>{{ __('Type') }}</flux:label>
                                        <select wire:model="templateFields.{{ $index }}.type" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                            <option value="text">{{ __('Text') }}</option>
                                            <option value="checkbox">{{ __('Checkbox') }}</option>
                                            <option value="yes_no">{{ __('Yes / No') }}</option>
                                        </select>
                                    </flux:field>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                        <input type="checkbox" wire:model="templateFields.{{ $index }}.required" class="rounded border-zinc-300 text-primary focus:ring-primary" />
                                        {{ __('Required') }}
                                    </label>
                                    @if ($index > 0)
                                        <flux:button variant="ghost" size="sm" wire:click="removeTemplateField({{ $index }})" class="text-red-500 hover:text-red-600">
                                            {{ __('Remove') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <flux:button variant="primary" wire:click="createTemplate">
                            {{ __('Save Template') }}
                        </flux:button>
                    </div>
                </div>

                <div class="space-y-3">
                    <flux:subheading>{{ __('Existing templates') }}</flux:subheading>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($templatesList as $tpl)
                            <flux:button 
                                variant="filled" 
                                wire:click="assignForm({{ $tpl['id'] }})"
                                class="flex items-center gap-1.5"
                            >
                                <flux:icon.plus class="size-4" />
                                {{ $tpl['title'] }}
                            </flux:button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Assigned & Completed Forms List -->
            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 bg-white dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Assigned & Completed Forms') }}</flux:heading>

                <div class="border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">{{ __('Form Name') }}</th>
                                <th class="px-6 py-3.5">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5">{{ __('Dates') }}</th>
                                <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($patient->patientForms as $form)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors" wire:key="pf-{{ $form->id }}">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $form->template->title }}</div>
                                        <div class="text-xs text-zinc-400 mt-0.5">{{ $form->template->description }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <flux:badge size="sm" color="{{ $form->status === 'completed' ? 'green' : 'amber' }}">
                                            {{ ucfirst($form->status) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-400">
                                        <div>{{ __('Assigned: :date', ['date' => $form->created_at->format('M j, Y')]) }}</div>
                                        @if ($form->signed_at)
                                            <div class="text-green-600 dark:text-green-400 font-medium mt-0.5">
                                                {{ __('Signed: :date', ['date' => $form->signed_at->format('M j, Y')]) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if ($form->status === 'completed')
                                            <flux:button size="sm" variant="ghost" wire:click="viewCompletedForm({{ $form->id }})">
                                                {{ __('View Responses') }}
                                            </flux:button>
                                        @endif
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost" 
                                            class="text-red-500 hover:text-red-600"
                                            wire:click="deleteForm({{ $form->id }})"
                                            wire:confirm="{{ __('Remove this form assignment?') }}"
                                        >
                                            {{ __('Remove') }}
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-zinc-400 italic text-sm">
                                        {{ __('No forms assigned to this patient.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- View Completed Form Modal Overlay -->
    <div x-data="{ open: @entangle('showFormModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6 max-h-[90vh] overflow-y-auto animate-fade-in">
                <div>
                    <flux:heading size="lg">{{ $selectedFormTitle }}</flux:heading>
                    <flux:text class="text-xs mt-1">{{ __('Submitted Questionnaire Responses & E-Signature Audit Trail') }}</flux:text>
                </div>

                <div class="space-y-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    <div class="space-y-3 pb-4">
                        <span class="text-xs font-semibold text-zinc-500 block uppercase tracking-wider">{{ __('Responses') }}</span>
                        @if ($selectedFormData)
                            @foreach ($selectedFormData as $key => $val)
                                <div class="text-sm">
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400 block">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">
                                        @if (is_bool($val))
                                            {{ $val ? __('Yes') : __('No') }}
                                        @else
                                            {{ $val }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs italic text-zinc-400">{{ __('No responses captured.') }}</p>
                        @endif
                    </div>

                    <div class="pt-4 space-y-2.5">
                        <span class="text-xs font-semibold text-zinc-500 block uppercase tracking-wider">{{ __('E-Signature Audit Trail') }}</span>
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 text-xs space-y-1.5 font-mono">
                            <div><span class="text-zinc-400">{{ __('E-Signed By:') }}</span> {{ $selectedFormSignature }}</div>
                            <div><span class="text-zinc-400">{{ __('Signed At:') }}</span> {{ $selectedFormSignedAt }}</div>
                            <div><span class="text-zinc-400">{{ __('IP Address:') }}</span> {{ $selectedFormIp }}</div>
                            <div class="text-[10px] text-green-600 dark:text-green-400 font-semibold flex items-center gap-1 mt-2">
                                <flux:icon.check-circle class="size-3.5 inline" />
                                {{ __('Secured electronic record locked.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="button" wire:click="$set('showFormModal', false)">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    @if ($activeTab === 'care_coordination')
        <livewire:patients.patient-care-coordination :patient="$patient" />
    @endif

    @if ($activeTab === 'radiology')
        <livewire:patients.patient-radiology :patient="$patient" />
    @endif
</flux:main>
