<?php

namespace App\Services;

use App\Models\EligibilityCheck;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class EligibilityService
{
    /**
     * Verify patient's insurance coverage eligibility via a simulated 270/271 transaction.
     *
     * @param Patient $patient
     * @return EligibilityCheck
     */
    public function checkEligibility(Patient $patient): EligibilityCheck
    {
        $insurance = $patient->primary_insurance;

        if (empty($insurance)) {
            return EligibilityCheck::create([
                'practice_id' => $patient->practice_id ?? Auth::user()->practice_id,
                'patient_id' => $patient->id,
                'status' => 'error',
                'copay_amount' => 0.00,
                'deductible_amount' => 0.00,
                'payer_name' => 'No Insurance On File',
                'checked_at' => now(),
            ]);
        }

        // Check if policy has dummy indicator for inactive
        $isInactive = str_contains(strtolower($insurance), 'inactive') || str_contains(strtolower($insurance), 'terminated');
        
        $status = $isInactive ? 'ineligible' : 'eligible';
        $copay = $isInactive ? 0.00 : 30.00;
        $deductible = $isInactive ? 0.00 : 250.00;
        
        // Extract payer name or default
        $payer = 'Blue Cross Blue Shield';
        if (preg_match('/(aetna|cigna|united|medicaid|medicare)/i', $insurance, $matches)) {
            $payer = ucfirst(strtolower($matches[1]));
        }

        return EligibilityCheck::create([
            'practice_id' => $patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $patient->id,
            'status' => $status,
            'copay_amount' => $copay,
            'deductible_amount' => $deductible,
            'payer_name' => $payer,
            'checked_at' => now(),
        ]);
    }
}
