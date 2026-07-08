<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\RadiologyOrder;
use App\Models\RadiologyReport;
use Illuminate\Support\Facades\Auth;

class RadiologyService
{
    /**
     * Create a new radiology order for a patient.
     */
    public function createOrder(Patient $patient, string $procedure, string $indication, ?int $encounterId = null): RadiologyOrder
    {
        return RadiologyOrder::create([
            'practice_id' => $patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $patient->id,
            'encounter_id' => $encounterId,
            'ordered_by' => Auth::id() ?? User::first()->id,
            'procedure_name' => $procedure,
            'clinical_indication' => $indication,
            'status' => 'ordered',
            'order_date' => now(),
        ]);
    }

    /**
     * Upload and finalize a radiology report for an order.
     */
    public function uploadReport(RadiologyOrder $order, array $data, ?string $filePath = null): RadiologyReport
    {
        $report = RadiologyReport::create([
            'practice_id' => $order->practice_id,
            'radiology_order_id' => $order->id,
            'patient_id' => $order->patient_id,
            'radiologist_id' => Auth::id(),
            'findings' => $data['findings'],
            'impression' => $data['impression'],
            'attachment_path' => $filePath,
            'reported_at' => now(),
        ]);

        $order->update([
            'status' => 'reported',
            'completed_at' => now(),
        ]);

        return $report;
    }
}
