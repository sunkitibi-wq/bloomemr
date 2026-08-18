<?php

namespace App\Livewire\Encounters;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Clinical Notes')]
#[Layout('components.layouts.app.workspace')]
class EncounterList extends Component
{
    use WithPagination;

    public $filterStatus = 'all';

    public $isCreateModalOpen = false;

    public $selectedPatientId = null;

    public function openCreateModal()
    {
        $this->selectedPatientId = null;
        $this->isCreateModalOpen = true;
    }

    public function proceedToCreate()
    {
        $this->validate([
            'selectedPatientId' => 'required|exists:patients,id',
        ], [
            'selectedPatientId.required' => 'Please select a patient first.',
        ]);

        return redirect()->route('encounters.create', $this->selectedPatientId);
    }

    public function render()
    {
        $query = Encounter::with(['patient', 'provider'])
            ->where('practice_id', Auth::user()->practice_id);

        if (! Auth::user()->is_system_admin) {
            $query->where('provider_id', Auth::id());
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $encounters = $query->latest('encounter_date')->paginate(15);
        $patients = Patient::where('practice_id', Auth::user()->practice_id)->orderBy('last_name')->get();

        return view('livewire.encounters.encounter-list', [
            'encounters' => $encounters,
            'patients' => $patients,
        ]);
    }
}
