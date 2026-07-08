<?php

namespace App\Http\Resources\Fhir;

use Illuminate\Http\Resources\Json\JsonResource;

class ObservationResource extends JsonResource
{
    /**
     * Transform the resource into a FHIR R4 Observation resource for labs.
     */
    public function toArray($request): array
    {
        $labResult = $this->resource;
        $data = $labResult->result_data ?? [];

        $observations = [];
        if (is_array($data)) {
            foreach ($data as $index => $obs) {
                $observations[] = [
                    'resourceType' => 'Observation',
                    'id' => (string) $labResult->id.'-'.$index,
                    'meta' => [
                        'lastUpdated' => $labResult->updated_at?->toIso8601String(),
                    ],
                    'status' => 'final',
                    'category' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/observation-category',
                                    'code' => 'laboratory',
                                    'display' => 'Laboratory',
                                ],
                            ],
                        ],
                    ],
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => $obs['name'] ?? 'unknown',
                                'display' => $obs['name'] ?? 'Unknown Observation',
                            ],
                        ],
                        'text' => $obs['name'] ?? '',
                    ],
                    'subject' => [
                        'reference' => 'Patient/'.$labResult->patient_id,
                    ],
                    'performer' => $labResult->labOrder ? [
                        [
                            'reference' => 'Organization/'.($labResult->practice_id ?? 0),
                        ],
                    ] : [],
                    'valueQuantity' => [
                        'value' => (float) ($obs['value'] ?? 0),
                        'unit' => $obs['unit'] ?? '',
                    ],
                    'referenceRange' => isset($obs['range']) ? [
                        [
                            'text' => $obs['range'],
                        ],
                    ] : [],
                    'interpretation' => ! empty($obs['flag']) ? [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation',
                                    'code' => $obs['flag'],
                                ],
                            ],
                        ],
                    ] : [],
                ];
            }
        }

        return $observations;
    }
}
