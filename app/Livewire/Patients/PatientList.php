<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Patients')]
class PatientList extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(): View
    {
        return view('livewire.patients.patient-list');
    }

    #[Computed]
    public function patients()
    {
        return Patient::query()
            ->when($this->search, fn ($q) => $q->whereAny([
                'first_name', 'last_name', 'mrn', 'phone', 'email',
            ], 'like', "%{$this->search}%"))
            ->with('primaryProvider')
            ->latest()
            ->paginate(15);
    }
}
