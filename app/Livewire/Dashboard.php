<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Assessment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
#[Layout('components.layouts.app.workspace')]
class Dashboard extends Component
{
    public $revenuePeriod = 'this_month';

    #[Computed]
    public function coSignQueue()
    {
        if (Auth::user()->role === 'attending') {
            return Encounter::with(['patient', 'provider'])
                ->where('status', 'co_sign_pending')
                ->latest()
                ->get();
        }

        return collect();
    }

    #[Computed]
    public function recentCharts()
    {
        $ids = session('recent_patients', []);
        if (empty($ids)) {
            return collect();
        }

        return Patient::findMany($ids)->sortBy(fn ($p) => array_search($p->id, $ids))->values();
    }

    #[Computed]
    public function clinicalAlerts()
    {
        $alerts = [];

        // 1. Unsigned notes > 24 hours
        $unsigned = Encounter::where('status', 'draft')
            ->where('provider_id', Auth::id())
            ->where('created_at', '<', now()->subDay())
            ->with('patient')
            ->get();

        foreach ($unsigned as $enc) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Unsigned note for {$enc->patient->full_name} from ".$enc->encounter_date->format('M j'),
                'action_url' => route('encounters.note', $enc),
            ];
        }

        // 2. High-risk flag: PHQ-9 >= 15 and no visit in 60 days
        $patients = Patient::with(['encounters' => fn ($q) => $q->latest()])->get();
        foreach ($patients as $pat) {
            $lastEncounter = $pat->encounters->first();
            if ($lastEncounter && $lastEncounter->encounter_date->lt(now()->subDays(60))) {
                $lastPHQ9 = Assessment::where('patient_id', $pat->id)
                    ->where('instrument', 'phq-9')
                    ->latest()
                    ->first();
                if ($lastPHQ9 && $lastPHQ9->score >= 15) {
                    $alerts[] = [
                        'type' => 'danger',
                        'message' => "High Risk: {$pat->full_name} (PHQ-9: {$lastPHQ9->score}) has no visit in 60+ days.",
                        'action_url' => route('patients.show', $pat),
                    ];
                }
            }
        }

        return collect($alerts);
    }

    #[Computed]
    public function todayCount()
    {
        return Encounter::whereDate('encounter_date', today())
            ->where('status', '!=', 'draft')
            ->count();
    }

    #[Computed]
    public function todayAppointments()
    {
        return Appointment::with(['patient', 'provider'])
            ->where('practice_id', Auth::user()->practice_id)
            ->whereDate('appointment_date', today())
            ->orderBy('start_time')
            ->get();
    }

    #[Computed]
    public function totalPatients()
    {
        return Patient::count();
    }

    #[Computed]
    public function recentEncounters()
    {
        return Encounter::with(['patient', 'provider'])
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function recentPatients()
    {
        return Patient::with('primaryProvider')
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function monthlyRevenue()
    {
        $query = Invoice::where('practice_id', Auth::user()->practice_id)
            ->where('status', 'paid');

        if ($this->revenuePeriod === 'this_month') {
            $query->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year);
        } elseif ($this->revenuePeriod === 'last_month') {
            $query->whereMonth('updated_at', now()->subMonth()->month)
                ->whereYear('updated_at', now()->subMonth()->year);
        } elseif ($this->revenuePeriod === 'this_year') {
            $query->whereYear('updated_at', now()->year);
        }

        return $query->sum('total_amount');
    }

    #[Computed]
    public function revenueChartData()
    {
        $query = Invoice::where('practice_id', Auth::user()->practice_id)
            ->where('status', 'paid');

        if ($this->revenuePeriod === 'this_month') {
            $query->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year);
        } elseif ($this->revenuePeriod === 'last_month') {
            $query->whereMonth('updated_at', now()->subMonth()->month)
                ->whereYear('updated_at', now()->subMonth()->year);
        } elseif ($this->revenuePeriod === 'this_year') {
            $query->whereYear('updated_at', now()->year);
        }

        $invoices = $query->get(['total_amount', 'updated_at']);

        // Group by day for month views, by month for year view
        if ($this->revenuePeriod === 'this_year') {
            $grouped = $invoices->groupBy(function ($item) {
                return $item->updated_at->format('M');
            });
            $labels = collect(range(1, 12))->map(fn ($m) => now()->month($m)->format('M'))->toArray();

            $data = [];
            foreach ($labels as $label) {
                $data[] = isset($grouped[$label]) ? $grouped[$label]->sum('total_amount') : 0;
            }
        } else {
            $grouped = $invoices->groupBy(function ($item) {
                return $item->updated_at->format('M j');
            });

            // Generate daily labels for the month
            $daysInMonth = $this->revenuePeriod === 'last_month' ? now()->subMonth()->daysInMonth : now()->daysInMonth;
            $month = $this->revenuePeriod === 'last_month' ? now()->subMonth() : now();

            $labels = [];
            $data = [];

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = $month->copy()->day($i);
                $label = $date->format('M j');
                $labels[] = $label;
                $data[] = isset($grouped[$label]) ? $grouped[$label]->sum('total_amount') : 0;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    #[Computed]
    public function demographicStats()
    {
        $stats = Patient::select('gender_identity')
            ->selectRaw('count(*) as count')
            ->groupBy('gender_identity')
            ->pluck('count', 'gender_identity')
            ->toArray();

        $total = array_sum($stats);
        if ($total === 0) {
            return [];
        }

        $formatted = [];
        $colors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#94a3b8'];
        $i = 0;

        foreach ($stats as $gender => $count) {
            $label = $gender ? ucfirst(str_replace('_', ' ', $gender)) : 'Unspecified';
            $formatted[] = [
                'label' => $label,
                'count' => $count,
                'percentage' => round(($count / $total) * 100),
                'color' => $colors[$i % count($colors)],
            ];
            $i++;
        }

        return $formatted;
    }

    #[Computed]
    public function unpaidInvoices()
    {
        return Invoice::where('practice_id', Auth::user()->practice_id)
            ->whereIn('status', ['draft', 'pending'])
            ->count();
    }

    #[Computed]
    public function thisMonthEncounters()
    {
        return Encounter::where('practice_id', Auth::user()->practice_id)
            ->whereMonth('encounter_date', now()->month)
            ->whereYear('encounter_date', now()->year)
            ->count();
    }

    #[Computed]
    public function pendingCount()
    {
        return Encounter::whereIn('status', ['draft', 'co_sign_pending'])
            ->where('provider_id', Auth::id())
            ->count();
    }

    #[Computed]
    public function pendingTasksCount()
    {
        return Task::where('assigned_to', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
    }

    #[Computed]
    public function completedTasksCount()
    {
        return Task::where('assigned_to', Auth::id())
            ->where('status', 'completed')
            ->count();
    }

    #[Computed]
    public function unreadNotificationCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    #[Computed]
    public function unreadNotifications()
    {
        return Auth::user()->unreadNotifications()->latest()->limit(10)->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
