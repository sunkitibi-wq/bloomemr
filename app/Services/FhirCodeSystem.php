<?php

namespace App\Services;

class FhirCodeSystem
{
    const LOINC = 'http://loinc.org';

    const SNOMED = 'http://snomed.info/sct';

    const ICD_10 = 'http://hl7.org/fhir/sid/icd-10-cm';

    const RXNORM = 'http://www.nlm.nih.gov/research/umls/rxnorm';

    const NDC = 'http://hl7.org/fhir/sid/ndc';

    const CPT = 'http://www.ama-assn.org/go/cpt';

    public static function administrativeGender(): array
    {
        return ['male', 'female', 'other', 'unknown'];
    }

    public static function encounterClass(string $type): array
    {
        return match ($type) {
            'office_visit', 'follow_up', 'initial_eval' => ['code' => 'AMB', 'display' => 'ambulatory'],
            'telehealth' => ['code' => 'TELE', 'display' => 'telehealth'],
            'school' => ['code' => 'SCHOOL', 'display' => 'school'],
            'crisis' => ['code' => 'EMER', 'display' => 'emergency'],
            default => ['code' => 'OTHER', 'display' => 'other'],
        };
    }

    public static function loincForDocumentCategory(string $category): string
    {
        return match ($category) {
            'iep' => '48765-2',
            'school_report' => '48765-2',
            'outside_records' => '34105-7',
            'behavioral_plan' => '47420-5',
            'prior_auth' => '73664-9',
            'consent' => '57017-6',
            default => '34105-7',
        };
    }

    public static function loincForAssessment(string $assessmentType): string
    {
        return match ($assessmentType) {
            'phq9' => '44249-1',
            'gad7' => '69727-6',
            'scared' => '62730-3',
            'cage' => '71931-7',
            'columbia_suicide' => '75379-7',
            default => 'unknown',
        };
    }

    public static function observationInterpretation(string $flag): string
    {
        return match ($flag) {
            'H' => 'H',
            'L' => 'L',
            'HH' => 'HH',
            'LL' => 'LL',
            'N' => 'N',
            default => 'N',
        };
    }

    public static function medicationRequestStatus(?string $status): string
    {
        return match ($status) {
            'sent' => 'active',
            'received' => 'active',
            'dispensed' => 'completed',
            'cancelled' => 'stopped',
            'draft' => 'draft',
            default => 'draft',
        };
    }
}
