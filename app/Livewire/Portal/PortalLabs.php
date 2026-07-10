<?php

namespace App\Livewire\Portal;

use App\Models\LabResult;
use App\Models\Patient;
use App\Services\AiClinicalAssistantService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Lab Results')]
class PortalLabs extends Component
{
    public ?Patient $patient = null;

    public ?LabResult $selectedLab = null;

    public string $aiExplanation = '';

    public bool $isProcessingAi = false;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            // Fallback for testing from clinician side
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function selectLab(int $labId): void
    {
        $this->selectedLab = LabResult::where('patient_id', $this->patient->id)->findOrFail($labId);
        $this->aiExplanation = '';
    }

    public function explainWithAi(): void
    {
        if (! $this->selectedLab) {
            return;
        }

        $this->isProcessingAi = true;

        $service = app(AiClinicalAssistantService::class);
        $this->aiExplanation = $service->explainLabResult($this->selectedLab->result_data);

        $this->isProcessingAi = false;
    }

    public function render(): View
    {
        $labs = collect();
        if ($this->patient) {
            $labs = LabResult::where('patient_id', $this->patient->id)
                ->with('labOrder')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.portal.portal-labs', [
            'labs' => $labs,
        ])->layout('layouts.blank');
    }
}
