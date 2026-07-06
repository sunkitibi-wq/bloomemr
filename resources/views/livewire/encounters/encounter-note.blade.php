<flux:main>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('patients.index') }}" wire:navigate>{{ __('Patients') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('patients.show', $encounter->patient) }}" wire:navigate>{{ $encounter->patient->full_name }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Clinical Note') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Clinical Note') }}</flux:heading>
            <flux:text class="mt-1">
                {{ $encounter->patient->full_name }} &middot;
                {{ $encounter->encounter_date->format('M j, Y g:i A') }}
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="checkPriorEncounter">
                {{ __('Carry Forward') }}
            </flux:button>
            <flux:button wire:click="saveDraft">
                {{ __('Save Draft') }}
            </flux:button>
            <flux:button variant="primary" wire:click="sign">
                {{ __('Sign & Complete') }}
            </flux:button>
        </div>
    </div>

    <!-- Note Settings & Auto-save Poll -->
    <div class="mt-6 flex flex-wrap items-center gap-4 bg-neutral-50 dark:bg-zinc-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700" wire:poll.30s="autoSave">
        <div>
            <flux:label for="template_type">{{ __('Note Template') }}</flux:label>
            <flux:select id="template_type" wire:model.live="template_type" class="mt-1 max-w-xs">
                <flux:select.option value="SOAP">{{ __('SOAP (Subjective/Objective/Assessment/Plan)') }}</flux:select.option>
                <flux:select.option value="DAP">{{ __('DAP (Data/Assessment/Plan)') }}</flux:select.option>
                <flux:select.option value="Narrative">{{ __('Narrative (Free-text)') }}</flux:select.option>
                <flux:select.option value="Intake">{{ __('Intake / Psychiatric Eval') }}</flux:select.option>
            </flux:select>
        </div>

        <flux:spacer />

        <div class="text-xs text-neutral-500 flex items-center gap-1.5 self-end">
            <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
            <span>{{ __('Draft auto-saving every 30 seconds.') }}</span>
            @if ($editing)
                <span class="ml-2 font-mono bg-neutral-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded">{{ __('Version :v', ['v' => $note->version]) }}</span>
            @endif
        </div>
    </div>

    <!-- Main Editor Layout: Form Editor + CDS Sidebar -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Editor Column -->
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit="saveDraft" class="space-y-6">
                @foreach (array_keys($sections) as $sectionKey)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label for="section-{{ $sectionKey }}">{{ strtoupper(str_replace('_', ' ', $sectionKey)) }}</flux:label>
                            <button type="button" wire:click="suggestAiPhrases('{{ $sectionKey }}')" class="text-xs text-primary hover:underline flex items-center gap-1">
                                <flux:icon.pencil-square class="size-3.5" />
                                <span>{{ __('AI Suggest Phrases') }}</span>
                            </button>
                        </div>
                        
                        <!-- Textarea with Smart Phrase listener -->
                        <div x-data="{
                            query: '',
                            showMenu: false,
                            caretPos: 0,
                            phrases: [],
                            cursorIndex: 0,
                            
                            init() {
                                this.$watch('query', value => {
                                    if (value.length > 0) {
                                        // Query matching smart phrases debounced
                                        fetch(`/api/smart-phrases?q=${encodeURIComponent(value)}`)
                                            .then(res => res.json())
                                            .then(data => {
                                                this.phrases = data;
                                                this.showMenu = data.length > 0;
                                                this.cursorIndex = 0;
                                            });
                                    } else {
                                        this.showMenu = false;
                                        this.phrases = [];
                                    }
                                });
                            },

                            onKeydown(e) {
                                if (this.showMenu) {
                                    if (e.key === 'ArrowDown') {
                                        e.preventDefault();
                                        this.cursorIndex = (this.cursorIndex + 1) % this.phrases.length;
                                    } else if (e.key === 'ArrowUp') {
                                        e.preventDefault();
                                        this.cursorIndex = (this.cursorIndex - 1 + this.phrases.length) % this.phrases.length;
                                    } else if (e.key === 'Enter') {
                                        e.preventDefault();
                                        this.selectPhrase(this.phrases[this.cursorIndex]);
                                    } else if (e.key === 'Escape') {
                                        this.showMenu = false;
                                    }
                                }
                            },

                            onInput(e) {
                                const val = e.target.value;
                                const selEnd = e.target.selectionEnd;
                                const textBeforeCaret = val.substring(0, selEnd);
                                const dotIndex = textBeforeCaret.lastIndexOf('.');
                                
                                // Check if care is right after a dot and a word: e.g. .adhd
                                if (dotIndex !== -1 && dotIndex >= textBeforeCaret.lastIndexOf(' ')) {
                                    const word = textBeforeCaret.substring(dotIndex + 1);
                                    if (!word.includes(' ')) {
                                        this.query = word;
                                        this.caretPos = dotIndex;
                                        return;
                                    }
                                }
                                this.query = '';
                            },

                            selectPhrase(phrase) {
                                const textarea = this.$refs.textarea;
                                const val = textarea.value;
                                const selEnd = textarea.selectionEnd;
                                const before = val.substring(0, this.caretPos);
                                const after = val.substring(selEnd);
                                const newVal = before + phrase.expansion + after;
                                
                                textarea.value = newVal;
                                // Trigger input event to update Livewire model
                                textarea.dispatchEvent(new Event('input'));
                                
                                // Reset state
                                this.showMenu = false;
                                
                                // Put focus back and place cursor after expansion
                                this.$nextTick(() => {
                                    textarea.focus();
                                    const newPos = this.caretPos + phrase.expansion.length;
                                    textarea.setSelectionRange(newPos, newPos);
                                });
                            }
                        }" class="relative mt-1">
                            <textarea
                                id="section-{{ $sectionKey }}"
                                x-ref="textarea"
                                wire:model="sections.{{ $sectionKey }}"
                                rows="{{ $template_type === 'Narrative' ? 25 : 8 }}"
                                class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary font-sans leading-relaxed"
                                @input="onInput"
                                @keydown="onKeydown"
                            ></textarea>

                            <!-- Autocomplete Floating Dropdown Menu -->
                            <div x-show="showMenu" x-cloak class="absolute z-50 bg-white dark:bg-zinc-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-lg mt-1 w-64 max-h-48 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-700">
                                <template x-for="(phrase, index) in phrases" :key="phrase.id">
                                    <div
                                        @click="selectPhrase(phrase)"
                                        class="px-3 py-2 text-xs cursor-pointer hover:bg-neutral-50 dark:hover:bg-zinc-700 flex justify-between items-center"
                                        :class="index === cursorIndex ? 'bg-neutral-100 dark:bg-zinc-700' : ''"
                                    >
                                        <div>
                                            <span class="font-bold text-primary">.</span><span class="font-bold" x-text="phrase.trigger"></span>
                                            <span class="text-neutral-400 block truncate" x-text="phrase.category"></span>
                                        </div>
                                        <span class="text-neutral-400 font-mono text-[10px]">enter</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- AI Suggested Phrases list -->
                        @if (!empty($aiSuggestions[$sectionKey]))
                            <div class="mt-2 flex flex-wrap gap-2 items-center bg-zinc-50 dark:bg-zinc-800/40 p-2.5 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider">{{ __('AI Suggestions:') }}</span>
                                @foreach ($aiSuggestions[$sectionKey] as $sugg)
                                    <button 
                                        type="button" 
                                        wire:click="applySuggestedPhrase('{{ $sectionKey }}', '{{ addslashes($sugg['expansion']) }}')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 hover:border-primary rounded-lg text-zinc-750 dark:text-zinc-350 transition-colors shadow-2xs"
                                        title="{{ $sugg['expansion'] }}"
                                    >
                                        <flux:icon.plus class="size-3 text-primary" />
                                        <span>.{{ $sugg['trigger'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </form>
        </div>

        <!-- Clinical Decision Support Column -->
        <div class="space-y-6">
            <!-- Ambient AI Assistant Card -->
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <flux:icon.pencil-square class="size-5 text-primary" />
                            {{ __('Ambient AI Assistant') }}
                        </flux:heading>
                        <flux:text class="text-xs mt-1">
                            {{ __('Record dialogue to draft structured clinical notes.') }}
                        </flux:text>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Dictation Recording Controls -->
                    <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/40 text-center space-y-3">
                        @if ($isRecording)
                            <div class="flex items-center justify-center gap-3">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                <span class="text-xs font-semibold text-red-600 dark:text-red-400 font-mono tracking-widest animate-pulse">{{ __('RECORDING DIALOGUE...') }}</span>
                            </div>
                            <flux:button variant="danger" wire:click="stopAmbientRecording" class="w-full">
                                <flux:icon.stop class="size-4 mr-2" />
                                {{ __('Stop Dictation') }}
                            </flux:button>
                        @else
                            <div class="text-xs text-zinc-400">
                                {{ __('Ready to transcribe clinician-patient interview') }}
                            </div>
                            <flux:button type="button" wire:click="startAmbientRecording" class="w-full">
                                <flux:icon.microphone class="size-4 mr-2 text-primary" />
                                {{ __('Start Dictation') }}
                            </flux:button>
                        @endif
                    </div>

                    <!-- Transcript text input -->
                    <flux:field>
                        <flux:label>{{ __('Conversational Transcript / Raw Notes') }}</flux:label>
                        <flux:textarea 
                            wire:model.live="ambientTranscript" 
                            placeholder="{{ __('Paste raw dialogue transcripts or type unstructured clinical observations here...') }}" 
                            rows="5"
                        />
                    </flux:field>

                    <flux:button 
                        variant="primary" 
                        wire:click="generateAiDraft" 
                        class="w-full"
                        wire:loading.attr="disabled"
                        :disabled="empty($ambientTranscript)"
                    >
                        <span wire:loading.remove wire:target="generateAiDraft" class="flex items-center gap-1.5 justify-center">
                            <flux:icon.pencil-square class="size-4" />
                            {{ __('Generate AI Draft') }}
                        </span>
                        <span wire:loading wire:target="generateAiDraft" class="flex items-center justify-center gap-1.5">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Drafting Note...') }}
                        </span>
                    </flux:button>

                    <!-- Preview Draft & Apply -->
                    @if (!empty($aiDraft))
                        <div class="border rounded-xl border-green-200 dark:border-green-800 p-4 bg-green-50/50 dark:bg-green-950/10 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-green-700 dark:text-green-400">
                                    {{ __('AI Draft Preview') }}
                                </span>
                            </div>

                            <div class="space-y-2.5 max-h-48 overflow-y-auto text-xs divide-y divide-green-100 dark:divide-green-900/50">
                                @foreach ($aiDraft as $secKey => $secText)
                                    <div class="pt-2 first:pt-0">
                                        <span class="font-bold text-zinc-700 dark:text-zinc-300 block uppercase text-[9px] tracking-wider">{{ str_replace('_', ' ', $secKey) }}</span>
                                        <p class="text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed">{{ $secText }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <flux:button type="button" variant="primary" wire:click="applyAiDraft" class="w-full bg-green-600 hover:bg-green-700 border-0">
                                <flux:icon.check-circle class="size-4 mr-2" />
                                {{ __('Apply to Note') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div>
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <flux:icon.presentation-chart-line class="size-5 text-primary" />
                        {{ __('Clinical Decision Support') }}
                    </flux:heading>
                    <flux:text class="text-xs mt-1">
                        {{ __('Evidence-based clinical guidelines and titration algorithms.') }}
                    </flux:text>
                </div>

                @if (empty($cdsAlgorithm))
                    <!-- Algorithm Selection -->
                    <div class="space-y-3">
                        <flux:button class="w-full justify-start" wire:click="selectCdsAlgorithm('adhd')">
                            <flux:icon.presentation-chart-line class="size-4 mr-2" />
                            {{ __('ADHD Titration Guide') }}
                        </flux:button>
                        <flux:button class="w-full justify-start" wire:click="selectCdsAlgorithm('depression')">
                            <flux:icon.heart class="size-4 mr-2" />
                            {{ __('Depression SSRI Guide') }}
                        </flux:button>
                    </div>
                @else
                    <!-- Active Algorithm Flowchart -->
                    <div class="border rounded-lg border-zinc-200 dark:border-zinc-700 p-4 bg-zinc-50 dark:bg-zinc-800/50 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">
                                {{ $cdsAlgorithm === 'adhd' ? __('ADHD Algorithm') : __('Depression Algorithm') }}
                            </span>
                            <flux:button variant="ghost" size="sm" wire:click="selectCdsAlgorithm('')">
                                {{ __('Back') }}
                            </flux:button>
                        </div>

                        @if ($cdsAlgorithm === 'adhd')
                            <!-- ADHD Steps -->
                            @if ($cdsStep === 'start')
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('1. Select first-line titration class:') }}</p>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('stimulant', 'First-line Stimulant: Initiate Methylphenidate 5mg QAM. Titrate weekly by 5mg. Monitor heart rate, blood pressure, and appetite.')">
                                        {{ __('Stimulant (First-Line Preferred)') }}
                                    </flux:button>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('non_stimulant', 'First-line Non-Stimulant: Initiate Atomoxetine 10mg daily. Increase to 40mg after 7 days. Monitor liver function if clinical signs arise.')">
                                        {{ __('Non-Stimulant (Anxiety / Tics / Abuse Risk)') }}
                                    </flux:button>
                                </div>
                            @elseif ($cdsStep === 'stimulant')
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('2. Stimulant Selection:') }}</p>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('stimulant_titrate', 'Titration Plan: Methylphenidate 5mg daily in morning for 7 days, then increase to 10mg daily. Return for follow-up and vitals check in 2 weeks.')">
                                        {{ __('Generic Methylphenidate (IR/ER)') }}
                                    </flux:button>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('stimulant_titrate', 'Titration Plan: Adderall 5mg daily in morning. Titrate to 10mg weekly as tolerated. Monitor cardiac status and sleep.')">
                                        {{ __('Generic Amphetamine Mixed Salts (IR/ER)') }}
                                    </flux:button>
                                </div>
                            @elseif ($cdsStep === 'stimulant_titrate')
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('3. Titration recommendation generated!') }}</p>
                                    <p class="text-xs text-zinc-500 italic bg-white dark:bg-zinc-900 border rounded p-2">{{ $cdsTitrationPlan }}</p>
                                </div>
                            @elseif ($cdsStep === 'non_stimulant')
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Non-Stimulant Treatment Plan:') }}</p>
                                    <p class="text-xs text-zinc-500 italic bg-white dark:bg-zinc-900 border rounded p-2">{{ $cdsTitrationPlan }}</p>
                                </div>
                            @endif

                        @elseif ($cdsAlgorithm === 'depression')
                            <!-- Depression Steps -->
                            @if ($cdsStep === 'start')
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('1. Choose first-line agent class:') }}</p>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('ssri', 'Depression First-Line: SSRI. Initiate Escitalopram 5mg daily. Increase to 10mg daily after 1 week. Warning: Monitor for mood activation and black-box suicide warnings.')">
                                        {{ __('SSRI (Escitalopram / Sertraline)') }}
                                    </flux:button>
                                    <flux:button size="sm" class="w-full" wire:click="selectCdsStep('atypical', 'Depression Alternative: Atypical (Wellbutrin). Initiate 75mg daily. Increase to 150mg daily after 7 days. Contraindicated in eating disorders / seizures.')">
                                        {{ __('Atypical Antidepressant (Wellbutrin)') }}
                                    </flux:button>
                                </div>
                            @elseif ($cdsStep === 'ssri')
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('SSRI First-line Treatment Plan:') }}</p>
                                    <p class="text-xs text-zinc-500 italic bg-white dark:bg-zinc-900 border rounded p-2">{{ $cdsTitrationPlan }}</p>
                                </div>
                            @elseif ($cdsStep === 'atypical')
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Alternative Treatment Plan:') }}</p>
                                    <p class="text-xs text-zinc-500 italic bg-white dark:bg-zinc-900 border rounded p-2">{{ $cdsTitrationPlan }}</p>
                                </div>
                            @endif
                        @endif

                        @if (!empty($cdsTitrationPlan))
                            <flux:button variant="primary" class="w-full mt-4" wire:click="applyTitrationToNote">
                                {{ __('Apply Recommendation to Plan') }}
                            </flux:button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Carry Forward Modal Dialog -->
    <div x-data="{ open: @entangle('showCarryForwardModal') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Carry Forward Prior Note') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Select sections to carry forward from the last signed note.') }}</flux:text>
                </div>

                @if ($priorNote)
                    <div class="space-y-3">
                        @foreach ($priorNote->sections as $sec => $val)
                            @if (!empty($val))
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-zinc-800 cursor-pointer">
                                    <input type="checkbox" wire:model="selectedPriorSections.{{ $sec }}" class="mt-1 rounded border-neutral-300 dark:border-neutral-700 text-primary focus:ring-primary" />
                                    <div class="flex-1">
                                        <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">{{ strtoupper(str_replace('_', ' ', $sec)) }}</span>
                                        <span class="text-xs text-neutral-500 block truncate max-w-xs">{{ $val }}</span>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('showCarryForwardModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" wire:click="carryForward">
                        {{ __('Import Selected') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</flux:main>
