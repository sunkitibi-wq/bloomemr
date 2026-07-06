<?php

namespace App\Livewire\SystemAdmin;

use App\Models\Practice;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('System Admin — Practice Manager')]
class PracticeManager extends Component
{
    public function mount(): void
    {
        Gate::allowIf(fn () => Auth::user()->isSystemAdmin());
    }

    #[Computed]
    public function practices()
    {
        return Practice::withCount(['users', 'patients'])
            ->orderBy('name')
            ->get();
    }

    public function togglePracticeStatus(int $practiceId): void
    {
        $practice = Practice::findOrFail($practiceId);

        if ($practice->is_active) {
            $practice->deactivate();
        } else {
            $practice->activate();
        }
    }

    public function impersonate(int $practiceId): void
    {
        $practice = Practice::findOrFail($practiceId);

        session(['current_practice_id' => $practice->id]);
        app()->instance('current_practice', $practice);

        session()->flash('success', __('Now viewing as :practice.', ['practice' => $practice->name]));

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function stopImpersonating(): void
    {
        session()->forget('current_practice_id');
        app()->instance('current_practice', null);

        session()->flash('success', __('Returned to system admin view.'));

        $this->redirect(route('system.practices'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.system-admin.practice-manager');
    }
}
