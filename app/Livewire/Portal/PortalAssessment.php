<?php

namespace App\Livewire\Portal;

use App\Models\Assessment;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Assessments')]
class PortalAssessment extends Component
{
    public ?Patient $patient = null;

    public ?Assessment $activeAssessment = null;

    /** @var array<int|string, int|null> */
    public array $responses = [];

    public bool $isSubmitted = false;

    public int $score = 0;

    public string $severityBand = '';

    /** @var array<string, array<string, mixed>> */
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
                6 => 'Feeling bad about yourself — or feeling like a failure',
                7 => 'Trouble concentrating on things, such as reading or watching television',
                8 => 'Moving or speaking so slowly that other people have noticed, or being so restless you move around a lot',
                9 => 'Thoughts that you would be better off dead or of hurting yourself',
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
        'scared' => [
            'name' => 'SCARED (Screen for Child Anxiety)',
            'description' => 'How true is the following statement for you? (0 = Not True, 1 = Somewhat True, 2 = Very True)',
            'choices' => [
                0 => 'Not True',
                1 => 'Somewhat True',
                2 => 'Very True',
            ],
            'questions' => [
                1 => 'When I feel frightened, it is hard to breathe',
                2 => 'I get headaches when I am at school',
                3 => 'I don\'t like to be with people I don\'t know well',
                4 => 'I get scared if I sleep away from home',
                5 => 'I worry about other people liking me',
                6 => 'When I get frightened, I feel like passing out',
                7 => 'I am nervous',
                8 => 'I follow my mother or father wherever they go',
            ],
        ],
    ];

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function startAssessment(int $assessmentId): void
    {
        if (! $this->patient) {
            return;
        }

        $assessment = Assessment::where('patient_id', $this->patient->id)
            ->whereNull('score')
            ->findOrFail($assessmentId);

        $this->activeAssessment = $assessment;
        $this->isSubmitted = false;
        $this->score = 0;
        $this->severityBand = '';

        // Initialise response slots
        $instrument = $this->instruments[$assessment->instrument] ?? null;
        if ($instrument) {
            $this->responses = [];
            foreach (array_keys($instrument['questions']) as $qId) {
                $this->responses[$qId] = null;
            }
        }
    }

    public function submitAssessment(): void
    {
        if (! $this->activeAssessment) {
            return;
        }

        // Ensure all questions answered
        foreach ($this->responses as $val) {
            if ($val === null) {
                session()->flash('error', __('Please answer all questions before submitting.'));

                return;
            }
        }

        $total = (int) array_sum(array_map('intval', $this->responses));
        $band = $this->calculateSeverityBand($this->activeAssessment->instrument, $total);

        $this->activeAssessment->update([
            'responses' => $this->responses,
            'score' => $total,
            'severity_band' => $band,
            'rater_type' => 'self_report',
        ]);

        $this->score = $total;
        $this->severityBand = $band;
        $this->isSubmitted = true;

        session()->flash('message', __('Assessment submitted. Your care team will review the results.'));
    }

    protected function calculateSeverityBand(string $instrument, int $score): string
    {
        if ($instrument === 'phq-9') {
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

        if ($instrument === 'gad-7') {
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

        if ($instrument === 'scared') {
            return $score >= 25 ? 'Likely anxiety disorder' : 'Below threshold';
        }

        return 'Scored';
    }

    public function render(): View
    {
        $pendingAssessments = collect();
        $completedAssessments = collect();

        if ($this->patient) {
            $pendingAssessments = Assessment::where('patient_id', $this->patient->id)
                ->whereNull('score')
                ->latest()
                ->get();

            $completedAssessments = Assessment::where('patient_id', $this->patient->id)
                ->whereNotNull('score')
                ->latest()
                ->limit(10)
                ->get();
        }

        $activeInstrument = $this->activeAssessment
            ? ($this->instruments[$this->activeAssessment->instrument] ?? null)
            : null;

        return view('livewire.portal.portal-assessment', [
            'pendingAssessments' => $pendingAssessments,
            'completedAssessments' => $completedAssessments,
            'activeInstrument' => $activeInstrument,
        ])->layout('layouts.blank');
    }
}
