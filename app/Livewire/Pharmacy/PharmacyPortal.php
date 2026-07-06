<?php

namespace App\Livewire\Pharmacy;

use App\Models\Prescription;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pharmacy Portal')]
class PharmacyPortal extends Component
{
    public $prescriptions;

    public function mount(): void
    {
        if (! Auth::user()?->can('view_prescriptions')) {
            abort(403);
        }

        $this->loadPrescriptions();
    }

    public function loadPrescriptions(): void
    {
        $this->prescriptions = Prescription::query()
            ->with(['patient', 'medication', 'pharmacy'])
            ->where('practice_id', Auth::user()->practice_id)
            ->orderByRaw("CASE WHEN fulfillment_status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();
    }

    public function updateFulfillmentStatus(int $prescriptionId, string $status): void
    {
        if (! Auth::user()?->can('update_prescriptions')) {
            abort(403);
        }

        $prescription = Prescription::query()
            ->where('practice_id', Auth::user()->practice_id)
            ->findOrFail($prescriptionId);

        $prescription->update([
            'fulfillment_status' => $status,
            'status' => $status === 'filled' ? 'filled' : $prescription->status,
            'dispensed_at' => $status === 'filled' ? now() : $prescription->dispensed_at,
        ]);

        Flux::toast(variant: 'success', text: __('Prescription updated.'));
        $this->loadPrescriptions();
    }

    public function render(): View
    {
        return view('livewire.pharmacy.pharmacy-portal')->layout('layouts.app');
    }
}
