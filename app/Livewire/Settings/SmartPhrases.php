<?php

namespace App\Livewire\Settings;

use App\Models\SmartPhrase;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Smart Phrases settings')]
class SmartPhrases extends Component
{
    public string $trigger = '';

    public string $expansion = '';

    public string $category = '';

    public bool $is_global = false;

    public ?SmartPhrase $editingPhrase = null;

    public bool $showForm = false;

    protected $rules = [
        'trigger' => 'required|string|alpha_num|max:50',
        'expansion' => 'required|string|max:5000',
        'category' => 'nullable|string|max:50',
        'is_global' => 'boolean',
    ];

    #[Computed]
    public function phrases()
    {
        return SmartPhrase::query()
            ->where(function ($q) {
                $q->where('is_global', true)
                    ->orWhere('owner_id', Auth::id());
            })
            ->latest()
            ->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(SmartPhrase $phrase): void
    {
        if ($phrase->is_global && ! Auth::user()->isSuperAdmin()) {
            Flux::toast(variant: 'danger', text: 'You do not have permission to edit global phrases.');

            return;
        }

        if (! $phrase->is_global && $phrase->owner_id !== Auth::id()) {
            Flux::toast(variant: 'danger', text: 'You do not have permission to edit this phrase.');

            return;
        }

        $this->editingPhrase = $phrase;
        $this->trigger = $phrase->trigger;
        $this->expansion = $phrase->expansion;
        $this->category = $phrase->category ?? '';
        $this->is_global = $phrase->is_global;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'trigger' => $this->trigger,
            'expansion' => $this->expansion,
            'category' => $this->category ?: null,
            'is_global' => Auth::user()->isSuperAdmin() ? $this->is_global : false,
            'owner_id' => $this->is_global ? null : Auth::id(),
        ];

        if ($this->editingPhrase) {
            $this->editingPhrase->update($data);
            Flux::toast(variant: 'success', text: 'Smart Phrase updated.');
        } else {
            SmartPhrase::create($data);
            Flux::toast(variant: 'success', text: 'Smart Phrase created.');
        }

        $this->resetForm();
    }

    public function delete(SmartPhrase $phrase): void
    {
        if ($phrase->is_global && ! Auth::user()->isSuperAdmin()) {
            Flux::toast(variant: 'danger', text: 'You do not have permission to delete global phrases.');

            return;
        }

        if (! $phrase->is_global && $phrase->owner_id !== Auth::id()) {
            Flux::toast(variant: 'danger', text: 'You do not have permission to delete this phrase.');

            return;
        }

        $phrase->delete();
        Flux::toast(variant: 'success', text: 'Smart Phrase deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingPhrase = null;
        $this->trigger = '';
        $this->expansion = '';
        $this->category = '';
        $this->is_global = false;
        $this->showForm = false;
    }

    public function render(): View
    {
        return view('livewire.settings.smart-phrases');
    }
}
