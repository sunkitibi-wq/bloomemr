<?php

namespace App\Livewire\Crm;

use App\Models\Patient;
use App\Models\PatientEngagement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Campaign Manager')]
class CampaignManager extends Component
{
    public $campaignName = '';
    public $campaignType = 'email';
    public $message = '';
    public $targetCondition = '';

    public function generateCampaign()
    {
        $this->validate([
            'campaignName' => 'required|string|max:255',
            'campaignType' => 'required|in:email,sms,call,letter',
            'message' => 'required|string',
        ]);

        $query = Patient::where('practice_id', Auth::user()->practice_id);

        if ($this->targetCondition) {
            $query->where(function($q) {
                $q->whereHas('encounters', function($en) {
                    $en->where('clinical_notes', 'like', '%' . $this->targetCondition . '%');
                })->orWhereHas('medications', function($m) {
                    $m->where('name', 'like', '%' . $this->targetCondition . '%');
                });
            });
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            session()->flash('error', 'No patients match this criteria.');
            return;
        }

        $count = 0;
        foreach ($patients as $patient) {
            PatientEngagement::create([
                'practice_id' => Auth::user()->practice_id,
                'patient_id' => $patient->id,
                'assigned_to' => Auth::id(),
                'title' => $this->campaignName,
                'description' => $this->message,
                'type' => $this->campaignType,
                'status' => 'pending',
                'due_date' => now()->addDays(7),
            ]);
            $count++;
        }

        session()->flash('message', "Campaign created! $count patients enrolled.");
        
        $this->reset(['campaignName', 'campaignType', 'message', 'targetCondition']);
    }

    public function render()
    {
        $engagements = PatientEngagement::with(['patient', 'assignee'])
            ->where('practice_id', Auth::user()->practice_id)
            ->latest()
            ->paginate(15);

        return view('livewire.crm.campaign-manager', [
            'engagements' => $engagements
        ]);
    }
}
