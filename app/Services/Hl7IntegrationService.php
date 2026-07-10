<?php

namespace App\Services;

use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Practice;
use Illuminate\Support\Facades\Http;

class Hl7IntegrationService
{
    /**
     * Fetch Lab Results from Health Gorilla API or Mock based on config.
     */
    public function fetchLabResults(Patient $patient, LabOrder $order, array $observations = []): string
    {
        if (config('services.health_gorilla.mock', true)) {
            return $this->generateMockQuestHl7($patient, $order, $observations);
        }

        $response = Http::withToken(config('services.health_gorilla.token', ''))
            ->get('https://api.healthgorilla.com/fhir/R4/DiagnosticReport', [
                'patient' => $patient->id,
                'based-on' => $order->id,
            ]);

        if ($response->successful()) {
            return $response->body();
        }

        return '';
    }

    /**
     * Generates a mock Quest Diagnostics HL7 ORU^R01 message string.
     */
    public function generateMockQuestHl7(Patient $patient, LabOrder $order, array $observations): string
    {
        $timestamp = now()->format('YmdHis');
        $msh = "MSH|^~\\&|QUEST|LAB|||{$timestamp}||ORU^R01|MSG".rand(10000, 99999).'|P|2.3';
        $pid = "PID|1||{$patient->mrn}||{$patient->last_name}^{$patient->first_name}|||||||||||||||||||||||";
        $obr = "OBR|1||{$order->id}||{$order->panel}||||||||||||||||||||F";

        $segments = [$msh, $pid, $obr];
        $index = 1;

        foreach ($observations as $obs) {
            // $obs: ['name' => 'TSH', 'value' => 5.4, 'range' => '0.4-4.0', 'flag' => 'H', 'unit' => 'mIU/L']
            $flag = $obs['flag'] ?? '';
            $unit = $obs['unit'] ?? '';
            $segments[] = "OBX|{$index}|NM|{$obs['name']}||{$obs['value']}|{$unit}|{$obs['range']}|{$flag}|||F";
            $index++;
        }

        return implode("\n", $segments);
    }

    /**
     * Parses an incoming HL7 ORU^R01 text payload and saves results to the database.
     */
    public function parseOruPayload(string $hl7Payload, Practice $practice): ?LabResult
    {
        // Check if FHIR payload
        if (str_starts_with(trim($hl7Payload), '{')) {
            return $this->parseFhirPayload($hl7Payload, $practice);
        }

        $lines = explode("\n", trim($hl7Payload));
        $patient = null;
        $labOrder = null;
        $observations = [];
        $isCritical = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $fields = explode('|', $line);
            $segmentType = $fields[0];

            if ($segmentType === 'PID') {
                $mrn = $fields[3] ?? null;
                if ($mrn) {
                    $patient = Patient::where('mrn', $mrn)->first();
                }
            } elseif ($segmentType === 'OBR') {
                $orderId = $fields[3] ?? null;
                if ($orderId) {
                    $labOrder = LabOrder::find($orderId);
                }
            } elseif ($segmentType === 'OBX') {
                $name = $fields[3] ?? 'Observation';
                $value = $fields[5] ?? '';
                $unit = $fields[6] ?? '';
                $range = $fields[7] ?? '';
                $flag = $fields[8] ?? '';

                if (in_array($flag, ['H', 'L', 'HH', 'LL'])) {
                    if (in_array($flag, ['HH', 'LL']) || (float) $value > 15.0 || (float) $value < 2.0) {
                        $isCritical = true;
                    }
                }

                $observations[] = [
                    'name' => $name,
                    'value' => $value,
                    'unit' => $unit,
                    'range' => $range,
                    'flag' => $flag,
                ];
            }
        }

        if (! $patient || ! $labOrder) {
            return null;
        }

        // Create LabResult record
        $result = LabResult::create([
            'practice_id' => $practice->id,
            'patient_id' => $patient->id,
            'lab_order_id' => $labOrder->id,
            'result_data' => $observations,
            'is_critical' => $isCritical,
        ]);

        // Complete the lab order
        $labOrder->update(['status' => 'completed']);

        return $result;
    }

    private function parseFhirPayload(string $fhirPayload, Practice $practice): ?LabResult
    {
        $data = json_decode($fhirPayload, true);
        if (! $data || ($data['resourceType'] ?? '') !== 'DiagnosticReport') {
            return null;
        }

        $patientId = str_replace('Patient/', '', $data['subject']['reference'] ?? '');
        $orderId = str_replace('ServiceRequest/', '', $data['basedOn'][0]['reference'] ?? '');

        $patient = Patient::find($patientId);
        $labOrder = LabOrder::find($orderId);

        if (! $patient || ! $labOrder) {
            return null;
        }

        $observations = [];
        $isCritical = false;

        foreach ($data['result'] ?? [] as $res) {
            $obs = [
                'name' => $res['display'] ?? 'Observation',
                'value' => $res['valueQuantity']['value'] ?? '',
                'unit' => $res['valueQuantity']['unit'] ?? '',
                'range' => $res['referenceRange'][0]['text'] ?? '',
                'flag' => '',
            ];
            $observations[] = $obs;
        }

        $result = LabResult::create([
            'practice_id' => $practice->id,
            'patient_id' => $patient->id,
            'lab_order_id' => $labOrder->id,
            'result_data' => $observations,
            'is_critical' => $isCritical,
        ]);

        $labOrder->update(['status' => 'completed']);

        return $result;
    }
}
