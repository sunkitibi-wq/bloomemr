<div class="flex flex-col gap-6 max-w-[1600px] w-full">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            @if ($patient->photo_path)
                <img src="{{ Storage::url($patient->photo_path) }}" alt="{{ $patient->full_name }}" class="h-16 w-16 rounded-full object-cover border border-slate-200 shadow-sm" />
            @else
                <div class="h-16 w-16 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl font-bold font-mono shadow-sm">
                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $patient->full_name }}</h1>
                <p class="text-[13px] text-slate-500 mt-1 font-mono">{{ __('MRN: :mrn', ['mrn' => $patient->mrn]) }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('encounters.create', $patient) }}" wire:navigate class="px-4 py-2 bg-indigo-600 border border-transparent rounded-xl shadow-sm text-[13px] font-semibold text-white hover:bg-indigo-700 transition-colors">
                {{ __('New Encounter') }}
            </a>
            <a href="{{ route('patients.assessment.create', $patient) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                {{ __('New Assessment') }}
            </a>
            <a href="{{ route('patients.edit', $patient) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('documents.upload', $patient) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                {{ __('Upload Document') }}
            </a>
            @can('delete', $patient)
                <button wire:click="deletePatient" wire:confirm="{{ __('Are you sure you want to delete this patient record?') }}" class="px-4 py-2 bg-red-50 border border-red-100 rounded-xl shadow-sm text-[13px] font-medium text-red-600 hover:bg-red-100 transition-colors">
                    {{ __('Delete') }}
                </button>
            @endcan
        </div>
    </div>

    <!-- Tabs Menu -->
    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
        @php
            $tabs = [
                'encounters' => 'Encounters & History',
                'medications' => 'Medications & Prescribing',
                'labs' => 'Labs & Monitoring',
                'messages' => 'Secure Messages',
                'refills' => 'Refill Requests',
                'telehealth' => 'Telehealth Room',
                'engagements' => 'Engagement',
                'forms' => 'Intake & Consents',
                'care_coordination' => 'Care Coordination',
                'radiology' => 'Radiology & Imaging'
            ];
        @endphp
        
        @foreach($tabs as $key => $label)
            <button 
                wire:click="$set('activeTab', '{{ $key }}')" 
                class="whitespace-nowrap px-4 py-2 rounded-xl text-[13px] font-medium transition-colors {{ $activeTab === $key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                {{ __($label) }}
            </button>
        @endforeach
    </div>

    @if ($activeTab === 'encounters')
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-bold text-[14px] text-slate-800">{{ __('Demographics') }}</h3>
                    <dl class="mt-4 space-y-3 text-[13px]">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('DOB') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->date_of_birth?->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Gender') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->gender_identity ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Pronouns') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->pronouns ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Language') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->preferred_language }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-slate-500">{{ __('Provider') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->primaryProvider?->name ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Insurance & Eligibility') }}</h3>
                        <button wire:click="checkInsuranceEligibility" wire:loading.attr="disabled" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[11px] font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                            <span wire:loading.remove wire:target="checkInsuranceEligibility">{{ __('Verify') }}</span>
                            <span wire:loading wire:target="checkInsuranceEligibility">{{ __('Verifying...') }}</span>
                        </button>
                    </div>
                    <dl class="space-y-3 text-[13px]">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Primary Payer') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->primary_insurance ?? 'None On File' }}</dd>
                        </div>
                        @if ($latestEligibilityCheck)
                            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Status') }}</dt>
                                <dd>
                                    @if ($latestEligibilityCheck->status === 'eligible')
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ __('Active / Eligible') }}
                                        </span>
                                    @elseif ($latestEligibilityCheck->status === 'ineligible')
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ __('Inactive / Ineligible') }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-200">
                                            {{ __('Error') }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            @if ($latestEligibilityCheck->status === 'eligible')
                                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                    <dt class="text-slate-500">{{ __('Payer Name') }}</dt>
                                    <dd class="font-medium text-slate-800">{{ $latestEligibilityCheck->payer_name }}</dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                    <dt class="text-slate-500">{{ __('Co-Pay') }}</dt>
                                    <dd class="font-bold text-slate-800">${{ number_format($latestEligibilityCheck->copay_amount, 2) }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-slate-500">{{ __('Deductible') }}</dt>
                                    <dd class="font-bold text-slate-800">${{ number_format($latestEligibilityCheck->deductible_amount, 2) }}</dd>
                                </div>
                            @endif
                            <div class="text-[11px] text-slate-400 mt-3 text-right">
                                {{ __('Checked: :time', ['time' => $latestEligibilityCheck->checked_at->diffForHumans()]) }}
                            </div>
                        @else
                            <p class="text-[12px] italic text-slate-400 mt-2">{{ __('No eligibility checks have been performed yet.') }}</p>
                        @endif
                    </dl>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-bold text-[14px] text-slate-800">{{ __('Contacts') }}</h3>
                    <dl class="mt-4 space-y-3 text-[13px]">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Phone') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->phone ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Email') }}</dt>
                            <dd class="font-medium text-slate-800 truncate max-w-[150px]">{{ $patient->email ?? '—' }}</dd>
                        </div>
                        <div class="flex flex-col gap-1 pt-1">
                            <dt class="text-slate-500">{{ __('Emergency Contact') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $patient->emergency_contact_name ?? '—' }} <br><span class="text-slate-500 text-[12px]">{{ $patient->emergency_contact_phone ?? '' }}</span></dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-bold text-[14px] text-slate-800">{{ __('Clinical Summary') }}</h3>
                    <dl class="mt-4 space-y-4 text-[13px]">
                        <div>
                            <dt class="font-medium text-slate-500">{{ __('Problem List') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->problem_list ?: '—' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-slate-50">
                            <dt class="font-medium text-slate-500">{{ __('Allergies') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->allergies ?: 'NKDA' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-slate-50">
                            <dt class="font-medium text-slate-500">{{ __('Past Medical History') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->past_medical_history ?: '—' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-slate-50">
                            <dt class="font-medium text-slate-500">{{ __('Surgical History') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->surgical_history ?: '—' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-slate-50">
                            <dt class="font-medium text-slate-500">{{ __('Family History') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->family_history ?: '—' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-slate-50">
                            <dt class="font-medium text-slate-500">{{ __('Social History') }}</dt>
                            <dd class="mt-1 font-medium text-slate-800 whitespace-pre-line">{{ $patient->social_history ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Recent Encounters') }}</h3>
                    </div>
                    <div class="flex flex-col">
                        @forelse ($patient->encounters as $encounter)
                            <div class="flex items-center justify-between border-b border-slate-100 p-5 last:border-0 hover:bg-slate-50 transition-colors">
                                <div>
                                    <div class="text-[14px] font-bold text-slate-800">{{ $encounter->encounter_date->format('M j, Y') }}</div>
                                    <div class="text-[12px] text-slate-500 mt-0.5">
                                        {{ ucfirst($encounter->type) }} &middot; {{ $encounter->provider->name }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $encounter->status === 'signed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                        {{ ucfirst($encounter->status) }}
                                    </span>
                                    <a href="{{ route('encounters.note', $encounter) }}" wire:navigate class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        {{ __('View Note') }}
                                    </a>
                                    @can('delete', $encounter)
                                        <button wire:click="deleteEncounter({{ $encounter->id }})" wire:confirm="{{ __('Are you sure you want to delete this encounter?') }}" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <flux:icon.trash class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <flux:icon.document-text class="w-10 h-10 mx-auto text-slate-300 mb-2" variant="outline" />
                                <p class="text-[13px] text-slate-500">{{ __('No encounters yet.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Completed Assessments') }}</h3>
                    </div>

                    @if ($patient->assessments->isNotEmpty())
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
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
                                                    borderColor: '#4f46e5',
                                                    backgroundColor: '#4f46e5',
                                                    tension: 0.3,
                                                    fill: false
                                                },
                                                {
                                                    label: 'GAD-7',
                                                    data: @json($patient->assessments->where('instrument', 'gad-7')->sortBy('created_at')->values()->map(fn($a) => ['x' => $a->created_at->format('M j'), 'y' => $a->score])),
                                                    borderColor: '#0ea5e9',
                                                    backgroundColor: '#0ea5e9',
                                                    tension: 0.3,
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

                    <div class="flex flex-col">
                        @forelse ($patient->assessments as $assessment)
                            <div class="flex items-center justify-between border-b border-slate-100 p-5 last:border-0 hover:bg-slate-50 transition-colors">
                                <div>
                                    <div class="text-[14px] font-bold text-slate-800">{{ strtoupper($assessment->instrument) }} <span class="text-indigo-600">(Score: {{ $assessment->score }})</span></div>
                                    <div class="text-[12px] text-slate-500 mt-0.5">
                                        {{ __('Rater: :rater', ['rater' => ucfirst($assessment->rater_type)]) }} &middot; {{ $assessment->severity_band }}
                                    </div>
                                </div>
                                <div class="text-[12px] font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                                    {{ $assessment->created_at->format('M j, Y') }}
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <flux:icon.clipboard-document-list class="w-10 h-10 mx-auto text-slate-300 mb-2" variant="outline" />
                                <p class="text-[13px] text-slate-500">{{ __('No assessments completed yet.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-[14px] text-slate-800">{{ __('Documents') }}</h3>
                    </div>
                    
                    <div class="flex flex-col">
                        @forelse ($patient->documents as $document)
                            <div class="flex items-center justify-between border-b border-slate-100 p-5 last:border-0 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        <flux:icon.document class="w-8 h-8 text-indigo-500" variant="outline" />
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-bold text-slate-800">{{ $document->original_name }}</div>
                                        <div class="text-[12px] text-slate-500 mt-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $document->category)) }} &middot; {{ number_format($document->size / 1024, 1) }} KB
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-[12px] font-medium text-slate-500">
                                        {{ $document->created_at->format('M j, Y') }}
                                    </div>
                                    @can('delete', $document)
                                        <button wire:click="deleteDocument({{ $document->id }})" wire:confirm="{{ __('Are you sure you want to delete this document?') }}" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <flux:icon.trash class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <flux:icon.folder class="w-10 h-10 mx-auto text-slate-300 mb-2" variant="outline" />
                                <p class="text-[13px] text-slate-500">{{ __('No documents yet.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @elseif ($activeTab === 'medications')
        <div class="mt-2">
            <livewire:patients.patient-medications :patient="$patient" />
        </div>
    @elseif ($activeTab === 'labs')
        <div class="mt-2">
            <livewire:patients.patient-labs :patient="$patient" />
        </div>
    @elseif ($activeTab === 'messages')
        <div class="mt-2">
            <livewire:patients.patient-messages :patient="$patient" />
        </div>
    @elseif ($activeTab === 'refills')
        <div class="mt-2">
            <livewire:patients.patient-refills :patient="$patient" />
        </div>
    @elseif ($activeTab === 'telehealth')
        <div class="mt-2">
            <livewire:patients.patient-telehealth :patient="$patient" />
        </div>
    @elseif ($activeTab === 'engagements')
        <div class="mt-2">
            <livewire:patients.patient-engagements :patient="$patient" />
        </div>
    @elseif ($activeTab === 'forms')
        <div class="mt-2 space-y-6">
            <!-- Create or assign forms -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
                <div>
                    <h3 class="font-bold text-[16px] text-slate-800 mb-1">{{ __('Create or Assign Intake / Consent Forms') }}</h3>
                    <p class="text-[13px] text-slate-500">{{ __('Create a new template for your team or select an existing one to assign to the patient portal.') }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 p-5 space-y-5 bg-slate-50/50">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-semibold text-slate-700">{{ __('Template Title') }}</label>
                            <input wire:model="templateTitle" type="text" class="block w-full px-3 py-2 border border-slate-200 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors shadow-sm" placeholder="e.g. New Patient Intake" />
                            @error('templateTitle') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-semibold text-slate-700">{{ __('Template Type') }}</label>
                            <select wire:model="templateType" class="block w-full px-3 py-2 border border-slate-200 rounded-lg leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors shadow-sm">
                                <option value="consent">{{ __('Consent') }}</option>
                                <option value="intake">{{ __('Intake') }}</option>
                            </select>
                            @error('templateType') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-semibold text-slate-700">{{ __('Description') }}</label>
                        <textarea wire:model="templateDescription" rows="2" class="block w-full px-3 py-2 border border-slate-200 rounded-lg leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] transition-colors shadow-sm" placeholder="Optional summary for the form"></textarea>
                        @error('templateDescription') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-4 pt-2 border-t border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[14px] font-bold text-slate-800">{{ __('Questions') }}</h4>
                            <button wire:click="addTemplateField" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[12px] font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                                {{ __('Add Question') }}
                            </button>
                        </div>

                        @foreach ($templateFields as $index => $field)
                            <div class="rounded-xl border border-slate-200 p-4 bg-white space-y-4 shadow-sm" wire:key="template-field-{{ $index }}">
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[12px] font-semibold text-slate-700">{{ __('Field Name') }}</label>
                                        <input wire:model="templateFields.{{ $index }}.name" type="text" class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-[12px] focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. reason_for_visit" />
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[12px] font-semibold text-slate-700">{{ __('Question Label') }}</label>
                                        <input wire:model="templateFields.{{ $index }}.label" type="text" class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-[12px] focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. What brings you in today?" />
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[12px] font-semibold text-slate-700">{{ __('Type') }}</label>
                                        <select wire:model="templateFields.{{ $index }}.type" class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-[12px] focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                            <option value="text">{{ __('Text') }}</option>
                                            <option value="checkbox">{{ __('Checkbox') }}</option>
                                            <option value="yes_no">{{ __('Yes / No') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                    <label class="flex items-center gap-2 text-[13px] text-slate-600 font-medium cursor-pointer">
                                        <input type="checkbox" wire:model="templateFields.{{ $index }}.required" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                        {{ __('Required') }}
                                    </label>
                                    @if ($index > 0)
                                        <button wire:click="removeTemplateField({{ $index }})" class="text-[12px] font-semibold text-red-500 hover:text-red-600">
                                            {{ __('Remove') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button wire:click="createTemplate" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-xl shadow-sm text-[13px] font-semibold text-white hover:bg-indigo-700 transition-colors">
                            {{ __('Save Template') }}
                        </button>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h4 class="text-[14px] font-bold text-slate-800">{{ __('Existing templates') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($templatesList as $tpl)
                            <button wire:click="assignForm({{ $tpl['id'] }})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[12px] font-semibold rounded-lg transition-colors border border-slate-200">
                                <flux:icon.plus class="w-3 h-3" />
                                {{ $tpl['title'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Assigned & Completed Forms List -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-[14px] text-slate-800">{{ __('Assigned & Completed Forms') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] whitespace-nowrap">
                        <thead class="text-[11px] text-slate-400 font-medium border-b border-slate-100 bg-slate-50/50 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-4 font-medium">{{ __('Form Name') }}</th>
                                <th class="px-5 py-4 font-medium">{{ __('Status') }}</th>
                                <th class="px-5 py-4 font-medium">{{ __('Dates') }}</th>
                                <th class="px-5 py-4 font-medium text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($patient->patientForms as $form)
                                <tr class="hover:bg-slate-50 transition-colors" wire:key="pf-{{ $form->id }}">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-800">{{ $form->template->title }}</div>
                                        <div class="text-[12px] text-slate-500 mt-0.5">{{ $form->template->description }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $form->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-[12px] text-slate-500 flex flex-col gap-0.5">
                                        <div>{{ __('Assigned: :date', ['date' => $form->created_at->format('M j, Y')]) }}</div>
                                        @if ($form->signed_at)
                                            <div class="text-emerald-600 font-semibold">
                                                {{ __('Signed: :date', ['date' => $form->signed_at->format('M j, Y')]) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($form->status === 'completed')
                                                <button wire:click="viewCompletedForm({{ $form->id }})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                                    {{ __('View Responses') }}
                                                </button>
                                            @endif
                                            <button wire:click="deleteForm({{ $form->id }})" wire:confirm="{{ __('Remove this form assignment?') }}" class="px-3 py-1.5 bg-red-50 border border-red-100 rounded-lg text-[11px] font-medium text-red-600 hover:bg-red-100 transition-colors">
                                                {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-60">
                                            <flux:icon.document-check class="w-10 h-10 text-slate-300 mb-3" variant="outline" />
                                            <p class="text-[13px] text-slate-500">{{ __('No forms assigned to this patient.') }}</p>
                                        </div>
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
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div @click.away="open = false" class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-100 p-6 flex flex-col max-h-[90vh]">
                <div class="pb-4 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800">{{ $selectedFormTitle }}</h2>
                    <p class="text-[12px] text-slate-500 mt-1">{{ __('Submitted Questionnaire Responses & E-Signature Audit Trail') }}</p>
                </div>

                <div class="py-4 overflow-y-auto flex-1 flex flex-col gap-6">
                    <div class="space-y-3">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Responses') }}</h4>
                        <div class="space-y-4">
                            @if ($selectedFormData)
                                @foreach ($selectedFormData as $key => $val)
                                    <div>
                                        <label class="block text-[13px] font-semibold text-slate-500 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        <div class="text-[14px] text-slate-800 font-medium">
                                            @if (is_bool($val))
                                                {{ $val ? __('Yes') : __('No') }}
                                            @else
                                                {{ $val }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-[13px] italic text-slate-400">{{ __('No responses captured.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('E-Signature Audit Trail') }}</h4>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-[12px] space-y-2 font-mono">
                            <div class="flex justify-between items-center"><span class="text-slate-500">{{ __('E-Signed By:') }}</span> <span class="font-bold text-slate-800">{{ $selectedFormSignature }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">{{ __('Signed At:') }}</span> <span class="text-slate-800">{{ $selectedFormSignedAt }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">{{ __('IP Address:') }}</span> <span class="text-slate-800">{{ $selectedFormIp }}</span></div>
                            <div class="pt-3 mt-3 border-t border-slate-200 text-[11px] text-emerald-600 font-bold flex items-center gap-1.5">
                                <flux:icon.check-circle class="w-4 h-4" />
                                {{ __('Secured electronic record locked.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button wire:click="$set('showFormModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-[13px] transition-colors">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($activeTab === 'care_coordination')
        <div class="mt-2">
            <livewire:patients.patient-care-coordination :patient="$patient" />
        </div>
    @endif

    @if ($activeTab === 'radiology')
        <div class="mt-2">
            <livewire:patients.patient-radiology :patient="$patient" />
        </div>
    @endif
</div>
