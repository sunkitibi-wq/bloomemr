<?php

namespace App\Livewire\SystemAdmin;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('System Admin — Dashboard')]
#[Layout('components.layouts.system')]
class SystemDashboard extends Component
{
    public function mount(): void
    {
        Gate::allowIf(fn () => Auth::user()->isSystemAdmin());
    }

    public function render(): View
    {
        $stats = [
            'total_practices' => Practice::count(),
            'active_practices' => Practice::where('is_active', true)->count(),
            'total_users' => User::count(),
            'pending_kyc' => User::where('kyc_status', 'pending')->count(),
        ];

        return view('livewire.system-admin.system-dashboard', [
            'stats' => $stats,
        ]);
    }
}
