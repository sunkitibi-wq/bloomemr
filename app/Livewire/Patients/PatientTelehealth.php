<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Services\DailyService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientTelehealth extends Component
{
    public Patient $patient;

    // Telehealth states
    public bool $isCallActive = false;

    public bool $isMuted = false;

    public bool $isCameraOff = false;

    public string $callStatus = 'waiting'; // waiting, connected, ended

    public string $roomUrl = '';

    public string $meetingToken = '';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function joinCall(): void
    {
        $roomName = "bloom-telehealth-patient-{$this->patient->id}";

        $daily = app(DailyService::class);
        $room = $daily->getOrCreateRoom($roomName);

        if ($room) {
            $this->roomUrl = $room['url'];
            $token = $daily->createMeetingToken($roomName, auth()->user()->name, true);
            if ($token) {
                $this->meetingToken = $token;
                $this->isCallActive = true;
                $this->callStatus = 'connected';

                return;
            }
        }

        // Fallback to simulation if configuration is missing/invalid
        $this->isCallActive = true;
        $this->callStatus = 'connecting';
    }

    public function endCall(): void
    {
        $this->isCallActive = false;
        $this->callStatus = 'ended';
    }

    public function toggleMute(): void
    {
        $this->isMuted = ! $this->isMuted;
    }

    public function toggleCamera(): void
    {
        $this->isCameraOff = ! $this->isCameraOff;
    }

    public function render(): View
    {
        return view('livewire.patients.patient-telehealth');
    }
}
