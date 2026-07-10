<?php

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Services\ClearinghouseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Claims Center')]
class ClaimsCenter extends Component
{
    use WithPagination;

    public $filterStatus = 'unsubmitted';

    public $selectedInvoices = [];

    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedInvoices = Invoice::where('practice_id', Auth::user()->practice_id)
                ->where('status', '!=', 'draft')
                ->where('insurance_claim_status', $this->filterStatus)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedInvoices = [];
        }
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->selectedInvoices = [];
        $this->selectAll = false;
    }

    public function batchSubmit()
    {
        $count = count($this->selectedInvoices);

        if ($count === 0) {
            session()->flash('error', __('Please select at least one claim to submit.'));

            return;
        }

        $service = app(ClearinghouseService::class);
        $invoices = Invoice::whereIn('id', $this->selectedInvoices)->get();

        $success = 0;
        foreach ($invoices as $invoice) {
            try {
                $service->submitClaim($invoice);
                $success++;
            } catch (\Exception $e) {
                // Log or handle individual claim error
            }
        }

        $this->selectedInvoices = [];
        $this->selectAll = false;

        session()->flash('message', __(':count claims successfully submitted to the clearinghouse.', ['count' => $success]));
    }

    public function render()
    {
        $claims = Invoice::with(['patient', 'encounter'])
            ->where('practice_id', Auth::user()->practice_id)
            ->where('status', '!=', 'draft')
            ->where('insurance_claim_status', $this->filterStatus)
            ->latest()
            ->paginate(20);

        return view('livewire.billing.claims-center', [
            'claims' => $claims,
        ]);
    }
}
