<?php

namespace App\Livewire\Portal;

use App\Models\EducationArticle;
use App\Models\Patient;
use App\Models\PatientEducationAssignment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Education Library')]
class PortalEducation extends Component
{
    public ?Patient $patient = null;

    public ?EducationArticle $activeArticle = null;

    public string $selectedCategory = 'all';

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function selectArticle(int $articleId): void
    {
        $this->activeArticle = EducationArticle::findOrFail($articleId);
    }

    public function acknowledge(int $assignmentId): void
    {
        if (! $this->patient) {
            return;
        }

        $assignment = PatientEducationAssignment::where('patient_id', $this->patient->id)
            ->findOrFail($assignmentId);

        $assignment->update(['acknowledged_at' => now()]);

        session()->flash('message', __('Article acknowledged.'));
    }

    public function render(): View
    {
        $articles = collect();
        $assignments = collect();

        if ($this->patient) {
            $query = EducationArticle::where('practice_id', $this->patient->practice_id)
                ->where('is_published', true);

            if ($this->selectedCategory !== 'all') {
                $query->where('category', $this->selectedCategory);
            }

            $articles = $query->orderBy('title')->get();

            $assignments = PatientEducationAssignment::where('patient_id', $this->patient->id)
                ->with('article')
                ->latest()
                ->get();
        }

        return view('livewire.portal.portal-education', [
            'articles' => $articles,
            'assignments' => $assignments,
        ])->layout('layouts.blank');
    }
}
