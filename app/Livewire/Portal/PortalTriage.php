<?php

namespace App\Livewire\Portal;

use App\Models\Patient;
use App\Services\AiClinicalAssistantService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Symptom Triage')]
class PortalTriage extends Component
{
    public ?Patient $patient = null;

    public string $symptoms = '';

    public array $triageResult = [];

    public bool $isProcessingAi = false;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function evaluateSymptoms(): void
    {
        $this->validate([
            'symptoms' => 'required|string|min:10',
        ]);

        $this->isProcessingAi = true;

        $service = app(AiClinicalAssistantService::class);
        $this->triageResult = $service->triageSymptoms($this->symptoms);

        $this->isProcessingAi = false;
    }

    public function resetTriage(): void
    {
        $this->symptoms = '';
        $this->triageResult = [];
    }

    public function render(): View
    {
        return view('livewire.portal.portal-triage')->layout('layouts.blank');
    }
}
