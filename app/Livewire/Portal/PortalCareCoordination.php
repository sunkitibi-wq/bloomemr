<?php

namespace App\Livewire\Portal;

use App\Models\DirectMessage;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clinical Care Sharing Log')]
class PortalCareCoordination extends Component
{
    public ?Patient $patient = null;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function render(): View
    {
        $messages = collect();

        if ($this->patient) {
            $messages = DirectMessage::where('patient_id', $this->patient->id)
                ->with(['sender', 'consent'])
                ->latest()
                ->get();
        }

        return view('livewire.portal.portal-care-coordination', [
            'messages' => $messages,
        ])->layout('layouts.blank');
    }
}
