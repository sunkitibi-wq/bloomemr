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

    public $allergies = '';
    public $problem_list = '';
    public $past_medical_history = '';
    public $surgical_history = '';
    public $family_history = '';
    public $social_history = '';

    public function updatedPatientId($value)
    {
        if ($value) {
            $patient = Patient::find($value);
            if ($patient) {
                $this->allergies = $patient->allergies;
                $this->problem_list = $patient->problem_list;
                $this->past_medical_history = $patient->past_medical_history;
                $this->surgical_history = $patient->surgical_history;
                $this->family_history = $patient->family_history;
                $this->social_history = $patient->social_history;
            }
        } else {
            $this->reset(['allergies', 'problem_list', 'past_medical_history', 'surgical_history', 'family_history', 'social_history']);
        }
    }

    public function savePatientDetails()
    {
        if ($this->patientId) {
            Patient::where('id', $this->patientId)->update([
                'allergies' => $this->allergies,
                'problem_list' => $this->problem_list,
                'past_medical_history' => $this->past_medical_history,
                'surgical_history' => $this->surgical_history,
                'family_history' => $this->family_history,
                'social_history' => $this->social_history,
            ]);
            session()->flash('message', 'Patient details saved successfully.');
        }
    }

    public function generateDraft()
    {
        $this->validate([
            'transcript' => 'required|string',
            'templateType' => 'required|string',
        ]);

        $service = app(AiClinicalAssistantService::class);
        
        $patientContext = [];
        if ($this->patientId) {
            $patientContext = [
                'Allergies' => $this->allergies,
                'Problem List' => $this->problem_list,
                'Past Medical History' => $this->past_medical_history,
                'Surgical History' => $this->surgical_history,
                'Family History' => $this->family_history,
                'Social History' => $this->social_history,
            ];
        }

        $this->draft = $service->generateDraftFromTranscript($this->templateType, $this->transcript, $patientContext);

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
