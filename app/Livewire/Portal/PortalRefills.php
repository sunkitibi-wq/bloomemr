<?php

namespace App\Livewire\Portal;

use App\Models\Medication;
use App\Models\Patient;
use App\Models\RefillRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Refill Requests')]
class PortalRefills extends Component
{
    public ?Patient $patient = null;

    // Request Form fields
    public string $medicationId = '';

    public string $notes = '';

    public bool $isFormOpen = false;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function openRequestForm(): void
    {
        $this->medicationId = '';
        $this->notes = '';
        $this->isFormOpen = true;
    }

    public function submitRequest(): void
    {
        $this->validate([
            'medicationId' => 'required|exists:medications,id',
            'notes' => 'nullable|string',
        ]);

        $medication = Medication::where('patient_id', $this->patient->id)->findOrFail((int) $this->medicationId);

        RefillRequest::create([
            'practice_id' => $this->patient->practice_id,
            'patient_id' => $this->patient->id,
            'medication_id' => $medication->id,
            'requested_by' => auth('portal')->id() ?? auth()->id(), // fallback for clinician preview
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        session()->flash('message', __('Refill request submitted successfully.'));
        $this->isFormOpen = false;
    }

    public function render(): View
    {
        $medications = collect();
        $refillRequests = collect();

        if ($this->patient) {
            $medications = Medication::where('patient_id', $this->patient->id)
                ->where('status', 'active')
                ->get();

            $refillRequests = RefillRequest::where('patient_id', $this->patient->id)
                ->with(['medication', 'requestedBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.portal.portal-refills', [
            'medications' => $medications,
            'refillRequests' => $refillRequests,
        ])->layout('layouts.blank');
    }
}
