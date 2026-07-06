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
     *
     * @param int $practiceId
     * @return void
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
    }

    /**
     * Manually close an open care gap for a patient.
     *
     * @param CareGap $gap
     * @return CareGap
     */
    public function resolveCareGap(CareGap $gap): CareGap
    {
        $gap->update(['status' => 'closed']);
        return $gap;
    }
}
