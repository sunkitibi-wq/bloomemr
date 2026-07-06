<?php

namespace App\Services;

use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Clinician encounter utilisation for the given month/year.
     *
     * @param int $practiceId
     * @param int $month
     * @param int $year
     * @return Collection
     */
    public function clinicianUtilization(int $practiceId, int $month, int $year): Collection
    {
        return User::where('practice_id', $practiceId)
            ->whereIn('role', ['physician', 'nurse_practitioner', 'attending', 'resident', 'nurse'])
            ->withCount([
                'encounters as encounter_count' => function ($q) use ($month, $year) {
                    $q->whereMonth('encounter_date', $month)
                      ->whereYear('encounter_date', $year);
                },
            ])
            ->orderByDesc('encounter_count')
            ->get(['id', 'name', 'role', 'encounter_count']);
    }

    /**
     * Patient demographic breakdown for the practice.
     *
     * @param int $practiceId
     * @return array<string, mixed>
     */
    public function demographicBreakdown(int $practiceId): array
    {
        $patients = Patient::where('practice_id', $practiceId)->get();

        $byGender = $patients->groupBy('gender_identity')
            ->map->count()
            ->toArray();

        $byEthnicity = $patients->groupBy('race_ethnicity')
            ->map->count()
            ->toArray();

        $ageGroups = [
            '0–17' => 0,
            '18–34' => 0,
            '35–49' => 0,
            '50–64' => 0,
            '65+' => 0,
        ];

        foreach ($patients as $patient) {
            $age = Carbon::parse($patient->date_of_birth)->age;
            if ($age <= 17) {
                $ageGroups['0–17']++;
            } elseif ($age <= 34) {
                $ageGroups['18–34']++;
            } elseif ($age <= 49) {
                $ageGroups['35–49']++;
            } elseif ($age <= 64) {
                $ageGroups['50–64']++;
            } else {
                $ageGroups['65+']++;
            }
        }

        return [
            'total' => $patients->count(),
            'by_gender' => $byGender,
            'by_ethnicity' => $byEthnicity,
            'age_groups' => $ageGroups,
        ];
    }

    /**
     * Monthly encounter volume for the last 6 months.
     *
     * @param int $practiceId
     * @return Collection
     */
    public function monthlyEncounterTrend(int $practiceId): Collection
    {
        $rows = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Encounter::where('practice_id', $practiceId)
                ->whereMonth('encounter_date', $date->month)
                ->whereYear('encounter_date', $date->year)
                ->count();

            $rows->push([
                'label' => $date->format('M Y'),
                'count' => $count,
            ]);
        }

        return $rows;
    }

    /**
     * Revenue summary for the practice (monthly and overall).
     *
     * @param int $practiceId
     * @return array<string, mixed>
     */
    public function revenueSummary(int $practiceId): array
    {
        $monthRevenue = Invoice::where('practice_id', $practiceId)
            ->where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        $yearRevenue = Invoice::where('practice_id', $practiceId)
            ->where('status', 'paid')
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        $outstanding = Invoice::where('practice_id', $practiceId)
            ->whereIn('status', ['draft', 'pending'])
            ->sum('total_amount');

        $unpaidCount = Invoice::where('practice_id', $practiceId)
            ->whereIn('status', ['draft', 'pending'])
            ->count();

        return [
            'month_revenue' => $monthRevenue,
            'year_revenue' => $yearRevenue,
            'outstanding_balance' => $outstanding,
            'unpaid_count' => $unpaidCount,
        ];
    }
}
