<?php

namespace App\Livewire\Workspace;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Help Center')]
#[Layout('components.layouts.app.workspace')]
class HelpCenter extends Component
{
    public function render()
    {
        return view('livewire.workspace.help-center');
    }
}
