<?php

namespace App\Livewire\Portal;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Visit Summaries')]
class PortalVisitSummaries extends Component
{
    public ?Patient $patient = null;

    public ?Encounter $activeEncounter = null;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function selectEncounter(int $encounterId): void
    {
        if (! $this->patient) {
            return;
        }

        $this->activeEncounter = Encounter::where('patient_id', $this->patient->id)
            ->whereNotNull('released_to_portal_at')
            ->with(['provider', 'clinicalNotes'])
            ->findOrFail($encounterId);
    }

    public function render(): View
    {
        $encounters = collect();

        if ($this->patient) {
            $encounters = Encounter::where('patient_id', $this->patient->id)
                ->whereNotNull('released_to_portal_at')
                ->where('status', 'signed')
                ->with(['provider', 'clinicalNotes'])
                ->orderByDesc('encounter_date')
                ->get();
        }

        return view('livewire.portal.portal-visit-summaries', [
            'encounters' => $encounters,
        ])->layout('layouts.blank');
    }
}
