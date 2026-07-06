<?php

namespace App\Livewire\Portal;

use App\Models\Patient;
use App\Services\DailyService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Telehealth Consultation')]
class PortalTelehealth extends Component
{
    public ?Patient $patient = null;

    // Telehealth states
    public bool $isCallActive = false;

    public bool $isMuted = false;

    public bool $isCameraOff = false;

    public string $callStatus = 'waiting'; // waiting, connected, ended

    public string $roomUrl = '';

    public string $meetingToken = '';

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function joinCall(): void
    {
        if (! $this->patient) {
            return;
        }

        $roomName = "bloom-telehealth-patient-{$this->patient->id}";
        
        $daily = app(DailyService::class);
        $room = $daily->getOrCreateRoom($roomName);

        if ($room) {
            $this->roomUrl = $room['url'];
            $token = $daily->createMeetingToken($roomName, auth()->user()->name, false);
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

        // Simulate connection delay
        $this->dispatch('call-connecting');
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
        return view('livewire.portal.portal-telehealth')->layout('layouts.blank');
    }
}
