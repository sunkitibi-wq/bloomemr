<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Livewire\Attributes\Layout;
use Livewire\Component;

class VendorList extends Component
{
    public $search = '';

    public $filterStatus = 'all';

    public $isModalOpen = false;

    public $vendorId = null;

    public $name = '';

    public $contact_person = '';

    public $email = '';

    public $phone = '';

    public $status = 'active';

    public $address = '';

    public $notes = '';

    public function create()
    {
        $this->reset(['vendorId', 'name', 'contact_person', 'email', 'phone', 'status', 'address', 'notes']);
        $this->status = 'active';
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Vendor::updateOrCreate(
            ['id' => $this->vendorId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'contact_person' => $this->contact_person,
                'status' => $this->status,
                'address' => $this->address,
                'notes' => $this->notes,
            ]
        );

        $this->isModalOpen = false;
    }

    #[Layout('components.layouts.app.workspace')]
    public function render()
    {
        $query = Vendor::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('contact_person', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $vendors = $query->orderBy('name', 'asc')->get();

        return view('livewire.vendors.vendor-list', [
            'vendors' => $vendors,
        ]);
    }
}
