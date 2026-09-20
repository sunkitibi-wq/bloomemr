<?php

namespace App\Livewire\Workspace;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Shared Content')]
#[Layout('components.layouts.app.workspace')]
class SharedContent extends Component
{
    public function render()
    {
        return view('livewire.workspace.shared-content');
    }
}
