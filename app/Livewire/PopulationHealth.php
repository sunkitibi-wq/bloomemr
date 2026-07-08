<?php

namespace App\Livewire;

use App\Models\CareGap;
use App\Models\PatientCohort;
use App\Services\PopulationHealthService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Population Health')]
class PopulationHealth extends Component
{
    public string $activeTab = 'cohorts';

    public string $gapStatusFilter = 'open';

    #[Computed]
    public function cohorts()
    {
        return PatientCohort::where('practice_id', Auth::user()->practice_id)
            ->withCount('members')
            ->get();
    }

    #[Computed]
    public function careGaps()
    {
        return CareGap::where('practice_id', Auth::user()->practice_id)
            ->where('status', $this->gapStatusFilter)
            ->with('patient')
            ->orderBy('due_date')
            ->get();
    }

    #[Computed]
    public function openGapCount()
    {
        return CareGap::where('practice_id', Auth::user()->practice_id)
            ->where('status', 'open')
            ->count();
    }

    #[Computed]
    public function cohortCount()
    {
        return PatientCohort::where('practice_id', Auth::user()->practice_id)->count();
    }

    #[Computed]
    public function totalEnrolled()
    {
        return PatientCohort::where('practice_id', Auth::user()->practice_id)
            ->withCount('members')
            ->get()
            ->sum('members_count');
    }

    public function refreshRegistries(PopulationHealthService $service): void
    {
        $service->refreshCohorts(Auth::user()->practice_id);

        session()->flash('success', 'Cohort registries and care gaps have been refreshed.');
    }

    public function resolveCareGap(int $gapId, PopulationHealthService $service): void
    {
        $gap = CareGap::findOrFail($gapId);
        $service->resolveCareGap($gap);

        session()->flash('success', 'Care gap marked as resolved.');
    }

    public function render(): View
    {
        return view('livewire.population-health');
    }
}
