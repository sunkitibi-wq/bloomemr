<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Exception;

class PaystackPaymentService
{
    /**
     * Process Paystack charge using raw card details.
     */
    public function processCardPayment(Invoice $invoice, string $cardNumber, string $expMonth, string $expYear, string $cvc): Payment
    {
        $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);
        $secretKey = config('services.paystack.secret') ?? 'sk_test_mock';

        $response = Http::withToken($secretKey)
            ->post('https://api.paystack.co/charge', [
                'email' => $invoice->patient->email ?? 'patient@example.com',
                'amount' => (int) ($invoice->total_amount * 100), // Kobo
                'card' => [
                    'number' => $cardNumber,
                    'cvv' => $cvc,
                    'expiry_month' => $expMonth,
                    'expiry_year' => $expYear,
                ],
            ]);

        $result = $response->json();

        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            $data = $result['data'];
            
            // For a simple server-side implementation, we require immediate success.
            // (Standard Paystack charge might require PIN/OTP which is complex for a raw API call)
            if (isset($data['status']) && $data['status'] === 'success') {
                $last4 = substr($cardNumber, -4);

                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'practice_id' => $invoice->practice_id,
                    'amount' => $invoice->total_amount,
                    'payment_method' => 'paystack_card (ending in '.$last4.')',
                    'transaction_reference' => $data['reference'],
                    'paid_at' => now(),
                ]);

                $invoice->update(['status' => 'paid']);

                return $payment;
            }

            throw new Exception('Paystack requires further action: ' . ($data['message'] ?? $data['status']));
        }

        throw new Exception($result['message'] ?? 'Payment failed or declined by Paystack.');
    }
}
