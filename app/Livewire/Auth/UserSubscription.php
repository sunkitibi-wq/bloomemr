<?php

namespace App\Livewire\Auth;

use App\Models\Invoice;
use App\Models\Patient;
use App\Services\StripePaymentService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Subscription')]
class UserSubscription extends Component
{
    public string $selectedPlan = '1_month';

    // Mock Stripe Card Input Fields
    public string $cardNumber = '';

    public string $cardExpiry = '';

    public string $cardCvc = '';

    // Organization Contact Sales Form Fields
    public string $orgName = '';

    public string $orgEmail = '';

    public string $orgPhone = '';

    public string $orgSize = '5';

    public bool $isOrgFormSubmitted = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        // If user is already active, redirect to EMR Dashboard
        if ($user->isSubscribed()) {
            $this->redirectRoute('dashboard');

            return;
        }

        // Pre-fill organization email if available
        $this->orgEmail = $user->email;
        if ($user->practice) {
            $this->orgName = $user->practice->name;
        }
    }

    public function processPayment(): void
    {
        $this->validate([
            'selectedPlan' => 'required|in:1_month,6_months,1_year',
            'cardNumber' => 'required|string|min:16|max:19',
            'cardExpiry' => 'required|string|min:5|max:7',
            'cardCvc' => 'required|string|min:3|max:4',
        ]);

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $prices = [
            '1_month' => 99.00,
            '6_months' => 499.00,
            '1_year' => 899.00,
        ];

        $patient = Patient::where('practice_id', $user->practice_id)->first();
        if (! $patient) {
            $patient = Patient::create([
                'practice_id' => $user->practice_id,
                'first_name' => 'System',
                'last_name' => 'Billing',
                'date_of_birth' => '1970-01-01',
                'mrn' => 'SYS-'.rand(100000, 999999),
            ]);
        }

        $invoice = Invoice::create([
            'practice_id' => $user->practice_id,
            'patient_id' => $patient->id,
            'cpt_codes' => [],
            'total_amount' => $prices[$this->selectedPlan],
            'status' => 'unpaid',
            'due_date' => now(),
        ]);

        try {
            $expiryParts = explode('/', $this->cardExpiry);
            $expMonth = trim($expiryParts[0] ?? '');
            $expYear = trim($expiryParts[1] ?? '');
            if (strlen($expYear) === 2) {
                $expYear = '20'.$expYear;
            }

            $paymentService = app(StripePaymentService::class);
            $paymentService->processCardPayment($invoice, $this->cardNumber, $expMonth, $expYear, $this->cardCvc);

            $durations = [
                '1_month' => now()->addMonth(),
                '6_months' => now()->addMonths(6),
                '1_year' => now()->addYear(),
            ];

            $user->subscribed_until = $durations[$this->selectedPlan];
            $user->save();

            Flux::toast(variant: 'success', text: __('Subscription activated successfully via Stripe. Welcome to Bloom.'));

            $this->redirectRoute('dashboard');
        } catch (\Exception $e) {
            $invoice->delete();
            $this->addError('cardNumber', $e->getMessage());
        }
    }

    public function submitOrgRequest(): void
    {
        $this->validate([
            'orgName' => 'required|string|min:2|max:255',
            'orgEmail' => 'required|email',
            'orgPhone' => 'required|string|min:7',
            'orgSize' => 'required|string',
        ]);

        // Simulating the contact form submission
        $this->isOrgFormSubmitted = true;
        Flux::toast(variant: 'success', text: __('Enterprise request submitted. A sales manager will contact you.'));
    }

    public function render(): View
    {
        return view('livewire.auth.user-subscription')
            ->layout('layouts.blank');
    }
}
