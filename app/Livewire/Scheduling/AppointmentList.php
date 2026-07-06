<?php

namespace App\Livewire\Scheduling;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Scheduling')]
class AppointmentList extends Component
{
    use WithPagination;

    public $filterDate;

    public $filterProvider = '';

    public $filterPatient = '';

    // Form fields
    public $appointmentId = null;

    public $patientId = '';

    public $providerId = '';

    public $appointmentDate = '';

    public $startTime = '';

    public $endTime = '';

    public $status = 'scheduled';

    public $notes = '';

    public bool $isFormOpen = false;

    public function mount(): void
    {
        $this->filterDate = today()->format('Y-m-d');
        $this->appointmentDate = today()->format('Y-m-d');
        $this->providerId = Auth::user()->role === 'attending' ? Auth::id() : '';
    }

    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProvider(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPatient(): void
    {
        $this->resetPage();
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->appointmentDate = $this->filterDate ?: today()->format('Y-m-d');
        $this->providerId = Auth::user()->role === 'attending' ? Auth::id() : User::first()?->id ?? '';
        $this->isFormOpen = true;
    }

    public function edit(Appointment $appointment): void
    {
        $this->appointmentId = $appointment->id;
        $this->patientId = $appointment->patient_id;
        $this->providerId = $appointment->provider_id;
        $this->appointmentDate = $appointment->appointment_date->format('Y-m-d');
        $this->startTime = $appointment->start_time;
        $this->endTime = $appointment->end_time;
        $this->status = $appointment->status;
        $this->notes = $appointment->notes ?? '';
        $this->isFormOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'patientId' => 'required|exists:patients,id',
            'providerId' => 'required|exists:users,id',
            'appointmentDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'status' => 'required|in:scheduled,checked_in,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'patient_id' => $this->patientId,
            'provider_id' => $this->providerId,
            'practice_id' => Auth::user()->practice_id,
            'appointment_date' => $this->appointmentDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->appointmentId) {
            $appointment = Appointment::findOrFail($this->appointmentId);
            $appointment->update($data);
            session()->flash('message', __('Appointment updated successfully.'));
        } else {
            Appointment::create($data);
            session()->flash('message', __('Appointment created successfully.'));
        }

        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(Appointment $appointment): void
    {
        $appointment->delete();
        session()->flash('message', __('Appointment deleted successfully.'));
    }

    public function updateStatus(Appointment $appointment, string $newStatus): void
    {
        if (in_array($newStatus, ['scheduled', 'checked_in', 'completed', 'cancelled', 'no_show'])) {
            $appointment->update(['status' => $newStatus]);
            session()->flash('message', __('Appointment status updated to :status.', ['status' => __($newStatus)]));
        }
    }

    public function resetForm(): void
    {
        $this->appointmentId = null;
        $this->patientId = '';
        $this->providerId = '';
        $this->appointmentDate = today()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->status = 'scheduled';
        $this->notes = '';
    }

    public function render(): View
    {
        $query = Appointment::query()
            ->with(['patient', 'provider'])
            ->where('practice_id', Auth::user()->practice_id);

        if ($this->filterDate) {
            $query->whereDate('appointment_date', $this->filterDate);
        }

        if ($this->filterProvider) {
            $query->where('provider_id', $this->filterProvider);
        }

        if ($this->filterPatient) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'like', '%'.$this->filterPatient.'%')
                    ->orWhere('last_name', 'like', '%'.$this->filterPatient.'%')
                    ->orWhere('mrn', 'like', '%'.$this->filterPatient.'%');
            });
        }

        $appointments = $query->orderBy('start_time')->paginate(15);
        $patients = Patient::where('practice_id', Auth::user()->practice_id)->orderBy('last_name')->get();
        $providers = User::where('practice_id', Auth::user()->practice_id)
            ->whereIn('role', ['attending', 'super_admin'])
            ->orderBy('name')
            ->get();

        return view('livewire.scheduling.appointment-list', [
            'appointments' => $appointments,
            'patients' => $patients,
            'providers' => $providers,
        ]);
    }
}
