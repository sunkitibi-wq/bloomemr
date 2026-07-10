<?php

namespace App\Livewire\Portal;

use App\Models\Invoice;
use App\Models\Patient;
use App\Services\PaystackPaymentService;
use App\Services\StripePaymentService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Billing')]
class PortalBilling extends Component
{
    public ?Patient $patient = null;

    // View Invoice Detail Modal
    public ?Invoice $selectedInvoice = null;

    public bool $isDetailOpen = false;

    // Payment Modal
    public bool $isPaymentModalOpen = false;

    public ?Invoice $invoiceToPay = null;

    public string $cardNumber = '';

    public string $cardExpiry = '';

    public string $cardCvc = '';

    public string $paymentGateway = 'stripe';

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }
    }

    public function mockPayment(Invoice $invoice): void
    {
        if ($invoice->status !== 'paid') {
            $invoice->update(['status' => 'paid']);
            session()->flash('message', __('Payment processed successfully (Mock Transaction).'));
            if ($this->selectedInvoice && $this->selectedInvoice->id === $invoice->id) {
                $this->selectedInvoice->refresh();
            }
        }
    }

    public function viewDetails(Invoice $invoice): void
    {
        $this->selectedInvoice = $invoice;
        $this->isDetailOpen = true;
    }

    public function openPaymentModal(Invoice $invoice): void
    {
        $this->invoiceToPay = $invoice;
        $this->cardNumber = '';
        $this->cardExpiry = '';
        $this->cardCvc = '';
        $this->paymentGateway = 'stripe';
        $this->isPaymentModalOpen = true;
        $this->isDetailOpen = false;
    }

    public function processPayment(): void
    {
        $this->validate([
            'cardNumber' => 'required|string|min:16',
            'cardExpiry' => 'required|string|min:5',
            'cardCvc' => 'required|string|min:3',
            'paymentGateway' => 'required|in:stripe,paystack',
        ]);

        if ($this->invoiceToPay && $this->invoiceToPay->status !== 'paid') {
            try {
                $service = $this->paymentGateway === 'paystack'
                    ? app(PaystackPaymentService::class)
                    : app(StripePaymentService::class);

                $parts = explode('/', $this->cardExpiry);
                if (count($parts) !== 2) {
                    throw new \Exception('Invalid expiration date format. Use MM/YY.');
                }
                [$expMonth, $expYear] = $parts;

                $service->processCardPayment($this->invoiceToPay, $this->cardNumber, trim($expMonth), trim($expYear), $this->cardCvc);

                $gatewayName = ucfirst($this->paymentGateway);
                session()->flash('message', __("Payment processed successfully via {$gatewayName}."));

                if ($this->selectedInvoice && $this->selectedInvoice->id === $this->invoiceToPay->id) {
                    $this->selectedInvoice->refresh();
                }

                $this->isPaymentModalOpen = false;
            } catch (\Exception $e) {
                $this->addError('payment_error', $e->getMessage());
            }
        }
    }

    public function render(): View
    {
        $invoices = collect();

        if ($this->patient) {
            $invoices = Invoice::where('patient_id', $this->patient->id)
                ->latest()
                ->get();
        }

        return view('livewire.portal.portal-billing', [
            'invoices' => $invoices,
        ])->layout('layouts.blank');
    }
}
