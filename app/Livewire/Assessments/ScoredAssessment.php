<?php

namespace App\Livewire\Assessments;

use App\Actions\LogAudit;
use App\Models\Assessment;
use App\Models\Encounter;
use App\Models\Patient;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Scored Assessment')]
class ScoredAssessment extends Component
{
    public Patient $patient;

    public ?Encounter $encounter = null;

    public string $instrument = 'phq-9';

    public string $rater_type = 'patient';

    public array $responses = [];

    public array $instruments = [
        'phq-9' => [
            'name' => 'PHQ-9 (Depression Screening)',
            'description' => 'Over the last 2 weeks, how often have you been bothered by any of the following problems?',
            'choices' => [
                0 => 'Not at all',
                1 => 'Several days',
                2 => 'More than half the days',
                3 => 'Nearly every day',
            ],
            'questions' => [
                1 => 'Little interest or pleasure in doing things',
                2 => 'Feeling down, depressed, or hopeless',
                3 => 'Trouble falling or staying asleep, or sleeping too much',
                4 => 'Feeling tired or having little energy',
                5 => 'Poor appetite or overeating',
                6 => 'Feeling bad about yourself — or that you are a failure or have let yourself or your family down',
                7 => 'Trouble concentrating on things, such as reading the newspaper or watching television',
                8 => 'Moving or speaking so slowly that other people could have noticed? Or the opposite — being so fidgety or restless that you have been moving around a lot more than usual',
                9 => 'Thoughts that you would be better off dead or of hurting yourself in some way',
            ],
        ],
        'gad-7' => [
            'name' => 'GAD-7 (Anxiety Screening)',
            'description' => 'Over the last 2 weeks, how often have you been bothered by any of the following problems?',
            'choices' => [
                0 => 'Not at all',
                1 => 'Several days',
                2 => 'More than half the days',
                3 => 'Nearly every day',
            ],
            'questions' => [
                1 => 'Feeling nervous, anxious, or on edge',
                2 => 'Not being able to stop or control worrying',
                3 => 'Worrying too much about different things',
                4 => 'Trouble relaxing',
                5 => 'Being so restless that it is hard to sit still',
                6 => 'Becoming easily annoyed or irritable',
                7 => 'Feeling afraid as if something awful might happen',
            ],
        ],
    ];

    public function mount(Patient $patient, ?Encounter $encounter = null): void
    {
        $this->patient = $patient;
        $this->encounter = $encounter;

        $this->resetResponses();
    }

    public function updatedInstrument(): void
    {
        $this->resetResponses();
    }

    protected function resetResponses(): void
    {
        $this->responses = [];
        foreach (array_keys($this->instruments[$this->instrument]['questions']) as $qId) {
            $this->responses[$qId] = null;
        }
    }

    public function getScoreProperty(): int
    {
        $total = 0;
        foreach ($this->responses as $val) {
            if ($val !== null) {
                $total += (int) $val;
            }
        }

        return $total;
    }

    public function getSeverityBandProperty(): string
    {
        $score = $this->getScoreProperty();

        if ($this->instrument === 'phq-9') {
            if ($score <= 4) {
                return 'Minimal depression';
            }
            if ($score <= 9) {
                return 'Mild depression';
            }
            if ($score <= 14) {
                return 'Moderate depression';
            }
            if ($score <= 19) {
                return 'Moderately severe depression';
            }

            return 'Severe depression';
        }

        if ($this->instrument === 'gad-7') {
            if ($score <= 4) {
                return 'Minimal anxiety';
            }
            if ($score <= 9) {
                return 'Mild anxiety';
            }
            if ($score <= 14) {
                return 'Moderate anxiety';
            }

            return 'Severe anxiety';
        }

        return 'Unknown';
    }

    public function save(): void
    {
        // Check that all questions are answered
        foreach ($this->responses as $qId => $val) {
            if ($val === null) {
                Flux::toast(variant: 'danger', text: 'Please answer all questions before saving.');

                return;
            }
        }

        $assessment = Assessment::create([
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter?->id,
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'instrument' => $this->instrument,
            'rater_type' => $this->rater_type,
            'responses' => $this->responses,
            'score' => $this->getScoreProperty(),
            'severity_band' => $this->getSeverityBandProperty(),
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'create',
            entityType: 'assessment',
            entityId: $assessment->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: 'Assessment saved successfully.');

        if ($this->encounter) {
            $this->redirect(route('patients.show', $this->patient), navigate: true);
        } else {
            $this->redirect(route('patients.show', $this->patient), navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.assessments.scored-assessment');
    }
}
