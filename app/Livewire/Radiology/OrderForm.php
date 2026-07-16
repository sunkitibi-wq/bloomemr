<?php

namespace App\Livewire\Radiology;

use App\Models\Patient;
use App\Models\RadiologyOrder;
use Flux\Flux;
use Livewire\Component;

class OrderForm extends Component
{
    public RadiologyOrder $order;
    public $patients;
    
    public $patient_id;
    public $procedure_name;
    public $clinical_indication;

    public function mount()
    {
        $this->order = new RadiologyOrder();
        $this->patients = Patient::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    public function rules()
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'procedure_name' => 'required|string|max:255',
            'clinical_indication' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->order->fill([
            'patient_id' => $this->patient_id,
            'practice_id' => auth()->user()->practice_id,
            'procedure_name' => $this->procedure_name,
            'clinical_indication' => $this->clinical_indication,
            'status' => 'ordered',
            'order_date' => now(),
            'ordered_by' => auth()->id(),
        ]);

        $this->order->save();

        $this->dispatch('order-created');
        
        Flux::modal('create-order-modal')->close();
        Flux::toast('Radiology order created successfully.', variant: 'success');
        
        $this->reset(['patient_id', 'procedure_name', 'clinical_indication']);
    }

    public function render()
    {
        return view('livewire.radiology.order-form');
    }
}
