<?php

namespace App\Jobs;

use App\Events\LabResultReceived;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Practice;
use App\Services\Hl7IntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PollLabResults implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all orders that are currently in 'ordered' status
        $pendingOrders = LabOrder::where('status', 'ordered')->get();

        if ($pendingOrders->isEmpty()) {
            return;
        }

        $hl7Service = app(Hl7IntegrationService::class);

        foreach ($pendingOrders as $order) {
            $patient = $order->patient;
            if (! $patient instanceof Patient) {
                continue;
            }

            // Generate mock observations depending on the panel type
            $observations = [];
            if ($order->panel === 'Thyroid Panel') {
                $observations = [
                    ['name' => 'TSH', 'value' => '6.20', 'range' => '0.40-4.00', 'flag' => 'H', 'unit' => 'uIU/mL'],
                    ['name' => 'Free T4', 'value' => '1.10', 'range' => '0.80-1.80', 'flag' => 'N', 'unit' => 'ng/dL'],
                ];
            } elseif ($order->panel === 'CBC') {
                $observations = [
                    ['name' => 'WBC', 'value' => '1.80', 'range' => '4.00-11.00', 'flag' => 'LL', 'unit' => 'x10E3/uL'],
                    ['name' => 'RBC', 'value' => '4.50', 'range' => '4.00-5.20', 'flag' => 'N', 'unit' => 'x10E6/uL'],
                ];
            } else {
                $observations = [
                    ['name' => 'Lithium Level', 'value' => '1.40', 'range' => '0.60-1.20', 'flag' => 'H', 'unit' => 'mmol/L'],
                ];
            }

            // Generate mock payload
            $payload = $hl7Service->generateMockQuestHl7($patient, $order, $observations);

            // Parse payload and create LabResult
            $practice = Practice::find($order->practice_id ?? $patient->practice_id);
            if (! $practice instanceof Practice) {
                continue;
            }

            $result = $hl7Service->parseOruPayload($payload, $practice);

            if ($result) {
                // Dispatch the event
                event(new LabResultReceived($result));
            }
        }
    }
}
