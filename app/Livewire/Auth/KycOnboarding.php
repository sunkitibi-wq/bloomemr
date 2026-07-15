<?php

namespace App\Livewire\Auth;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Clinical Identity Verification')]
class KycOnboarding extends Component
{
    public int $step = 1;

    // Step 1: Personal Details
    public string $legal_first_name = '';

    public string $legal_last_name = '';

    public string $date_of_birth = '';

    public string $selected_role = '';

    // Step 2: Professional Credentials
    public string $license_number = '';

    public string $license_state = '';

    public string $npi_number = '';

    // Step 3: Document Upload
    public string $document_name = '';

    public bool $document_uploaded = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        if ($user->kyc_status === 'approved') {
            $this->redirectRoute('dashboard');

            return;
        }

        if ($user->kyc_status === 'pending') {
            $this->step = 4;
        } elseif ($user->kyc_status === 'rejected') {
            $this->step = 1;
        } else {
            // Pre-fill from user profile
            $nameParts = explode(' ', $user->name, 2);
            $this->legal_first_name = $nameParts[0] ?? '';
            $this->legal_last_name = $nameParts[1] ?? '';
            $this->selected_role = $user->role ?: 'attending';
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'legal_first_name' => 'required|string|min:2|max:100',
                'legal_last_name' => 'required|string|min:2|max:100',
                'date_of_birth' => 'required|date|before:today',
                'selected_role' => 'required|string|in:attending,resident,clinical_staff,receptionist,laboratory_officer,pharmacist,billing_admin,medical_records_officer,radiologist,insurance_officer,public_health_officer,hospital_administrator',
            ]);
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->validate([
                'license_number' => 'required|string|min:4|max:50',
                'license_state' => 'required|string|size:2',
                'npi_number' => 'nullable|string|numeric|digits:10',
            ]);
            $this->step = 3;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1 && $this->step < 4) {
            $this->step--;
        }
    }

    public function uploadMockDocument(): void
    {
        $this->document_name = 'clinical_id_'.uniqid().'.pdf';
        $this->document_uploaded = true;
        Flux::toast(variant: 'success', text: __('Document uploaded successfully (Mock).'));
    }

    public function submitKyc(): void
    {
        if (! $this->document_uploaded) {
            $this->addError('document', __('Please upload a verification document.'));

            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->kyc_status = 'pending';
        $user->kyc_rejection_reason = null;
        $user->kyc_data = [
            'legal_first_name' => $this->legal_first_name,
            'legal_last_name' => $this->legal_last_name,
            'date_of_birth' => $this->date_of_birth,
            'role' => $this->selected_role,
            'license_number' => $this->license_number,
            'license_state' => $this->license_state,
            'npi_number' => $this->npi_number,
            'document_name' => $this->document_name,
            'submitted_at' => now()->toIso8601String(),
        ];

        // Also update EMR role if clinician
        if ($this->selected_role) {
            $user->role = $this->selected_role;
        }

        $user->save();

        $this->step = 4;
        Flux::toast(variant: 'success', text: __('Identity verification details submitted successfully.'));
    }

    public function restartKyc(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->kyc_status = 'unsubmitted';
            $user->save();
        }

        $this->step = 1;
        $this->document_uploaded = false;
        $this->document_name = '';
    }

    public function render(): View
    {
        return view('livewire.auth.kyc-onboarding')
            ->layout('layouts.blank');
    }
}
