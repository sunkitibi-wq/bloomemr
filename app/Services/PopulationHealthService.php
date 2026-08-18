<?php

namespace App\Services;

use App\Models\CareGap;
use App\Models\Patient;
use App\Models\PatientCohort;
use App\Models\PatientCohortMember;

class PopulationHealthService
{
    /**
     * Re-evaluate and populate registries and cohorts based on clinical criteria.
     */
    public function refreshCohorts(int $practiceId): void
    {
        // 1. ADHD Cohort
        $adhdCohort = PatientCohort::firstOrCreate([
            'practice_id' => $practiceId,
            'name' => 'ADHD Cohort Registry',
        ], [
            'description' => 'Patients diagnosed with ADHD or undergoing active stimulant/non-stimulant titration guides.',
        ]);

        // Find patients with ADHD clinical notes or titration guide indicators
        $adhdPatients = Patient::where('practice_id', $practiceId)
            ->where(function ($q) {
                $q->whereHas('encounters.clinicalNotes', function ($sq) {
                    $sq->where('body', 'like', '%ADHD%')
                        ->orWhere('body', 'like', '%stimulant%')
                        ->orWhere('body', 'like', '%Buspar%');
                })->orWhere('problem_list', 'like', '%ADHD%');
            })->get();

        foreach ($adhdPatients as $patient) {
            PatientCohortMember::firstOrCreate([
                'cohort_id' => $adhdCohort->id,
                'patient_id' => $patient->id,
            ], [
                'joined_at' => now(),
            ]);

            // Generate ADHD-specific care gap
            CareGap::firstOrCreate([
                'practice_id' => $practiceId,
                'patient_id' => $patient->id,
                'gap_type' => 'medication_review',
            ], [
                'description' => 'Semi-annual ADHD medication and titration plan review.',
                'status' => 'open',
                'due_date' => now()->addMonths(6),
            ]);
        }

        // 2. Pediatric Wellness Cohort
        $pedsCohort = PatientCohort::firstOrCreate([
            'practice_id' => $practiceId,
            'name' => 'Pediatric Vaccines & Wellness',
        ], [
            'description' => 'Patients under the age of 18 requiring scheduled immunization records and developmental screening checks.',
        ]);

        $pediatricPatients = Patient::where('practice_id', $practiceId)
            ->whereDate('date_of_birth', '>=', now()->subYears(18))
            ->get();

        foreach ($pediatricPatients as $patient) {
            PatientCohortMember::firstOrCreate([
                'cohort_id' => $pedsCohort->id,
                'patient_id' => $patient->id,
            ], [
                'joined_at' => now(),
            ]);

            // Generate pediatric immunization care gap
            CareGap::firstOrCreate([
                'practice_id' => $practiceId,
                'patient_id' => $patient->id,
                'gap_type' => 'vaccine_due',
            ], [
                'description' => 'Childhood immunization wellness check and panel updates.',
                'status' => 'open',
                'due_date' => now()->addMonths(3),
            ]);
        }

        // 3. Diabetes Care Gap
        $diabeticPatients = Patient::where('practice_id', $practiceId)
            ->where('problem_list', 'like', '%diabetes%')
            ->get();
        foreach ($diabeticPatients as $patient) {
            CareGap::firstOrCreate([
                'practice_id' => $practiceId,
                'patient_id' => $patient->id,
                'gap_type' => 'hba1c_check',
            ], [
                'description' => 'Bi-annual HbA1c check for diabetic patients.',
                'status' => 'open',
                'due_date' => now()->addMonths(6),
            ]);
        }

        // 4. Hypertension Care Gap
        $hypertensionPatients = Patient::where('practice_id', $practiceId)
            ->where('problem_list', 'like', '%hypertension%')
            ->get();
        foreach ($hypertensionPatients as $patient) {
            CareGap::firstOrCreate([
                'practice_id' => $practiceId,
                'patient_id' => $patient->id,
                'gap_type' => 'blood_pressure_check',
            ], [
                'description' => 'Regular blood pressure monitoring for hypertension.',
                'status' => 'open',
                'due_date' => now()->addMonths(3),
            ]);
        }

        // 5. Cancer Screening Care Gap (Age > 45)
        $cancerScreeningPatients = Patient::where('practice_id', $practiceId)
            ->whereDate('date_of_birth', '<=', now()->subYears(45))
            ->get();
        foreach ($cancerScreeningPatients as $patient) {
            CareGap::firstOrCreate([
                'practice_id' => $practiceId,
                'patient_id' => $patient->id,
                'gap_type' => 'colorectal_screening',
            ], [
                'description' => 'Routine colorectal cancer screening for adults 45 and older.',
                'status' => 'open',
                'due_date' => now()->addMonths(12),
            ]);
        }

        // 6. Process Custom Cohorts
        $customCohorts = PatientCohort::where('practice_id', $practiceId)
            ->whereNotNull('criteria')
            ->get();

        foreach ($customCohorts as $cohort) {
            $query = Patient::where('practice_id', $practiceId);

            $criteria = $cohort->criteria;

            if (isset($criteria['min_age']) && $criteria['min_age']) {
                $query->whereDate('date_of_birth', '<=', now()->subYears($criteria['min_age']));
            }
            if (isset($criteria['max_age']) && $criteria['max_age']) {
                $query->whereDate('date_of_birth', '>=', now()->subYears($criteria['max_age']));
            }
            if (isset($criteria['diagnosis_keyword']) && $criteria['diagnosis_keyword']) {
                $query->where('problem_list', 'like', '%'.$criteria['diagnosis_keyword'].'%');
            }

            $matchedPatients = $query->get();

            // Re-sync cohort members
            $matchedIds = $matchedPatients->pluck('id')->toArray();

            // Delete old members not matching anymore
            PatientCohortMember::where('cohort_id', $cohort->id)
                ->whereNotIn('patient_id', $matchedIds)
                ->delete();

            foreach ($matchedPatients as $patient) {
                PatientCohortMember::firstOrCreate([
                    'cohort_id' => $cohort->id,
                    'patient_id' => $patient->id,
                ], [
                    'joined_at' => now(),
                ]);
            }
        }
    }

    /**
     * Manually close an open care gap for a patient.
     */
    public function resolveCareGap(CareGap $gap): CareGap
    {
        $gap->update(['status' => 'closed']);

        return $gap;
    }
}
