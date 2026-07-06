<?php

namespace App\Livewire\Billing;

use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Billing Manager')]
class BillingManager extends Component
{
    use WithPagination;

    public $filterStatus = '';

    public $filterPatient = '';

    // Form fields
    public $invoiceId = null;

    public $patientId = '';

    public $encounterId = '';

    public $dueDate = '';

    public $status = 'draft';

    public $claimStatus = 'unsubmitted';

    public $claimReference = '';

    public $claimNote = '';

    public $totalAmount = 0.00;

    // CPT list
    public array $cptCodes = [];

    public $newCptCode = '';

    public $newCptDesc = '';

    public $newCptFee = 0.00;

    // Default CPT codes helper list
    public array $defaultCpts = [
        ['code' => '90791', 'description' => 'Psychiatric Diagnostic Evaluation', 'fee' => 150.00],
        ['code' => '90834', 'description' => 'Psychotherapy, 45 minutes', 'fee' => 110.00],
        ['code' => '96112', 'description' => 'Developmental Test Administration', 'fee' => 180.00],
        ['code' => '90863', 'description' => 'Pharmacologic Management', 'fee' => 90.00],
    ];

    public bool $isFormOpen = false;

    // EDI Modal Properties
    public bool $showEdiModal = false;

    public ?string $ediRequestPayload = null;

    public ?string $ediResponsePayload = null;

    public ?Invoice $ediInvoice = null;

    public function mount(): void
    {
        $this->dueDate = today()->addDays(30)->format('Y-m-d');
    }

