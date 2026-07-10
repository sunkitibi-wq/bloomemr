<?php

namespace App\Livewire\Reporting;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clinical Quality Measures (CQM)')]
class ClinicalQualityMeasures extends Component
{
    public $reportingPeriod = '2026';

    // Simple simulated measures
    public function getMeasuresProperty()
    {
        $practiceId = Auth::user()->practice_id;

        $totalPatients = Patient::where('practice_id', $practiceId)->count();
        if ($totalPatients === 0) {
            return [];
        }

        // Measure 1: Depression Screening
        $patientsWithScreening = Patient::where('practice_id', $practiceId)
            ->whereHas('assessments', function ($q) {
                $q->where('instrument', 'PHQ-9');
            })->count();

        // Measure 2: Medication Documented
        $patientsWithMeds = Patient::where('practice_id', $practiceId)
            ->whereHas('medications')->count();

        // Measure 3: Encounters closed within 48h
        $totalEncounters = Encounter::where('practice_id', $practiceId)->count();
        $closedEncounters = Encounter::where('practice_id', $practiceId)
            ->where('status', 'signed')->count();

        return [
            [
                'id' => 'CMS2v11',
                'name' => 'Preventive Care and Screening: Screening for Depression',
                'description' => 'Percentage of patients aged 12 years and older screened for depression.',
                'denominator' => $totalPatients,
                'numerator' => $patientsWithScreening,
                'rate' => $totalPatients > 0 ? round(($patientsWithScreening / $totalPatients) * 100, 1) : 0,
                'target' => 85,
            ],
            [
                'id' => 'CMS68v11',
                'name' => 'Documentation of Current Medications',
                'description' => 'Percentage of visits for patients aged 18 years and older for which the provider documented current medications.',
                'denominator' => $totalPatients,
                'numerator' => $patientsWithMeds,
                'rate' => $totalPatients > 0 ? round(($patientsWithMeds / $totalPatients) * 100, 1) : 0,
                'target' => 95,
            ],
            [
                'id' => 'CLIN-001',
                'name' => 'Timely Note Completion',
                'description' => 'Percentage of encounters signed and closed within 48 hours.',
                'denominator' => $totalEncounters,
                'numerator' => $closedEncounters,
                'rate' => $totalEncounters > 0 ? round(($closedEncounters / $totalEncounters) * 100, 1) : 0,
                'target' => 90,
            ],
        ];
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cqm_report_'.$this->reportingPeriod.'.csv"',
        ];

        $measures = $this->measures;

        $callback = function () use ($measures) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Description', 'Numerator', 'Denominator', 'Rate (%)', 'Target (%)']);

            foreach ($measures as $measure) {
                fputcsv($file, [
                    $measure['id'],
                    $measure['name'],
                    $measure['description'],
                    $measure['numerator'],
                    $measure['denominator'],
                    $measure['rate'],
                    $measure['target'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.reporting.clinical-quality-measures', [
            'measures' => $this->measures,
        ]);
    }
}
