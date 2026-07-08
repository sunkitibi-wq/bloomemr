<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Practice Analytics')]
class PracticeAnalytics extends Component
{
    public int $month;

    public int $year;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    #[Computed]
    public function clinicianUtilization()
    {
        return app(AnalyticsService::class)
            ->clinicianUtilization(Auth::user()->practice_id, $this->month, $this->year);
    }

    #[Computed]
    public function demographics()
    {
        return app(AnalyticsService::class)
            ->demographicBreakdown(Auth::user()->practice_id);
    }

    #[Computed]
    public function encounterTrend()
    {
        return app(AnalyticsService::class)
            ->monthlyEncounterTrend(Auth::user()->practice_id);
    }

    #[Computed]
    public function revenue()
    {
        return app(AnalyticsService::class)
            ->revenueSummary(Auth::user()->practice_id);
    }

    #[Computed]
    public function availableMonths(): array
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[] = ['value' => $date->month, 'year' => $date->year, 'label' => $date->format('F Y')];
        }

        return array_reverse($months);
    }

    public function render(): View
    {
        return view('livewire.practice-analytics');
    }
}
