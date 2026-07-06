<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;

class StripePaymentService
{
    /**
     * Simulate Stripe PaymentIntent creation and card payment processing.
     *
     * @param Invoice $invoice
     * @param string $cardNumber
     * @return Payment
     */
    public function processCardPayment(Invoice $invoice, string $cardNumber): Payment
    {
        // Simulate checking if card number is test card or passes basic format checks
        $last4 = substr($cardNumber, -4);
        
        $reference = 'ch_' . Str::random(24);
        
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'practice_id' => $invoice->practice_id,
            'amount' => $invoice->total_amount,
            'payment_method' => 'credit_card (ending in ' . $last4 . ')',
            'transaction_reference' => $reference,
            'paid_at' => now(),
        ]);
        
        $invoice->update(['status' => 'paid']);
        
        return $payment;
    }
}
