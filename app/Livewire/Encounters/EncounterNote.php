<?php

namespace App\Livewire\Encounters;

use App\Actions\LogAudit;
use App\Models\ClinicalNote;
use App\Models\Encounter;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clinical Note')]
class EncounterNote extends Component
{
    public Encounter $encounter;

    public ?ClinicalNote $note = null;

    public string $template_type = '';

    public array $sections = [];

    public string $body = '';

    public bool $editing = false;

    // Carry Forward properties
    public ?ClinicalNote $priorNote = null;

    public array $selectedPriorSections = [];

    public bool $showCarryForwardModal = false;

    // CDS Algorithm properties
    public string $cdsAlgorithm = '';

    public string $cdsStep = 'start';

    public string $cdsTitrationPlan = '';

    // Ambient AI properties
    public string $ambientTranscript = '';

    public bool $isRecording = false;

    public int $recordingTimer = 0;

    public array $aiDraft = [];

    public bool $isProcessingAi = false;

    public array $aiSuggestions = [];

    public function mount(Encounter $encounter): void
    {
        $this->encounter = $encounter->load('patient');
        $this->note = $encounter->clinicalNotes()->latest()->first();
        $this->template_type = $encounter->type;

        if ($this->note) {
            $this->editing = true;
            $this->template_type = $this->note->template_type;
            $this->sections = $this->note->sections ?? [];
            $this->body = $this->note->body ?? '';
        } else {
            $this->initializeSections();
        }
    }

    public function updatedTemplateType(): void
    {
        $this->initializeSections();
    }

    public function initializeSections(): void
    {
        $this->sections = [];
        if ($this->template_type === 'SOAP') {
            $this->sections = ['subjective' => '', 'objective' => '', 'assessment' => '', 'plan' => ''];
        } elseif ($this->template_type === 'DAP') {
            $this->sections = ['data' => '', 'assessment' => '', 'plan' => ''];
        } elseif ($this->template_type === 'Intake') {
            $this->sections = [
                'reason_for_visit' => '',
                'hpi' => '',
                'past_psychiatric_history' => '',
                'medical_history' => '',
                'family_history' => '',
                'social_history' => '',
                'mental_status_exam' => '',
                'diagnostic_impression' => '',
                'plan' => '',
            ];
        } else {
            $this->sections = ['body' => ''];
        }
    }

    public function checkPriorEncounter(): void
    {
        $priorEncounter = Encounter::where('patient_id', $this->encounter->patient_id)
            ->where('id', '!=', $this->encounter->id)
            ->where('status', 'signed')
            ->latest()
            ->first();

        if ($priorEncounter) {
            $this->priorNote = $priorEncounter->clinicalNotes()->latest()->first();
            if ($this->priorNote && ! empty($this->priorNote->sections)) {
                $this->selectedPriorSections = [];
                foreach (array_keys($this->priorNote->sections) as $sec) {
                    $this->selectedPriorSections[$sec] = true;
                }
                $this->showCarryForwardModal = true;
            } else {
                Flux::toast(variant: 'warning', text: 'Prior encounter has no text sections.');
            }
        } else {
            Flux::toast(variant: 'warning', text: 'No prior signed encounter found to carry forward.');
        }
    }

    public function carryForward(): void
    {
        if ($this->priorNote && ! empty($this->priorNote->sections)) {
            foreach ($this->priorNote->sections as $sec => $val) {
                if (! empty($this->selectedPriorSections[$sec])) {
                    if (array_key_exists($sec, $this->sections)) {
                        $this->sections[$sec] = $val;
                    } else {
                        if (isset($this->sections['body'])) {
                            $this->sections['body'] .= "\n\n".ucfirst($sec).":\n".$val;
                        } elseif (isset($this->sections['subjective']) && in_array($sec, ['reason_for_visit', 'hpi'])) {
                            $this->sections['subjective'] .= "\n\n".$val;
                        } else {
                            $firstKey = array_key_first($this->sections);
                            if ($firstKey) {
                                $this->sections[$firstKey] .= "\n\n[".ucfirst($sec)." carried forward]:\n".$val;
                            }
                        }
                    }
                }
            }
            $this->showCarryForwardModal = false;
            Flux::toast(variant: 'success', text: 'Selected sections carried forward.');
        }
    }

