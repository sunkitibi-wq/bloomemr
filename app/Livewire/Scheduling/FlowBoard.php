<?php

namespace App\Livewire\Scheduling;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class FlowBoard extends Component
{
    public $date;

    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function updateStatus($appointmentId, $newStatus)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => $newStatus]);
    }

    public function render()
    {
        $appointments = Appointment::with(['patient', 'provider'])
            ->whereDate('appointment_date', $this->date)
            ->orderBy('start_time')
            ->get();

        $boards = [
            'Scheduled' => $appointments->filter(fn ($a) => !in_array($a->status, ['arrived', 'triaged', 'in-room', 'completed', 'cancelled'])),
            'Arrived' => $appointments->where('status', 'arrived'),
            'Triaged' => $appointments->where('status', 'triaged'),
            'In Room' => $appointments->where('status', 'in-room'),
            'Completed' => $appointments->where('status', 'completed'),
        ];

        return view('livewire.scheduling.flow-board', [
            'boards' => $boards,
        ]);
    }
}
