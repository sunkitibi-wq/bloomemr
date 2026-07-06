<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Practice;
use App\Services\Hl7IntegrationService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientLabs extends Component
{
    public Patient $patient;

    // Lab order fields
    public string $panel = 'Thyroid Panel';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function orderLabs(): void
    {
        $this->validate([
            'panel' => 'required|string|max:100',
        ]);

        $order = LabOrder::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'panel' => $this->panel,
            'status' => 'ordered',
            'requisition_pdf_path' => 'requisitions/REQ-'.rand(100000, 999999).'.pdf',
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'order_lab',
            entityType: 'lab_order',
            entityId: $order->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: "Lab Order for {$this->panel} generated successfully.");
        $this->patient->load('labOrders');
    }

    public function simulateQuestResult(int $orderId): void
    {
        $order = LabOrder::find($orderId);
        if (! $order || $order->status === 'completed') {
            Flux::toast(variant: 'danger', text: 'Invalid or already completed lab order.');

            return;
        }

        // Define mock observations depending on the panel type
        $observations = [];
        if ($order->panel === 'Thyroid Panel') {
            $observations = [
                ['name' => 'TSH', 'value' => '6.20', 'range' => '0.40-4.00', 'flag' => 'H', 'unit' => 'uIU/mL'],
                ['name' => 'Free T4', 'value' => '1.10', 'range' => '0.80-1.80', 'flag' => 'N', 'unit' => 'ng/dL'],
            ];
        } elseif ($order->panel === 'CBC') {
            $observations = [
                ['name' => 'WBC', 'value' => '1.80', 'range' => '4.00-11.00', 'flag' => 'LL', 'unit' => 'x10E3/uL'], // Critical Low
                ['name' => 'RBC', 'value' => '4.50', 'range' => '4.00-5.20', 'flag' => 'N', 'unit' => 'x10E6/uL'],
            ];
        } else {
            // General Metabolic
            $observations = [
                ['name' => 'Lithium Level', 'value' => '1.40', 'range' => '0.60-1.20', 'flag' => 'H', 'unit' => 'mmol/L'],
            ];
        }

        $hl7Service = app(Hl7IntegrationService::class);
        $hl7Payload = $hl7Service->generateMockQuestHl7($this->patient, $order, $observations);

        $practice = Practice::find($this->patient->practice_id ?? Auth::user()->practice_id);
        $result = $hl7Service->parseOruPayload($hl7Payload, $practice);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'import_lab_result',
            entityType: 'lab_result',
            entityId: $result->id,
            patientId: $this->patient->id,
        );

        if ($result->is_critical) {
            Flux::toast(variant: 'danger', text: "CRITICAL LAB VALUE RECEIVED: Abnormal values found in {$order->panel}!");
        } else {
            Flux::toast(variant: 'success', text: 'Quest Diagnostics HL7 result delivery simulated successfully.');
        }

        $this->patient->load(['labOrders', 'labResults']);
    }

    public function getPriorValue(string $panel, string $obsName, int $currentResultId): ?array
    {
        // Fetch previous results for this patient and panel
        $results = LabResult::where('patient_id', $this->patient->id)
            ->where('id', '<', $currentResultId)
            ->whereHas('labOrder', fn ($q) => $q->where('panel', $panel))
            ->latest()
            ->get();

        foreach ($results as $res) {
            foreach ($res->result_data as $obs) {
                if ($obs['name'] === $obsName) {
                    return $obs;
                }
            }
        }

        return null;
    }

    public function render(): View
    {
        return view('livewire.patients.patient-labs');
    }
}
