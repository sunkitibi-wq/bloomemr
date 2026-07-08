<?php

namespace App\Livewire\Portal;

use App\Models\Document;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Document Upload')]
class PortalDocumentUpload extends Component
{
    use WithFileUploads;

    public ?Patient $patient = null;

    #[Validate(['file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,tiff|max:20480'])]
    public $file = null;

    public string $category = 'School Report';

    public string $notes = '';

    /** @var string[] */
    public array $categories = [
        'IEP',
        'School Report',
        'Behavioral Plan',
        'Consent',
        'Outside Records',
        'Other',
    ];

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function upload(): void
    {
        $this->validate();

        if (! $this->patient) {
            return;
        }

        $path = $this->file->store("patients/{$this->patient->id}/portal-uploads", 'public');

        Document::create([
            'patient_id' => $this->patient->id,
            'practice_id' => $this->patient->practice_id,
            'uploaded_by' => auth('portal')->id() ?? auth()->id(),
            'category' => $this->category,
            'original_name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'version' => 1,
        ]);

        session()->flash('message', __('Document uploaded successfully. Your care team will be notified.'));

        $this->file = null;
        $this->notes = '';
    }

    public function render(): View
    {
        $recentUploads = collect();

        if ($this->patient) {
            $recentUploads = Document::where('patient_id', $this->patient->id)
                ->where('uploaded_by', auth('portal')->id() ?? auth()->id())
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('livewire.portal.portal-document-upload', [
            'recentUploads' => $recentUploads,
        ])->layout('layouts.blank');
    }
}
