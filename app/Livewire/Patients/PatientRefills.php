<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\RefillRequest;
use App\Services\SurescriptsService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientRefills extends Component
{
    public Patient $patient;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function approve(int $requestId): void
    {
        $request = RefillRequest::where('patient_id', $this->patient->id)->findOrFail($requestId);

        $request->update(['status' => 'approved']);

        // Renew/Refill the medication
        $medication = $request->medication;

        $prescription = Prescription::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'medication_id' => $medication->id,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Transmit renewed prescription to Surescripts
        app(SurescriptsService::class)->transmitPrescription($prescription);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'approve_refill',
            entityType: 'prescription',
            entityId: $prescription->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Refill request approved. New prescription sent.'));
        $this->patient->load('medications');
    }

    public function deny(int $requestId): void
    {
        $request = RefillRequest::where('patient_id', $this->patient->id)->findOrFail($requestId);

        $request->update(['status' => 'denied']);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'deny_refill',
            entityType: 'refill_request',
            entityId: $request->id,
            patientId: $this->patient->id,
        );

        Flux::toast(text: __('Refill request denied.'));
    }

    public function render(): View
    {
        $refillRequests = RefillRequest::where('patient_id', $this->patient->id)
            ->with(['medication', 'requestedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.patients.patient-refills', [
            'refillRequests' => $refillRequests,
        ]);
    }
}
