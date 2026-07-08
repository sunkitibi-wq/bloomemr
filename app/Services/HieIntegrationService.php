<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HieIntegrationService
{
    private string $endpoint;

    private string $apiKey;

    private string $organizationId;

    public function __construct()
    {
        $this->endpoint = config('services.hie.endpoint', 'https://hie.example.com/fhir');
        $this->apiKey = config('services.hie.api_key', '');
        $this->organizationId = config('services.hie.org_id', '');
    }

    /**
     * Submit a CCDA document to the HIE for a given patient.
     */
    public function submitCcda(Patient $patient, array $ccdaData): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/fhir+json',
            'Organization-Id' => $this->organizationId,
        ])->timeout(60)->post("{$this->endpoint}/DocumentReference", [
            'resourceType' => 'DocumentReference',
            'status' => 'current',
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => '57133-1',
                        'display' => 'Referral note',
                    ],
                ],
            ],
            'subject' => [
                'reference' => "Patient/{$patient->id}",
            ],
            'content' => [
                [
                    'attachment' => [
                        'contentType' => 'application/xml',
                        'data' => base64_encode(json_encode($ccdaData)),
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('HIE CCDA submission failed', [
                'patient_id' => $patient->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        }

        return ['success' => true, 'reference' => $response->json('id')];
    }

    /**
     * Query the HIE for patient records.
     */
    public function queryPatientRecord(Patient $patient): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Accept' => 'application/fhir+json',
            'Organization-Id' => $this->organizationId,
        ])->timeout(30)->get("{$this->endpoint}/Patient", [
            'identifier' => $patient->mrn,
        ]);

        if ($response->failed()) {
            Log::error('HIE query failed', [
                'patient_id' => $patient->id,
                'status' => $response->status(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        }

        return ['success' => true, 'bundle' => $response->json()];
    }
}
