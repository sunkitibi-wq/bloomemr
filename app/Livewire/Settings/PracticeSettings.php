<?php

namespace App\Livewire\Settings;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Practice Settings')]
class PracticeSettings extends Component
{
    public string $practiceName = '';

    public string $timezone = 'America/New_York';

    public string $locale = 'en';

    public function mount(): void
    {
        Gate::allowIf(fn () => Auth::user()->isSuperAdmin());

        $practice = Auth::user()->practice;

        $this->practiceName = $practice->name;
        $this->timezone = $practice->settings['timezone'] ?? 'America/New_York';
        $this->locale = $practice->settings['locale'] ?? 'en';
    }

    public function save(): void
    {
        $this->validate([
            'practiceName' => 'required|string|max:255',
            'timezone' => 'required|string|timezone',
            'locale' => 'required|string|in:en,es,fr,de',
        ]);

        $practice = Auth::user()->practice;
        $practice->name = $this->practiceName;
        $practice->settings = array_merge($practice->settings ?? [], [
            'timezone' => $this->timezone,
            'locale' => $this->locale,
        ]);
        $practice->save();

        Flux::toast(variant: 'success', text: __('Practice settings saved successfully.'));
    }

    public function render(): View
    {
        return view('livewire.settings.practice-settings');
    }
}
