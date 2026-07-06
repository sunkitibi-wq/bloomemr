<?php

namespace App\Livewire\Portal;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Appointments')]
class PortalAppointments extends Component
{
    public ?Patient $patient = null;

    // Request Appointment Form
    public $appointmentDate = '';

    public $startTime = '';

    public $endTime = '';

    public $providerId = '';

    public $notes = '';

    public bool $isFormOpen = false;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }

        if ($this->patient) {
            $this->providerId = $this->patient->primary_provider_id ?? User::where('practice_id', $this->patient->practice_id)->first()?->id ?? '';
        }
        $this->appointmentDate = today()->addDay()->format('Y-m-d');
    }

    public function openRequestForm(): void
    {
        $this->appointmentDate = today()->addDay()->format('Y-m-d');
        $this->startTime = '09:00';
        $this->endTime = '09:30';
        $this->notes = '';
        $this->isFormOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'appointmentDate' => 'required|date|after_or_equal:today',
            'startTime' => 'required',
            'endTime' => 'required',
            'providerId' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        Appointment::create([
            'patient_id' => $this->patient->id,
            'provider_id' => $this->providerId,
            'practice_id' => $this->patient->practice_id,
            'appointment_date' => $this->appointmentDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'status' => 'scheduled',
            'notes' => $this->notes,
        ]);

        session()->flash('message', __('Appointment scheduled successfully.'));
        $this->isFormOpen = false;
    }

    public function render(): View
    {
        $appointments = collect();
        $providers = collect();

        if ($this->patient) {
            $appointments = Appointment::where('patient_id', $this->patient->id)
                ->with('provider')
                ->orderBy('appointment_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get();

            $providers = User::where('practice_id', $this->patient->practice_id)
                ->whereIn('role', ['attending', 'super_admin'])
                ->get();
        }

        return view('livewire.portal.portal-appointments', [
            'appointments' => $appointments,
            'providers' => $providers,
        ])->layout('layouts.blank');
    }
}
