<?php

namespace App\Livewire\Clinical;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Telemedicine')]
#[Layout('components.layouts.app.workspace')]
class TelemedicineDashboard extends Component
{
    use WithPagination;

    public $isCreateModalOpen = false;

    public $selectedPatientId = '';

    public function openCreateModal()
    {
        $this->selectedPatientId = '';
        $this->isCreateModalOpen = true;
    }

    public function startSession()
    {
        $this->validate([
            'selectedPatientId' => 'required|exists:patients,id',
        ], [
            'selectedPatientId.required' => 'Please select a patient first.',
        ]);

        return redirect()->route('patients.show', ['patient' => $this->selectedPatientId, 'tab' => 'telehealth']);
    }

    public function render(): View
    {
        $appointments = Appointment::with(['patient', 'provider'])
            ->where('practice_id', Auth::user()->practice_id)
            ->where('appointment_date', today())
            ->where('status', 'scheduled')
            ->where('type', 'like', '%tele%')
            ->orderBy('start_time')
            ->get();

        $patients = Patient::where('practice_id', Auth::user()->practice_id)
            ->orderBy('last_name')
            ->get();

        return view('livewire.clinical.telemedicine-dashboard', [
            'appointments' => $appointments,
            'patients' => $patients,
        ]);
    }
}
