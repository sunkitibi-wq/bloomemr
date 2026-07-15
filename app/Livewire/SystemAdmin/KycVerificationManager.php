<?php

namespace App\Livewire\SystemAdmin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('System Admin — KYC Verification Manager')]
class KycVerificationManager extends Component
{
    public string $activeTab = 'pending';

    public array $rejectionReasons = [];

    public function mount(): void
    {
        Gate::allowIf(fn () => Auth::user()->isSystemAdmin());
    }

    #[Computed]
    public function pendingUsers()
    {
        return User::where('kyc_status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    #[Computed]
    public function historyUsers()
    {
        return User::whereIn('kyc_status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function approveKyc(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->kyc_status = 'approved';
        $user->kyc_rejection_reason = null;
        $user->save();

        Flux::toast(variant: 'success', text: __('KYC verification for :name has been approved.', ['name' => $user->name]));
    }

    public function rejectKyc(int $userId): void
    {
        $reason = trim($this->rejectionReasons[$userId] ?? '');
        if (empty($reason)) {
            $this->addError("rejectionReasons.{$userId}", __('A rejection reason is required.'));

            return;
        }

        $user = User::findOrFail($userId);
        $user->kyc_status = 'rejected';
        $user->kyc_rejection_reason = $reason;
        $user->save();

        unset($this->rejectionReasons[$userId]);
        Flux::toast(variant: 'success', text: __('KYC verification for :name has been rejected.', ['name' => $user->name]));
    }

    public function render(): View
    {
        return view('livewire.system-admin.kyc-verification-manager');
    }
}
