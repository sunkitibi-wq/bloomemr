<?php

namespace App\Http\Resources\Fhir;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into a FHIR R4 Patient resource.
     */
    public function toArray($request): array
    {
        $patient = $this->resource;

        return [
            'resourceType' => 'Patient',
            'id' => (string) $patient->id,
            'meta' => [
                'versionId' => '1',
                'lastUpdated' => $patient->updated_at?->toIso8601String(),
            ],
            'identifier' => [
                [
                    'system' => 'urn:oid:2.16.840.1.113883.19.5', // MRN system
                    'value' => $patient->mrn,
                ],
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => $patient->last_name,
                    'given' => [$patient->first_name],
                ],
            ],
            'telecom' => array_filter([
                $patient->phone ? ['system' => 'phone', 'value' => $patient->phone, 'use' => 'home'] : null,
                $patient->email ? ['system' => 'email', 'value' => $patient->email, 'use' => 'home'] : null,
            ]),
            'gender' => $this->mapGender($patient->gender_identity),
            'birthDate' => $patient->date_of_birth?->format('Y-m-d'),
            'address' => $patient->address ? [
                [
                    'use' => 'home',
                    'text' => $patient->address,
                ],
            ] : [],
            'maritalStatus' => $patient->emergency_contact_name ? [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-MaritalStatus',
                        'code' => 'M',
                        'display' => 'Married',
                    ],
                ],
            ] : null,
            'contact' => $patient->emergency_contact_name ? [
                [
                    'relationship' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0131',
                                    'code' => 'N',
                                    'display' => 'Next of Kin',
                                ],
                            ],
                        ],
                    ],
                    'name' => [
                        'text' => $patient->emergency_contact_name,
                    ],
                    'telecom' => $patient->emergency_contact_phone ? [
                        ['system' => 'phone', 'value' => $patient->emergency_contact_phone, 'use' => 'mobile'],
                    ] : [],
                ],
            ] : [],
            'communication' => [
                [
                    'language' => [
                        'coding' => [
                            [
                                'system' => 'urn:ietf:bcp:47',
                                'code' => $patient->preferred_language ?? 'en',
                                'display' => $patient->preferred_language ?? 'English',
                            ],
                        ],
                    ],
                    'preferred' => true,
                ],
            ],
            'generalPractitioner' => $patient->primaryProvider ? [
                [
                    'reference' => 'Practitioner/'.$patient->primaryProvider->id,
                    'display' => $patient->primaryProvider->name,
                ],
            ] : [],
            'managingOrganization' => $patient->practice ? [
                'reference' => 'Organization/'.$patient->practice->id,
                'display' => $patient->practice->name,
            ] : null,
        ];
    }

    protected function mapGender(?string $gender): ?string
    {
        return match (strtolower($gender ?? '')) {
            'male', 'm' => 'male',
            'female', 'f' => 'female',
            'non-binary', 'nonbinary', 'nb' => 'other',
            default => null,
        };
    }
}
