<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Models\RadiologyOrder;
use App\Models\RadiologyReport;
use App\Services\RadiologyService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class PatientRadiology extends Component
{
    use WithFileUploads;

    public Patient $patient;

    // Order Form
    public string $procedureName = '';
    public string $clinicalIndication = '';

    // Report Form / Modal State
    public bool $showReportModal = false;
    public ?RadiologyOrder $selectedOrder = null;
    public string $findings = '';
    public string $impression = '';
    public $scanFile = null;

    // View Report Modal
    public bool $showViewModal = false;
    public ?RadiologyReport $selectedReport = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function placeOrder(): void
    {
        $this->validate([
            'procedureName' => 'required|string|max:128',
            'clinicalIndication' => 'required|string',
        ]);

        $service = app(RadiologyService::class);
        $service->createOrder($this->patient, $this->procedureName, $this->clinicalIndication);

        $this->procedureName = '';
        $this->clinicalIndication = '';

        Flux::toast(variant: 'success', text: __('Radiology imaging order placed.'));
        $this->patient->load('radiologyOrders');
    }

    public function openReportModal(int $orderId): void
    {
        $this->selectedOrder = RadiologyOrder::findOrFail($orderId);
        $this->findings = '';
        $this->impression = '';
        $this->scanFile = null;
        $this->showReportModal = true;
    }

    public function submitReport(): void
    {
        $this->validate([
            'findings' => 'required|string',
            'impression' => 'required|string',
            'scanFile' => 'nullable|file|mimes:pdf,jpeg,png,tiff|max:10240',
        ]);

        $path = null;
        if ($this->scanFile) {
            $path = $this->scanFile->store('radiology/' . $this->patient->id, 'public');
        }

        $service = app(RadiologyService::class);
        $service->uploadReport($this->selectedOrder, [
            'findings' => $this->findings,
            'impression' => $this->impression,
        ], $path);

        $this->showReportModal = false;
        Flux::toast(variant: 'success', text: __('Radiology report submitted and finalized.'));
        $this->patient->load('radiologyOrders.report');
    }

    public function viewReport(int $reportId): void
    {
        $this->selectedReport = RadiologyReport::with('order.orderedBy', 'radiologist')->findOrFail($reportId);
        $this->showViewModal = true;
    }

    public function render(): View
    {
        $orders = $this->patient->radiologyOrders()
            ->with(['orderedBy', 'report.radiologist'])
            ->latest()
            ->get();

        return view('livewire.patients.patient-radiology', [
            'orders' => $orders,
        ]);
    }
}
