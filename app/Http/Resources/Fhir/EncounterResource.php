<?php

namespace App\Http\Resources\Fhir;

use Illuminate\Http\Resources\Json\JsonResource;

class EncounterResource extends JsonResource
{
    /**
     * Transform the resource into a FHIR R4 Encounter resource.
     */
    public function toArray($request): array
    {
        $encounter = $this->resource;

        return [
            'resourceType' => 'Encounter',
            'id' => (string) $encounter->id,
            'meta' => [
                'versionId' => '1',
                'lastUpdated' => $encounter->updated_at?->toIso8601String(),
            ],
            'identifier' => [
                [
                    'system' => 'urn:oid:2.16.840.1.113883.19.5',
                    'value' => "ENC-{$encounter->id}",
                ],
            ],
            'status' => $this->mapStatus($encounter->status),
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'Ambulatory',
            ],
            'type' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/encounter-type',
                            'code' => $encounter->type,
                            'display' => ucfirst($encounter->type),
                        ],
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/'.$encounter->patient_id,
                'display' => $encounter->patient?->full_name ?? 'Unknown',
            ],
            'participant' => $encounter->provider ? [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'PPRF',
                                    'display' => 'Primary Performer',
                                ],
                            ],
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/'.$encounter->provider_id,
                        'display' => $encounter->provider->name ?? 'Unknown Provider',
                    ],
                ],
            ] : [],
            'period' => [
                'start' => $encounter->encounter_date?->toIso8601String(),
                'end' => $encounter->signed_at?->toIso8601String(),
            ],
            'reasonCode' => $encounter->chief_complaint ? [
                [
                    'text' => $encounter->chief_complaint,
                ],
            ] : [],
            'diagnosis' => $encounter->assessment ? [
                [
                    'condition' => [
                        'display' => $encounter->assessment,
                    ],
                    'use' => [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/diagnosis-role',
                                'code' => 'DD',
                                'display' => 'Discharge diagnosis',
                            ],
                        ],
                    ],
                ],
            ] : [],
            'serviceProvider' => $encounter->practice ? [
                'reference' => 'Organization/'.$encounter->practice->id,
                'display' => $encounter->practice->name,
            ] : null,
        ];
    }

    protected function mapStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'planned',
            'co_sign_pending' => 'in-progress',
            'signed' => 'finished',
            'amended' => 'finished',
            default => 'planned',
        };
    }
}
