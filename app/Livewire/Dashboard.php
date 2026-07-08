<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Assessment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
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
        return Invoice::where('practice_id', Auth::user()->practice_id)
            ->where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');
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
