<?php

namespace App\Livewire\Radiology;

use App\Models\RadiologyOrder;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Radiology Management')]
class RadiologyManager extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $orderIdToComplete;

    public function mount()
    {
        abort_if(auth()->user()->role === 'patient', 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = RadiologyOrder::query()
            ->with(['patient', 'orderedBy'])
            ->when($this->search, function (Builder $query) {
                $query->whereHas('patient', function (Builder $q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest('order_date')
            ->paginate(10);

        return view('livewire.radiology.radiology-manager', [
            'orders' => $orders,
        ]);
    }
}