    public function saveDraft(): void
    {
        $this->autoSave();
        Flux::toast(variant: 'success', text: 'Draft saved.');
    }

    public function autoSave(): void
    {
        $this->body = '';
        if ($this->template_type === 'Narrative') {
            $this->body = $this->sections['body'] ?? '';
        } else {
            foreach ($this->sections as $sec => $val) {
                if (! empty($val)) {
                    $this->body .= '## '.strtoupper(str_replace('_', ' ', $sec))."\n".$val."\n\n";
                }
            }
        }

        $data = [
            'encounter_id' => $this->encounter->id,
            'template_type' => $this->template_type,
            'sections' => $this->sections,
            'body' => $this->body,
            'draft' => ['sections' => $this->sections, 'body' => $this->body],
            'practice_id' => $this->encounter->practice_id ?? Auth::user()->practice_id,
        ];

        if ($this->editing) {
            $this->note->update($data);
        } else {
            $this->note = ClinicalNote::create($data);
            $this->editing = true;
        }
    }

    public function sign(): void
    {
        $this->autoSave();

        if ($this->note) {
            $this->note->update([
                'signed_by' => Auth::id(),
                'signed_at' => now(),
            ]);
        }

        $this->encounter->update([
            'status' => 'signed',
            'signed_by' => Auth::id(),
            'signed_at' => now(),
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'sign',
            entityType: 'clinical_note',
            entityId: $this->note?->id,
            patientId: $this->encounter->patient_id,
        );

        Flux::toast(variant: 'success', text: 'Note signed.');
        $this->redirect(route('patients.show', $this->encounter->patient), navigate: true);
    }

    public function selectCdsAlgorithm(string $algo): void
    {
        $this->cdsAlgorithm = $algo;
        $this->cdsStep = 'start';
        $this->cdsTitrationPlan = '';
    }

    public function selectCdsStep(string $step, string $titrationText = ''): void
    {
        $this->cdsStep = $step;
        if ($titrationText) {
            $this->cdsTitrationPlan = $titrationText;
        }
    }

    public function applyTitrationToNote(): void
    {
        if (empty($this->cdsTitrationPlan)) {
            Flux::toast(variant: 'danger', text: 'No titration plan recommendation selected.');

            return;
        }

        $targetKey = 'plan';
        if (array_key_exists($targetKey, $this->sections)) {
            $this->sections[$targetKey] = trim(($this->sections[$targetKey] ?? '')."\n\n".$this->cdsTitrationPlan);
            Flux::toast(variant: 'success', text: 'Titration plan recommendation appended to Plan.');
        } else {
            $firstKey = array_key_first($this->sections);
            if ($firstKey) {
                $this->sections[$firstKey] = trim(($this->sections[$firstKey] ?? '')."\n\n".$this->cdsTitrationPlan);
                Flux::toast(variant: 'success', text: 'Titration plan recommendation appended.');
            }
        }
        $this->cdsAlgorithm = '';
    }

    public function startAmbientRecording(): void
    {
        $this->isRecording = true;
        $this->recordingTimer = 0;
        $this->ambientTranscript = '';
        $this->aiDraft = [];
        Flux::toast(variant: 'success', text: __('Ambient listening active. Please conduct patient interview.'));
    }

    public function stopAmbientRecording(): void
    {
        $this->isRecording = false;

        // Populate a highly realistic clinical dialogue based on active template
        if ($this->template_type === 'SOAP') {
            $this->ambientTranscript = "Clinician: Hello Timmy, how are you feeling today?\n" .
                "Patient: I am sleeping poorly, sleeping only about 4 or 5 hours a night. Feeling very anxious about my new job.\n" .
                "Clinician: Okay, I notice you are showing some mild psychomotor agitation. Let's look at your vitals. BP is 120/80, pulse is 72.\n" .
                "Patient: Yes, my heart feels like it races sometimes.\n" .
                "Clinician: I will diagnose you with generalized anxiety disorder and moderate sleep onset insomnia.\n" .
                "Clinician: Let's titrate your dosage of Buspar to 10mg twice daily and follow up in two weeks.";
        } elseif ($this->template_type === 'DAP') {
            $this->ambientTranscript = "Patient reports sleeping poorly and racing heart symptoms.\n" .
                "Attending noticed mild psychomotor agitation on exam. Vitals are BP 120/80 and pulse 72.\n" .
                "Impression is generalized anxiety disorder and moderate sleep onset insomnia.\n" .
                "Plan is to titrate Buspar to 10mg twice daily. Follow up in two weeks.";
        } else {
            $this->ambientTranscript = "Reason for visit: presenting with sleep onset insomnia.\n" .
                "HPI: patient describes gradual onset of clinical symptoms of anxiety.\n" .
                "Past psych: denies prior psychiatric hospitalizations.\n" .
                "MSE: alert and oriented, shows psychomotor agitation, anxious affect.\n" .
                "Diagnostic impression: assess for generalized anxiety disorder vs panic disorder.\n" .
                "Plan: titrate Buspar, follow up in two weeks.";
        }

        Flux::toast(variant: 'success', text: __('Dictation finished. Ambient dialogue transcribed.'));
    }

    public function generateAiDraft(): void
    {
        $this->isProcessingAi = true;
        
        $service = app(\App\Services\AiClinicalAssistantService::class);
        $this->aiDraft = $service->generateDraftFromTranscript($this->template_type, $this->ambientTranscript);
        
        $this->isProcessingAi = false;
        Flux::toast(variant: 'success', text: __('Ambient note drafted successfully. Review below.'));
    }

    public function applyAiDraft(): void
    {
        if (empty($this->aiDraft)) {
            Flux::toast(variant: 'danger', text: __('No AI draft available.'));
            return;
        }

        foreach ($this->aiDraft as $sec => $text) {
            if (array_key_exists($sec, $this->sections)) {
                $this->sections[$sec] = $text;
            }
        }

        $this->aiDraft = [];
        Flux::toast(variant: 'success', text: __('AI draft merged into note.'));
    }

    public function suggestAiPhrases(string $sectionKey): void
    {
        $text = $this->sections[$sectionKey] ?? '';
        if (empty($text)) {
            Flux::toast(variant: 'warning', text: __('Section is empty. Add details first.'));
            return;
        }

        $service = app(\App\Services\AiClinicalAssistantService::class);
        $this->aiSuggestions[$sectionKey] = $service->suggestSmartPhrases($text)->toArray();
        Flux::toast(variant: 'success', text: __('AI smart phrase recommendations retrieved.'));
    }

    public function applySuggestedPhrase(string $sectionKey, string $expansion): void
    {
        if (array_key_exists($sectionKey, $this->sections)) {
            $current = trim($this->sections[$sectionKey]);
            $this->sections[$sectionKey] = $current ? $current . ' ' . $expansion : $expansion;
            
            // Remove from suggestions list
            if (isset($this->aiSuggestions[$sectionKey])) {
                $this->aiSuggestions[$sectionKey] = array_filter(
                    $this->aiSuggestions[$sectionKey],
                    fn($item) => $item['expansion'] !== $expansion
                );
            }
            Flux::toast(variant: 'success', text: __('Smart phrase appended.'));
        }
    }

    public function render(): View
    {
        return view('livewire.encounters.encounter-note');
    }
}
