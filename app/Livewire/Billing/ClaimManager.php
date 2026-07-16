<?php

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Models\ClaimSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Claim Manager')]
class ClaimManager extends Component
{
    public Invoice $invoice;
    public ?ClaimSubmission $claim = null;

    public function mount(Invoice $invoice)
    {
        abort_unless($invoice->practice_id === Auth::user()->practice_id, 403);
        $this->invoice = $invoice;
        $this->claim = $invoice->claimSubmissions()->latest()->first();

        if (!$this->claim) {
            $this->claim = $invoice->claimSubmissions()->create([
                'status' => 'draft',
                'edi_request' => $this->generateEdiPreview($invoice),
            ]);
        }
    }

    private function generateEdiPreview(Invoice $invoice): string
    {
        $patient = $invoice->patient;
        $date = now()->format('Ymd');
        $amount = number_format($invoice->total_amount, 2, '', '');
        return "ISA*00*          *00*          *ZZ*SUBMITTER      *ZZ*RECEIVER       *{$date}*1200*^*00501*000000001*0*T*:~\nGS*HC*SUBMITTER*RECEIVER*{$date}*1200*1*X*005010X222A1~\nST*837*0001*005010X222A1~\nBHT*0019*00*0123*20231010*1023*CH~\nNM1*85*2*BLOOM CLINIC*****XX*1234567890~\nNM1*87*2*PAYER*****PI*98765~\nHL*1**20*1~\nNM1*IL*1*{$patient->last_name}*{$patient->first_name}****MI*{$patient->mrn}~\nCLM*{$invoice->id}*{$amount}***11:B:1*Y*A*Y*I~\nSE*9*0001~\nGE*1*1~\nIEA*1*000000001~";
    }

    public function submitClaim()
    {
        $this->claim->update([
            'status' => 'submitted',
        ]);

        $this->invoice->update([
            'insurance_claim_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Mock clearinghouse response
        $this->claim->update([
            'edi_response' => "ISA*00*          *00*          *ZZ*RECEIVER       *ZZ*SUBMITTER      *".now()->format('Ymd')."*1200*^*00501*000000001*0*T*:\nTA1*000000001*231010*1200*A*000~",
        ]);

        session()->flash('message', 'Claim successfully submitted.');
    }

    public function render()
    {
        return view('livewire.billing.claim-manager');
    }
}
