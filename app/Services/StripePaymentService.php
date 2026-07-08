<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripePaymentService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret') ?? 'sk_test_mock');
    }

    /**
     * Process Stripe PaymentIntent creation and card payment processing.
     */
    public function processCardPayment(Invoice $invoice, string $cardNumber, string $expMonth, string $expYear, string $cvc): Payment
    {
        // Remove any non-numeric characters from the card number (e.g., spaces, dashes)
        $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);

        try {
            // Create a PaymentMethod with raw card details.
            $paymentMethod = $this->stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => $cardNumber,
                    'exp_month' => $expMonth,
                    'exp_year' => $expYear,
                    'cvc' => $cvc,
                ],
            ]);

            // Create and confirm a PaymentIntent
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => (int) ($invoice->total_amount * 100), // Convert to cents
                'currency' => 'usd',
                'payment_method' => $paymentMethod->id,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'description' => 'Invoice #' . $invoice->id,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'practice_id' => $invoice->practice_id,
                ],
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $last4 = substr($cardNumber, -4);

                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'practice_id' => $invoice->practice_id,
                    'amount' => $invoice->total_amount,
                    'payment_method' => 'credit_card (ending in '.$last4.')',
                    'transaction_reference' => $paymentIntent->id,
                    'paid_at' => now(),
                ]);

                $invoice->update(['status' => 'paid']);

                return $payment;
            }

            throw new \Exception('Payment requires further action or failed. Status: ' . $paymentIntent->status);
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
