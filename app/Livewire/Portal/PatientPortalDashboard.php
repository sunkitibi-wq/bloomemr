<?php

namespace App\Livewire\Portal;

use App\Models\Appointment;
use App\Models\Assessment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Patient & Family Portal')]
class PatientPortalDashboard extends Component
{
    public ?Patient $patient = null;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())
                ->with(['primaryProvider', 'encounters', 'assessments'])
                ->first();
        } else {
            // Fallback for clinicians previewing the portal
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)
                ->with(['primaryProvider', 'encounters', 'assessments'])
                ->first();
        }
    }

    public function render(): View
    {
        $upcomingVisit = null;
        $upcomingAppointment = null;
        $completedAssessments = collect();
        $recentEncounters = collect();
        $invoices = collect();

        if ($this->patient) {
            $upcomingVisit = Encounter::where('patient_id', $this->patient->id)
                ->where('encounter_date', '>=', today())
                ->with('provider')
                ->first();

            $upcomingAppointment = Appointment::where('patient_id', $this->patient->id)
                ->whereDate('appointment_date', '>=', today())
                ->whereIn('status', ['scheduled', 'checked_in'])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->with('provider')
                ->first();

            $completedAssessments = Assessment::where('patient_id', $this->patient->id)
                ->latest()
                ->limit(5)
                ->get();

            $recentEncounters = Encounter::where('patient_id', $this->patient->id)
                ->where('status', 'signed')
                ->latest()
                ->limit(3)
                ->get();

            $invoices = Invoice::where('patient_id', $this->patient->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('livewire.portal.patient-portal-dashboard', [
            'upcomingVisit' => $upcomingVisit,
            'upcomingAppointment' => $upcomingAppointment,
            'completedAssessments' => $completedAssessments,
            'recentEncounters' => $recentEncounters,
            'invoices' => $invoices,
        ])->layout('layouts.blank');
    }
}
