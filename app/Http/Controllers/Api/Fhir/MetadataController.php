<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MetadataController extends Controller
{
    /**
     * Return the FHIR CapabilityStatement for this server.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'resourceType' => 'CapabilityStatement',
            'id' => 'bloom-ehr-capability',
            'version' => '1.0.0',
            'name' => 'BloomEHRCapabilityStatement',
            'title' => 'Bloom EHR FHIR R4 Capability Statement',
            'status' => 'active',
            'date' => now()->format('Y-m-d'),
            'publisher' => 'Bloom Behavioral Health',
            'description' => 'FHIR R4 API for Bloom child/adolescent psychiatry EHR',
            'kind' => 'instance',
            'software' => [
                'name' => 'Bloom EHR',
                'version' => '1.0.0',
                'releaseDate' => '2026-01-01',
            ],
            'fhirVersion' => '4.0.1',
            'format' => ['application/fhir+json', 'application/json'],
            'rest' => [
                [
                    'mode' => 'server',
                    'documentation' => 'Bloom EHR FHIR RESTful API',
                    'security' => [
                        'cors' => true,
                        'service' => [
                            [
                                'coding' => [
                                    [
                                        'system' => 'http://terminology.hl7.org/CodeSystem/restful-security-service',
                                        'code' => 'OAuth',
                                        'display' => 'OAuth2',
                                    ],
                                ],
                                'text' => 'OAuth2 using Laravel Passport',
                            ],
                        ],
                        'description' => 'OAuth2 bearer token required. See SMART on FHIR configuration at /.well-known/smart-configuration',
                    ],
                    'resource' => [
                        [
                            'type' => 'Patient',
                            'interaction' => [
                                ['code' => 'search-type'],
                                ['code' => 'read'],
                            ],
                            'searchParam' => [
                                ['name' => 'identifier', 'type' => 'token', 'documentation' => 'MRN assigned to the patient'],
                                ['name' => 'family', 'type' => 'string', 'documentation' => 'Last name'],
                                ['name' => 'given', 'type' => 'string', 'documentation' => 'First name'],
                                ['name' => 'birthdate', 'type' => 'date', 'documentation' => 'Date of birth'],
                                ['name' => 'phone', 'type' => 'token', 'documentation' => 'Phone number'],
                                ['name' => 'email', 'type' => 'token', 'documentation' => 'Email address'],
                            ],
                        ],
                        [
                            'type' => 'Encounter',
                            'interaction' => [
                                ['code' => 'search-type'],
                                ['code' => 'read'],
                            ],
                            'searchParam' => [
                                ['name' => 'patient', 'type' => 'reference', 'documentation' => 'Patient reference'],
                                ['name' => 'date', 'type' => 'date', 'documentation' => 'Encounter date'],
                                ['name' => 'status', 'type' => 'token', 'documentation' => 'Encounter status'],
                            ],
                        ],
                        [
                            'type' => 'Observation',
                            'interaction' => [
                                ['code' => 'search-type'],
                            ],
                            'searchParam' => [
                                ['name' => 'patient', 'type' => 'reference', 'documentation' => 'Patient reference'],
                                ['name' => 'date', 'type' => 'date', 'documentation' => 'Result date'],
                                ['name' => 'code', 'type' => 'token', 'documentation' => 'LOINC code'],
                            ],
                        ],
                        [
                            'type' => 'MedicationRequest',
                            'interaction' => [
                                ['code' => 'search-type'],
                                ['code' => 'read'],
                            ],
                            'searchParam' => [
                                ['name' => 'patient', 'type' => 'reference', 'documentation' => 'Patient reference'],
                                ['name' => 'status', 'type' => 'token', 'documentation' => 'Medication status'],
                            ],
                        ],
                        [
                            'type' => 'DocumentReference',
                            'interaction' => [
                                ['code' => 'search-type'],
                                ['code' => 'read'],
                            ],
                            'searchParam' => [
                                ['name' => 'patient', 'type' => 'reference', 'documentation' => 'Patient reference'],
                                ['name' => 'type', 'type' => 'token', 'documentation' => 'Document LOINC type'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
