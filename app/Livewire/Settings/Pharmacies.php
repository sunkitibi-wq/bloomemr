<?php

namespace App\Livewire\Settings;

use App\Models\Pharmacy;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pharmacies')]
class Pharmacies extends Component
{
    public array $pharmacies = [];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?int $selectedPharmacyId = null;

    public string $name = '';

    public string $ncpdp = '';

    public string $phone = '';

    public string $address = '';

    public function mount(): void
    {
        if (! Gate::allows('manage_users') && ! Gate::allows('view_pharmacy_settings')) {
            abort(403, 'Unauthorized.');
        }

        $this->loadData();
    }

    public function loadData(): void
    {
        $this->pharmacies = Pharmacy::where('practice_id', Auth::user()->practice_id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $pharmacyId): void
    {
        $this->resetForm();
        $pharmacy = Pharmacy::findOrFail($pharmacyId);

        if ($pharmacy->practice_id !== Auth::user()->practice_id) {
            abort(403);
        }

        $this->selectedPharmacyId = $pharmacy->id;
        $this->name = $pharmacy->name;
        $this->ncpdp = $pharmacy->ncpdp ?? '';
        $this->phone = $pharmacy->phone ?? '';
        $this->address = $pharmacy->address ?? '';
        $this->showEditModal = true;
    }

    public function savePharmacy(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'ncpdp' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->selectedPharmacyId) {
            $pharmacy = Pharmacy::findOrFail($this->selectedPharmacyId);
            if ($pharmacy->practice_id !== Auth::user()->practice_id) {
                abort(403);
            }

            $pharmacy->fill($validated);
            $pharmacy->save();
            Flux::toast(variant: 'success', text: __('Pharmacy updated.'));
            $this->showEditModal = false;
        } else {
            Pharmacy::create([
                'practice_id' => Auth::user()->practice_id,
                ...$validated,
            ]);
            Flux::toast(variant: 'success', text: __('Pharmacy created.'));
            $this->showCreateModal = false;
        }

        $this->resetForm();
        $this->loadData();
    }

    public function deletePharmacy(int $pharmacyId): void
    {
        $pharmacy = Pharmacy::findOrFail($pharmacyId);
        if ($pharmacy->practice_id !== Auth::user()->practice_id) {
            abort(403);
        }

        $pharmacy->delete();
        Flux::toast(variant: 'success', text: __('Pharmacy deleted.'));
        $this->loadData();
    }

    public function resetForm(): void
    {
        $this->selectedPharmacyId = null;
        $this->name = '';
        $this->ncpdp = '';
        $this->phone = '';
        $this->address = '';
        $this->showCreateModal = false;
        $this->showEditModal = false;
    }

    public function render(): View
    {
        return view('livewire.settings.pharmacies');
    }
}
