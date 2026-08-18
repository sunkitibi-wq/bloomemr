<?php

namespace App\Services;

use App\Models\Patient;

class DoseSpotService
{
    /**
     * Generate an SSO URL to embed DoseSpot iframe for a specific patient.
     */
    public function getPatientSsoUrl(Patient $patient): string
    {
        if (config('services.dosespot.mock', true)) {
            return 'https://sandbox.dosespot.com/sso/mock?patient_id='.$patient->id;
        }

        $clinicKey = config('services.dosespot.clinic_key', '');
        $clinicId = config('services.dosespot.clinic_id', '');
        $userId = auth()->id(); // Prescriber ID

        // Create the SSO token logic based on DoseSpot API specification
        $plainText = sprintf('clinicId=%s&userId=%s&patientId=%s', $clinicId, $userId, $patient->id);
        $encryptedToken = base64_encode(hash_hmac('sha256', $plainText, $clinicKey, true));

        $baseUrl = config('services.dosespot.base_url', 'https://my.dosespot.com');

        return sprintf(
            '%s/LoginSingleSignOn.aspx?b=2&ClinicId=%s&UserId=%s&PatientId=%s&Token=%s',
            $baseUrl,
            $clinicId,
            $userId,
            $patient->id,
            urlencode($encryptedToken)
        );
    }
}
