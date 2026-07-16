<?php

namespace App\Livewire\Clinical;

use App\Models\ClinicalNote;
use App\Models\Patient;
use App\Services\AiClinicalAssistantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Global AI Assistant')]
class AiAssistantDashboard extends Component
{
    public $transcript = '';

    public $templateType = 'SOAP';

    public $patientId = null;

    public $draft = null;

    public function generateDraft()
    {
        $this->validate([
            'transcript' => 'required|string',
            'templateType' => 'required|string',
        ]);

        $service = app(AiClinicalAssistantService::class);
        $this->draft = $service->generateDraftFromTranscript($this->templateType, $this->transcript);

        session()->flash('message', 'Draft generated successfully.');
    }

    public function saveAsNote()
    {
        $this->validate([
            'patientId' => 'required',
            'draft' => 'required|array',
        ]);

        $note = ClinicalNote::create([
            'patient_id' => $this->patientId,
            'provider_id' => Auth::id(),
            'type' => 'Progress Note',
            'content' => json_encode($this->draft),
            'status' => 'draft',
            'signed_at' => null,
            'signed_by' => null,
            'encounter_id' => null,
        ]);

        session()->flash('message', "Draft saved as Clinical Note #{$note->id} for patient.");
        $this->reset(['transcript', 'draft', 'patientId']);
    }

    public function render()
    {
        $patients = Patient::where('practice_id', Auth::user()->practice_id)->get();

        return view('livewire.clinical.ai-assistant-dashboard', [
            'patients' => $patients,
        ]);
    }
}
