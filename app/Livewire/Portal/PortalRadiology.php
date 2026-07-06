<?php

namespace App\Livewire\Portal;

use App\Models\Patient;
use App\Models\RadiologyOrder;
use App\Models\RadiologyReport;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Imaging Reports')]
class PortalRadiology extends Component
{
    public ?Patient $patient = null;

    // View Report Modal
    public bool $showViewModal = false;
    public ?RadiologyReport $selectedReport = null;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function viewReport(int $reportId): void
    {
        $this->selectedReport = RadiologyReport::with('order.orderedBy', 'radiologist')->findOrFail($reportId);
        
        // Ensure patient is authorized to view this report
        if ($this->patient && $this->selectedReport->patient_id !== $this->patient->id) {
            abort(403);
        }

        $this->showViewModal = true;
    }

    public function render(): View
    {
        $orders = collect();

        if ($this->patient) {
            $orders = RadiologyOrder::where('patient_id', $this->patient->id)
                ->where('status', 'reported')
                ->with(['orderedBy', 'report.radiologist'])
                ->latest()
                ->get();
        }

        return view('livewire.portal.portal-radiology', [
            'orders' => $orders,
        ])->layout('layouts.blank');
    }
}