    public function updatedFilterStatus(): void
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
        $this->isFormOpen = true;
    }

    public function edit(Invoice $invoice): void
    {
        $this->invoiceId = $invoice->id;
        $this->patientId = $invoice->patient_id;
        $this->encounterId = $invoice->encounter_id ?? '';
        $this->dueDate = $invoice->due_date->format('Y-m-d');
        $this->status = $invoice->status;
        $this->claimStatus = $invoice->insurance_claim_status;
        $this->claimReference = $invoice->claim_reference ?? '';
        $this->claimNote = $invoice->claim_note ?? '';
        $this->cptCodes = $invoice->cpt_codes ?? [];
        $this->totalAmount = $invoice->total_amount;
        $this->isFormOpen = true;
    }

    public function selectDefaultCpt(string $code): void
    {
        foreach ($this->defaultCpts as $cpt) {
            if ($cpt['code'] === $code) {
                $this->cptCodes[] = [
                    'code' => $cpt['code'],
                    'description' => $cpt['description'],
                    'fee' => $cpt['fee'],
                ];
                $this->recalculateTotal();
                break;
            }
        }
    }

    public function addCustomCpt(): void
    {
        $this->validate([
            'newCptCode' => 'required|string',
            'newCptDesc' => 'required|string',
            'newCptFee' => 'required|numeric|min:0',
        ]);

        $this->cptCodes[] = [
            'code' => $this->newCptCode,
            'description' => $this->newCptDesc,
            'fee' => (float) $this->newCptFee,
        ];

        $this->newCptCode = '';
        $this->newCptDesc = '';
        $this->newCptFee = 0.00;

        $this->recalculateTotal();
    }

    public function removeCpt(int $index): void
    {
        unset($this->cptCodes[$index]);
        $this->cptCodes = array_values($this->cptCodes);
        $this->recalculateTotal();
    }

    public function recalculateTotal(): void
    {
        $this->totalAmount = array_reduce($this->cptCodes, fn ($sum, $cpt) => $sum + $cpt['fee'], 0.00);
    }

    public function save(): void
    {
        $this->validate([
            'patientId' => 'required|exists:patients,id',
            'encounterId' => 'nullable|exists:encounters,id',
            'dueDate' => 'required|date',
            'status' => 'required|in:draft,pending,paid,void',
            'claimStatus' => 'required|in:unsubmitted,submitted,accepted,rejected',
            'cptCodes' => 'required|array|min:1',
        ]);

        $data = [
            'patient_id' => $this->patientId,
            'encounter_id' => $this->encounterId ?: null,
            'practice_id' => Auth::user()->practice_id,
            'cpt_codes' => $this->cptCodes,
            'total_amount' => $this->totalAmount,
            'status' => $this->status,
            'insurance_claim_status' => $this->claimStatus,
            'claim_reference' => $this->claimReference ?: null,
            'claim_note' => $this->claimNote ?: null,
            'due_date' => $this->dueDate,
        ];

        if ($this->invoiceId) {
            $invoice = Invoice::findOrFail($this->invoiceId);
            $invoice->update($data);
            session()->flash('message', __('Invoice updated successfully.'));
        } else {
            Invoice::create($data);
            session()->flash('message', __('Invoice created successfully.'));
        }

        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
        session()->flash('message', __('Invoice deleted successfully.'));
    }

    public function updateStatus(Invoice $invoice, string $newStatus): void
    {
        if (in_array($newStatus, ['draft', 'pending', 'paid', 'void'])) {
            $invoice->update(['status' => $newStatus]);
            session()->flash('message', __('Invoice status updated to :status.', ['status' => __($newStatus)]));
        }
    }

    public function updateClaimStatus(Invoice $invoice, string $newClaimStatus): void
    {
        if (in_array($newClaimStatus, ['unsubmitted', 'submitted', 'accepted', 'rejected'])) {
            $data = [
                'insurance_claim_status' => $newClaimStatus,
                'claim_reference' => $newClaimStatus === 'submitted' ? 'CLM-'.str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT) : $invoice->claim_reference,
                'claim_note' => $newClaimStatus === 'rejected' ? 'Claim returned for follow-up.' : $invoice->claim_note,
                'submitted_at' => $newClaimStatus === 'submitted' ? now() : $invoice->submitted_at,
                'accepted_at' => $newClaimStatus === 'accepted' ? now() : $invoice->accepted_at,
            ];

            if ($newClaimStatus === 'unsubmitted') {
                $data['submitted_at'] = null;
                $data['accepted_at'] = null;
            }

            $invoice->update($data);
            session()->flash('message', __('Insurance claim status updated to :status.', ['status' => __($newClaimStatus)]));
        }
    }

    public function resetForm(): void
    {
        $this->invoiceId = null;
        $this->patientId = '';
        $this->encounterId = '';
        $this->dueDate = today()->addDays(30)->format('Y-m-d');
        $this->status = 'draft';
        $this->claimStatus = 'unsubmitted';
        $this->claimReference = '';
        $this->claimNote = '';
        $this->cptCodes = [];
        $this->totalAmount = 0.00;
        $this->newCptCode = '';
        $this->newCptDesc = '';
        $this->newCptFee = 0.00;
    }

    public function submitClaim(int $invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $service = app(\App\Services\ClearinghouseService::class);
        $service->submitClaim($invoice);
        session()->flash('message', __('Insurance claim submitted to clearinghouse.'));
    }

    public function viewEdiPayload(int $invoiceId): void
    {
        $this->ediInvoice = Invoice::findOrFail($invoiceId);
        $submission = \App\Models\ClaimSubmission::where('invoice_id', $invoiceId)->latest()->first();

        if ($submission) {
            $this->ediRequestPayload = $submission->edi_request;
            $this->ediResponsePayload = $submission->edi_response;
        } else {
            // Generate preview if not submitted yet
            $service = app(\App\Services\ClearinghouseService::class);
            $this->ediRequestPayload = $service->generateEdi837($this->ediInvoice);
            $this->ediResponsePayload = __('Claim not yet submitted. Click "Submit Claim" to transmit.');
        }

        $this->showEdiModal = true;
    }

    public function render(): View
    {
        $query = Invoice::query()
            ->with(['patient', 'encounter'])
            ->where('practice_id', Auth::user()->practice_id);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPatient) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'like', '%'.$this->filterPatient.'%')
                    ->orWhere('last_name', 'like', '%'.$this->filterPatient.'%')
                    ->orWhere('mrn', 'like', '%'.$this->filterPatient.'%');
            });
        }

        $invoices = $query->latest()->paginate(15);
        $patients = Patient::where('practice_id', Auth::user()->practice_id)->orderBy('last_name')->get();
        $encounters = Encounter::where('practice_id', Auth::user()->practice_id)->latest()->limit(50)->get();

        return view('livewire.billing.billing-manager', [
            'invoices' => $invoices,
            'patients' => $patients,
            'encounters' => $encounters,
        ]);
    }
}
