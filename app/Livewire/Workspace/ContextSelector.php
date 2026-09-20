<?php

namespace App\Livewire\Workspace;

use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ContextSelector extends Component
{
    public $facilityId = null;
    public $departmentId = null;

    public function mount()
    {
        $this->facilityId = session('active_facility_id');
        $this->departmentId = session('active_department_id');

        $practice = Auth::user()->practice;
        if ($practice && !$this->facilityId) {
            $firstFacility = $practice->facilities()->first();
            if ($firstFacility) {
                $this->facilityId = $firstFacility->id;
                session(['active_facility_id' => $this->facilityId]);
            }
        }
    }

    public function updatedFacilityId($value)
    {
        session(['active_facility_id' => $value]);
        $this->departmentId = null; // Reset department
        session()->forget('active_department_id');
        $this->redirect(request()->header('Referer') ?? '/');
    }

    public function updatedDepartmentId($value)
    {
        session(['active_department_id' => $value]);
        $this->redirect(request()->header('Referer') ?? '/');
    }

    public function render()
    {
        $practice = Auth::user()->practice;
        $facilities = $practice ? $practice->facilities : collect();
        $departments = collect();

        if ($this->facilityId) {
            $facility = Facility::find($this->facilityId);
            if ($facility) {
                $departments = $facility->departments;
            }
        }

        return view('livewire.workspace.context-selector', [
            'facilities' => $facilities,
            'departments' => $departments,
        ]);
    }
}
