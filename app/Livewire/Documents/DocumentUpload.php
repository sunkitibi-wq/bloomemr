<?php

namespace App\Livewire\Documents;

use App\Actions\LogAudit;
use App\Models\Document;
use App\Models\Patient;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Upload Document')]
class DocumentUpload extends Component
{
    use WithFileUploads;

    public Patient $patient;

    public $file;

    public string $category = 'other';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function save(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:pdf,docx,jpeg,png,tiff', 'max:102400'],
            'category' => ['required', 'string', 'in:outside_records,iep,behavioral_plan,school_report,prior_auth,other'],
        ]);

        $path = $this->file->store('documents/'.$this->patient->id, 'public');

        Document::create([
            'patient_id' => $this->patient->id,
            'uploaded_by' => Auth::id(),
            'category' => $this->category,
            'original_name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'version' => 1,
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'create',
            entityType: 'document',
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: 'Document uploaded.');

        $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.documents.document-upload');
    }
}
