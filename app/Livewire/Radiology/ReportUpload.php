<?php

namespace App\Livewire\Radiology;

use App\Models\RadiologyOrder;
use App\Models\RadiologyReport;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReportUpload extends Component
{
    use WithFileUploads;

    public RadiologyOrder $order;
    public $findings;
    public $impression;
    public $attachment;

    public function mount(RadiologyOrder $order)
    {
        $this->order = $order;
    }

    public function rules()
    {
        return [
            'findings' => 'required|string',
            'impression' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB Max
        ];
    }

    public function save()
    {
        $this->validate();

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('radiology-reports', 'local');
        }

        $report = new RadiologyReport([
            'practice_id' => auth()->user()->practice_id,
            'radiology_order_id' => $this->order->id,
            'patient_id' => $this->order->patient_id,
            'radiologist_id' => auth()->id(), // Assuming the uploader is the radiologist or representative
            'findings' => $this->findings,
            'impression' => $this->impression,
            'attachment_path' => $path,
            'reported_at' => now(),
        ]);

        $report->save();

        $this->order->update([
            'status' => 'reported',
            'completed_at' => now(),
        ]);

        $this->dispatch('report-uploaded');
        
        Flux::modal('upload-report-modal-' . $this->order->id)->close();
        Flux::toast('Radiology report saved successfully.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.radiology.report-upload');
    }
}
