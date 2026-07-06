<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Patient;
use App\Models\PatientEngagement;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Patient Engagement')]
class PatientEngagements extends Component
{
    public Patient $patient;

    public string $title = '';

    public string $description = '';

    public string $type = 'call';

    public string $dueDate = '';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->patient->load('engagements');
        $this->dueDate = now()->addDay()->toDateString();
    }

    public function createEngagement(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:call,text,email,portal_message'],
            'dueDate' => ['required', 'date'],
        ]);

        $task = PatientEngagement::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'patient_id' => $this->patient->id,
            'assigned_to' => Auth::id(),
            'title' => trim($this->title),
            'description' => trim($this->description),
            'type' => $this->type,
            'status' => 'planned',
            'due_date' => $this->dueDate,
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'create',
            entityType: 'patient_engagement',
            entityId: $task->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Engagement task created.'));

        $this->reset(['title', 'description', 'type', 'dueDate']);
        $this->dueDate = now()->addDay()->toDateString();
        $this->patient->load('engagements');
    }

    public function completeEngagement(PatientEngagement $engagement): void
    {
        $engagement->update(['status' => 'completed']);
        $this->patient->load('engagements');
    }

    public function render(): View
    {
        return view('livewire.patients.patient-engagements');
    }
}
