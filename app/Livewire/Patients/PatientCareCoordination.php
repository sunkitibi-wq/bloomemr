<?php

namespace App\Livewire\Patients;

use App\Models\DirectMessage;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Services\DirectMessagingService;
use Livewire\Component;

class PatientCareCoordination extends Component
{
    public Patient $patient;

    // Form inputs
    public string $recipientName = '';

    public string $recipientAddress = '';

    public string $subject = '';

    public string $scope = 'CCD';

    public ?int $consentFormId = null;

    public ?string $expiresAt = null;

    // UI state
    public ?array $selectedMessagePayload = null;

    public bool $showPayloadModal = false;

    protected array $rules = [
        'recipientName' => 'required|string|max:255',
        'recipientAddress' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'scope' => 'required|string|in:CCD,referral,care_plan',
        'consentFormId' => 'required|integer',
        'expiresAt' => 'nullable|date|after:today',
    ];

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->subject = 'Clinical Summary (CCD) for '.$patient->full_name;
    }

    public function getSignedConsentsProperty()
    {
        return PatientForm::where('patient_id', $this->patient->id)
            ->where('status', 'completed')
            ->whereHas('template', function ($query) {
                $query->where('type', 'consent');
            })
            ->get();
    }

    public function getDirectMessagesProperty()
    {
        return DirectMessage::where('patient_id', $this->patient->id)
            ->with(['sender', 'consent'])
            ->latest()
            ->get();
    }

    public function sendSummary(): void
    {
        $this->validate();

        try {
            $service = app(DirectMessagingService::class);
            $service->sendDirectMessage(
                $this->patient,
                $this->recipientName,
                $this->recipientAddress,
                $this->subject,
                $this->scope,
                $this->consentFormId,
                $this->expiresAt
            );

            // Reset form inputs except scope/subject default
            $this->recipientName = '';
            $this->recipientAddress = '';
            $this->consentFormId = null;
            $this->expiresAt = null;
            $this->subject = 'Clinical Summary (CCD) for '.$this->patient->full_name;

            session()->flash('message', 'Clinical record shared successfully via Direct Messaging.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to share clinical record: '.$e->getMessage());
        }
    }

    public function viewPayload(int $id): void
    {
        $message = DirectMessage::where('patient_id', $this->patient->id)->findOrFail($id);
        $this->selectedMessagePayload = $message->payload;
        $this->showPayloadModal = true;
    }

    public function closePayloadModal(): void
    {
        $this->showPayloadModal = false;
        $this->selectedMessagePayload = null;
    }

    public function render()
    {
        return view('livewire.patients.patient-care-coordination', [
            'consents' => $this->signedConsents,
            'messages' => $this->directMessages,
        ]);
    }
}
