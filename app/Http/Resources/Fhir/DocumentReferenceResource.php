<?php

namespace App\Http\Resources\Fhir;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentReferenceResource extends JsonResource
{
    /**
     * Transform the resource into a FHIR R4 DocumentReference resource.
     */
    public function toArray($request): array
    {
        $document = $this->resource;

        return [
            'resourceType' => 'DocumentReference',
            'id' => (string) $document->id,
            'meta' => [
                'lastUpdated' => $document->updated_at?->toIso8601String(),
            ],
            'status' => 'current',
            'docStatus' => $document->status ?? 'final',
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => $this->mapLoincForCategory($document->category),
                        'display' => ucfirst(str_replace('_', ' ', $document->category ?? '')),
                    ],
                ],
            ],
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://hl7.org/fhir/ValueSet/document-classcodes',
                            'code' => 'clinical-note',
                            'display' => 'Clinical Note',
                        ],
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/'.$document->patient_id,
            ],
            'date' => $document->created_at?->toIso8601String(),
            'author' => $document->uploadedBy ? [
                [
                    'reference' => 'Practitioner/'.$document->uploaded_by,
                    'display' => $document->uploadedBy->name ?? 'Unknown',
                ],
            ] : [],
            'description' => $document->original_name ?? '',
            'content' => [
                [
                    'attachment' => [
                        'contentType' => $document->mime_type ?? 'application/octet-stream',
                        'title' => $document->original_name ?? 'Document',
                        'size' => $document->size,
                    ],
                    'format' => [
                        'system' => 'urn:oid:1.3.6.1.4.1.19376.1.2.3',
                        'code' => 'urn:ihe:iti:xds:2017:mimeTypeSufficient',
                        'display' => 'MIME Type sufficient',
                    ],
                ],
            ],
            'context' => [
                'period' => [
                    'start' => $document->created_at?->toIso8601String(),
                ],
                'related' => [
                    [
                        'reference' => 'Patient/'.$document->patient_id,
                    ],
                ],
            ],
        ];
    }

    protected function mapLoincForCategory(?string $category): string
    {
        return match ($category) {
            'iep' => '48765-2',
            'school_report' => '48765-2',
            'outside_records' => '34105-7',
            'behavioral_plan' => '47420-5',
            'prior_auth' => '73664-9',
            default => '34105-7',
        };
    }
}
