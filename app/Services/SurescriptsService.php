<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Support\Facades\Http;

class SurescriptsService
{
    /**
     * Check the insurance formulary for a proposed drug.
     */
    public function checkFormulary(Patient $patient, string $drugName): array
    {
        if (config('services.surescripts.mock', true)) {
            return $this->mockCheckFormulary($patient, $drugName);
        }

        if (empty($drugName)) {
            return [
                'status' => 'Unknown',
                'tier' => 'N/A',
                'copay' => 'N/A',
                'pa_required' => false,
            ];
        }

        try {
            $response = Http::withBasicAuth(
                config('services.surescripts.key', ''),
                config('services.surescripts.secret', '')
            )
                ->timeout(10)
                ->retry(3, 100)
                ->get('https://api.surescripts.com/v1/formulary', [
                    'patient_mrn' => $patient->mrn,
                    'drug_name' => $drugName,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            \Log::error('Surescripts checkFormulary API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'patient_mrn' => $patient->mrn,
            ]);
        } catch (\Exception $e) {
            \Log::error('Surescripts checkFormulary Exception: '.$e->getMessage());
        }

        return [
            'status' => 'Unknown (API Error)',
            'tier' => 'N/A',
            'copay' => 'N/A',
            'pa_required' => false,
        ];
    }

    /**
     * Transmit a prescription to Surescripts.
     */
    public function transmitPrescription(Prescription $prescription): bool
    {
        if (config('services.surescripts.mock', true)) {
            return $this->mockTransmitPrescription($prescription);
        }

        $prescription->load(['patient', 'medication']);

        try {
            $response = Http::withBasicAuth(
                config('services.surescripts.key', ''),
                config('services.surescripts.secret', '')
            )
                ->timeout(15)
                ->retry(3, 100)
                ->post('https://api.surescripts.com/v1/prescriptions', [
                    'patient_mrn' => $prescription->patient->mrn,
                    'drug_name' => $prescription->medication->name,
                    'dose' => $prescription->medication->dose,
                    'frequency' => $prescription->medication->frequency,
                    'provider_id' => $prescription->medication->prescriber_id,
                ]);

            if ($response->successful()) {
                return true;
            }

            \Log::error('Surescripts transmitPrescription API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'prescription_id' => $prescription->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Surescripts transmitPrescription Exception: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Fetch external medication history from Surescripts pharmacy hub.
     */
    public function fetchExternalMedHistory(Patient $patient): array
    {
        if (config('services.surescripts.mock', true)) {
            return $this->mockFetchExternalMedHistory($patient);
        }

        try {
            $response = Http::withBasicAuth(
                config('services.surescripts.key', ''),
                config('services.surescripts.secret', '')
            )
                ->timeout(10)
                ->retry(3, 100)
                ->get('https://api.surescripts.com/v1/medication-history', [
                    'patient_mrn' => $patient->mrn,
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            \Log::error('Surescripts fetchExternalMedHistory API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'patient_mrn' => $patient->mrn,
            ]);
        } catch (\Exception $e) {
            \Log::error('Surescripts fetchExternalMedHistory Exception: '.$e->getMessage());
        }

        return [];
    }

    private function mockCheckFormulary(Patient $patient, string $drugName): array
    {
        if (empty($drugName)) {
            return [
                'status' => 'Unknown',
                'tier' => 'N/A',
                'copay' => 'N/A',
                'pa_required' => false,
            ];
        }

        $drugLower = strtolower(trim($drugName));

        if (str_contains($drugLower, 'lexapro') || str_contains($drugLower, 'escitalopram') || str_contains($drugLower, 'abilify')) {
            return [
                'status' => 'Preferred Generic',
                'tier' => 'Tier 1',
                'copay' => '$10.00',
                'pa_required' => false,
            ];
        }

        if (str_contains($drugLower, 'adderall') || str_contains($drugLower, 'ritalin') || str_contains($drugLower, 'sec')) {
            return [
                'status' => 'Preferred Brand',
                'tier' => 'Tier 2',
                'copay' => '$35.00',
                'pa_required' => true,
            ];
        }

        if (str_contains($drugLower, 'vyvanse') || str_contains($drugLower, 'concerta')) {
            return [
                'status' => 'Non-Preferred Brand',
                'tier' => 'Tier 3',
                'copay' => '$75.00',
                'pa_required' => true,
            ];
        }

        return [
            'status' => 'Non-Preferred Generic',
            'tier' => 'Tier 2',
            'copay' => '$30.00',
            'pa_required' => false,
        ];
    }

    private function mockTransmitPrescription(Prescription $prescription): bool
    {
        // Simulates connection to Surescripts gateway and returns success
        return true;
    }

    private function mockFetchExternalMedHistory(Patient $patient): array
    {
        return [
            ['name' => 'Abilify', 'dose' => '5mg', 'frequency' => 'Daily at bedtime', 'ndc_code' => '59148-008-13', 'source' => 'Surescripts Hub History'],
            ['name' => 'Ibuprofen', 'dose' => '400mg', 'frequency' => 'As needed for pain', 'ndc_code' => '00406-0397-01', 'source' => 'Surescripts Hub History'],
        ];
    }
}
