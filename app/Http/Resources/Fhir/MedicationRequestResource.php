<?php

namespace App\Http\Resources\Fhir;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicationRequestResource extends JsonResource
{
    /**
     * Transform the resource into a FHIR R4 MedicationRequest resource.
     */
    public function toArray($request): array
    {
        $prescription = $this->resource;
        $isControlled = $prescription->is_controlled ?? false;

        return [
            'resourceType' => 'MedicationRequest',
            'id' => (string) $prescription->id,
            'meta' => [
                'lastUpdated' => $prescription->updated_at?->toIso8601String(),
            ],
            'status' => $this->mapStatus($prescription->status),
            'intent' => 'order',
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/medicationrequest-category',
                            'code' => 'outpatient',
                            'display' => 'Outpatient',
                        ],
                    ],
                ],
            ],
            'priority' => $isControlled ? 'urgent' : 'routine',
            'medicationReference' => $prescription->medication ? [
                'reference' => 'Medication/'.$prescription->medication_id,
                'display' => $prescription->medication->name ?? '',
            ] : null,
            'subject' => [
                'reference' => 'Patient/'.$prescription->patient_id,
            ],
            'authoredOn' => $prescription->created_at?->toIso8601String(),
            'requester' => [
                'reference' => 'Practitioner/'.($prescription->medication?->prescriber_id ?? 0),
            ],
            'dosageInstruction' => $prescription->medication ? [
                [
                    'text' => $prescription->medication->dose.' '.($prescription->medication->frequency ?? ''),
                    'timing' => [
                        'code' => [
                            'text' => $prescription->medication->frequency ?? '',
                        ],
                    ],
                    'doseAndRate' => [
                        [
                            'doseQuantity' => [
                                'value' => (float) filter_var($prescription->medication->dose, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                                'unit' => preg_replace('/[0-9.]/', '', $prescription->medication->dose ?? ''),
                            ],
                        ],
                    ],
                ],
            ] : [],
            'dispenseRequest' => [
                'quantity' => [
                    'value' => 30,
                    'unit' => 'tablet',
                ],
                'expectedSupplyDuration' => [
                    'value' => 30,
                    'unit' => 'days',
                ],
            ],
            'substitution' => [
                'allowedBoolean' => true,
            ],
        ];
    }

    protected function mapStatus(?string $status): string
    {
        return match ($status) {
            'sent' => 'active',
            'received' => 'active',
            'dispensed' => 'completed',
            'cancelled' => 'stopped',
            'draft' => 'draft',
            default => 'draft',
        };
    }
}
