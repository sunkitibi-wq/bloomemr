<?php

namespace App\Services;

use App\Models\Medication;
use App\Models\Patient;

class ClinicalDecisionSupportService
{
    /**
     * Checks if the patient is allergic to the proposed drug.
     */
    public function checkAllergies(Patient $patient, string $drugName): ?string
    {
        if (empty($patient->allergies) || empty($drugName)) {
            return null;
        }

        $drugLower = strtolower(trim($drugName));

        // Split allergies list by commas or semicolons
        $allergies = array_map('trim', preg_split('/[,;]/', strtolower($patient->allergies)));

        foreach ($allergies as $allergy) {
            if (! empty($allergy) && (str_contains($drugLower, $allergy) || str_contains($allergy, $drugLower))) {
                return "Allergy Alert: Patient is allergic to '{$allergy}'. Proposed drug: '{$drugName}'.";
            }
        }

        return null;
    }

    /**
     * Checks for drug-drug interactions with active medications.
     */
    public function checkDrugInteractions(Patient $patient, string $drugName): array
    {
        if (empty($drugName)) {
            return [];
        }

        $activeMeds = Medication::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->get();

        if ($activeMeds->isEmpty()) {
            return [];
        }

        $alerts = [];
        $proposedLower = strtolower(trim($drugName));

        // Group drug definitions
        $ssris = ['lexapro', 'prozac', 'zoloft', 'escitalopram', 'sertraline', 'fluoxetine', 'celexa', 'citalopram', 'paxil', 'paroxetine'];
        $maois = ['phenelzine', 'selegiline', 'nardil', 'parnate', 'tranylcypromine'];
        $stimulants = ['adderall', 'ritalin', 'methylphenidate', 'vyvanse', 'concerta', 'amphetamine', 'dexmethylphenidate', 'focalin'];
        $nsaids = ['ibuprofen', 'advil', 'aspirin', 'naproxen', 'aleve', 'meloxicam'];

        $isProposedSsri = $this->inGroup($proposedLower, $ssris);
        $isProposedMaoi = $this->inGroup($proposedLower, $maois);
        $isProposedStimulant = $this->inGroup($proposedLower, $stimulants);
        $isProposedNsaid = $this->inGroup($proposedLower, $nsaids);

        foreach ($activeMeds as $med) {
            $medLower = strtolower(trim($med->name));

            $isMedSsri = $this->inGroup($medLower, $ssris);
            $isMedMaoi = $this->inGroup($medLower, $maois);
            $isMedStimulant = $this->inGroup($medLower, $stimulants);
            $isMedNsaid = $this->inGroup($medLower, $nsaids);

            // 1. SSRI + MAOI (Severe)
            if (($isProposedSsri && $isMedMaoi) || ($isProposedMaoi && $isMedSsri)) {
                $alerts[] = [
                    'severity' => 'danger',
                    'message' => "Contraindicated: Combination of SSRI ({$drugName}) and MAOI ({$med->name}) carries severe risk of Serotonin Syndrome.",
                ];
            }

            // 2. Stimulant + MAOI (Severe)
            if (($isProposedStimulant && $isMedMaoi) || ($isProposedMaoi && $isMedStimulant)) {
                $alerts[] = [
                    'severity' => 'danger',
                    'message' => "Contraindicated: Combination of Stimulant ({$drugName}) and MAOI ({$med->name}) carries risk of Hypertensive Crisis.",
                ];
            }

            // 3. SSRI + NSAID (Moderate)
            if (($isProposedSsri && $isMedNsaid) || ($isProposedNsaid && $isMedSsri)) {
                $alerts[] = [
                    'severity' => 'warning',
                    'message' => "Moderate Interaction: SSRI ({$drugName}) and NSAID ({$med->name}) combination increases risk of gastrointestinal bleeding.",
                ];
            }

            // 4. Duplicate Therapy (Warning)
            if ($isProposedSsri && $isMedSsri) {
                $alerts[] = [
                    'severity' => 'warning',
                    'message' => "Therapeutic Duplicate: Patient is already taking another SSRI ({$med->name}).",
                ];
            }

            if ($isProposedStimulant && $isMedStimulant) {
                $alerts[] = [
                    'severity' => 'warning',
                    'message' => "Therapeutic Duplicate: Patient is already taking another Stimulant ({$med->name}).",
                ];
            }
        }

        return $alerts;
    }

    private function inGroup(string $drug, array $group): bool
    {
        foreach ($group as $item) {
            if (str_contains($drug, $item)) {
                return true;
            }
        }

        return false;
    }
}
