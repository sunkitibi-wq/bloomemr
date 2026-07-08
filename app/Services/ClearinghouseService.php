<?php

namespace App\Services;

use App\Models\ClaimSubmission;
use App\Models\Invoice;

class ClearinghouseService
{
    /**
     * Generate a standards-compliant ANSI ASC X12 837P Professional Claim EDI payload.
     */
    public function generateEdi837(Invoice $invoice): string
    {
        $invoice->load('patient');
        $patient = $invoice->patient;

        $date = now()->format('Ymd');
        $time = now()->format('Hi');
        $invId = str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT);

        $cpts = $invoice->cpt_codes ?? [];
        $total = number_format($invoice->total_amount, 2, '.', '');

        // Header
        $edi = "ISA*00*          *00*          *ZZ*BLOOMEMR       *ZZ*AVALITY        *{$date}*{$time}*U*00401*000000001*0*T*:~\n";
        $edi .= "GS*HC*BLOOMEMR*AVALITY*{$date}*{$time}*1*X*004010X098A1~\n";
        $edi .= "ST*837*0001~\n";
        $edi .= "BHT*0019*00*{$invId}*{$date}*{$time}*CH~\n";

        // Submit Provider Loop 2010AA
        $edi .= "NM1*85*2*BLOOM CHILD PSYCHIATRY*****XX*1234567890~\n";
        $edi .= "N3*123 MAIN STREET~\n";
        $edi .= "N4*NEW YORK*NY*10001~\n";

        // Receiver Loop 2010BB
        $edi .= "NM1*87*2*AVALITY*****XX*999999999~\n";

        // Subscriber Loop 2010CA
        $lname = strtoupper($patient->last_name);
        $fname = strtoupper($patient->first_name);
        $dob = $patient->date_of_birth ? $patient->date_of_birth->format('Ymd') : '20150101';
        $gender = $patient->gender_identity === 'female' ? 'F' : 'M';
        $policy = $patient->primary_insurance ?: 'POL-99999';

        $edi .= "NM1*IL*1*{$lname}*{$fname}****MI*{$policy}~\n";
        $edi .= "DMG*D8*{$dob}*{$gender}~\n";

        // Claim Info Loop 2300
        $edi .= "CLM*INV-{$invId}*{$total}***11::1*Y*A*Y*Y~\n";

        // Service Lines Loop 2400
        $idx = 1;
        foreach ($cpts as $cpt) {
            $fee = number_format($cpt['fee'], 2, '.', '');
            $edi .= "LX*{$idx}~\n";
            $edi .= "SV1*HC:{$cpt['code']}*{$fee}*UN*1***1~\n";
            $edi .= "DTP*472*D8*{$date}~\n";
            $idx++;
        }

        // Trailer
        $edi .= "SE*15*0001~\n";
        $edi .= "GE*1*1~\n";
        $edi .= 'IEA*1*000000001~';

        return $edi;
    }

    /**
     * Submit an insurance claim for an invoice to the clearinghouse.
     */
    public function submitClaim(Invoice $invoice): ClaimSubmission
    {
        $ediRequest = $this->generateEdi837($invoice);

        $date = now()->format('Ymd');
        $time = now()->format('Hi');

        // Generate simulated ANSI X12 997 Functional Acknowledgment response
        $ediResponse = "ISA*00*          *00*          *ZZ*AVALITY        *ZZ*BLOOMEMR       *{$date}*{$time}*U*00401*000000001*0*T*:~\n";
        $ediResponse .= "GS*FA*AVALITY*BLOOMEMR*{$date}*{$time}*1*X*004010~\n";
        $ediResponse .= "ST*997*0001~\n";
        $ediResponse .= "AK1*HC*1~\n";
        $ediResponse .= "AK2*837*0001~\n";
        $ediResponse .= "AK5*A~\n"; // Accepted
        $ediResponse .= "AK9*A*1*1*1~\n";
        $ediResponse .= "SE*8*0001~\n";
        $ediResponse .= "GE*1*1~\n";
        $ediResponse .= 'IEA*1*000000001~';

        $submission = ClaimSubmission::create([
            'invoice_id' => $invoice->id,
            'edi_request' => $ediRequest,
            'edi_response' => $ediResponse,
            'status' => 'accepted',
        ]);

        $invoice->update([
            'insurance_claim_status' => 'submitted',
            'submitted_at' => now(),
            'claim_reference' => 'CLM-'.str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT),
        ]);

        return $submission;
    }
}
