<?php

namespace App\Livewire\Patients;

use App\Models\EducationArticle;
use App\Models\Patient;
use App\Models\PatientEducationAssignment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientEducation extends Component
{
    public Patient $patient;

    public string $newTitle = '';

    public string $newContent = '';

    public string $newCategory = 'general';

    public string $newVideoUrl = '';

    public bool $showArticleForm = false;

    public ?EducationArticle $activeArticle = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function assignArticle(int $articleId): void
    {
        $article = EducationArticle::findOrFail($articleId);

        $already = PatientEducationAssignment::where('patient_id', $this->patient->id)
            ->where('education_article_id', $articleId)
            ->exists();

        if ($already) {
            Flux::toast(variant: 'warning', text: __('This article is already assigned to the patient.'));

            return;
        }

        PatientEducationAssignment::create([
            'practice_id' => $this->patient->practice_id,
            'patient_id' => $this->patient->id,
            'education_article_id' => $article->id,
            'assigned_by' => Auth::id(),
        ]);

        Flux::toast(variant: 'success', text: __('Article assigned to patient portal.'));
    }

    public function createArticle(): void
    {
        $this->validate([
            'newTitle' => 'required|string|min:3|max:255',
            'newContent' => 'required|string|min:10',
            'newCategory' => 'required|in:diagnosis,medication,general',
            'newVideoUrl' => 'nullable|url',
        ]);

        EducationArticle::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'title' => $this->newTitle,
            'content' => $this->newContent,
            'category' => $this->newCategory,
            'video_url' => $this->newVideoUrl ?: null,
            'is_published' => true,
        ]);

        $this->newTitle = '';
        $this->newContent = '';
        $this->newCategory = 'general';
        $this->newVideoUrl = '';
        $this->showArticleForm = false;

        Flux::toast(variant: 'success', text: __('Education article created and published.'));
    }

    public function viewArticle(int $articleId): void
    {
        $this->activeArticle = EducationArticle::findOrFail($articleId);
    }

    public function render(): View
    {
        $library = EducationArticle::where('practice_id', $this->patient->practice_id ?? Auth::user()->practice_id)
            ->where('is_published', true)
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        $assignments = PatientEducationAssignment::where('patient_id', $this->patient->id)
            ->with('article')
            ->latest()
            ->get();

        return view('livewire.patients.patient-education', [
            'library' => $library,
            'assignments' => $assignments,
        ]);
    }
}
