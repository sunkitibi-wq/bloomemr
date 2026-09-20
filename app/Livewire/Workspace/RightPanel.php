<?php

namespace App\Livewire\Workspace;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\WorkspaceNote;
use Illuminate\Support\Collection;
use Livewire\Component;

class RightPanel extends Component
{
    public $chatInput = '';
    public $chatMessages = [];
    
    public $isAddingNote = false;
    public $noteTitle = '';
    public $noteBody = '';

    public function mount()
    {
        $name = auth()->user() ? explode(' ', auth()->user()->name)[0] : 'User';
        $this->chatMessages = [
            ['role' => 'assistant', 'content' => 'Hello ' . $name . ', how can I help you today?']
        ];
    }

    public function sendMessage()
    {
        if (trim($this->chatInput) === '') {
            return;
        }

        $this->chatMessages[] = ['role' => 'user', 'content' => $this->chatInput];
        
        // Stub AI response
        $this->chatMessages[] = [
            'role' => 'assistant', 
            'content' => "I am an AI stub and cannot process \"{$this->chatInput}\" yet, but I've noted your request!"
        ];

        $this->chatInput = '';
    }

    public function triggerSuggestedAction($action)
    {
        $this->chatInput = $action;
        $this->sendMessage();
    }

    public function saveNote()
    {
        $this->validate([
            'noteTitle' => 'required|string|max:255',
            'noteBody' => 'nullable|string',
        ]);

        WorkspaceNote::create([
            'user_id' => auth()->id(),
            'title' => $this->noteTitle,
            'body' => $this->noteBody,
        ]);

        $this->isAddingNote = false;
        $this->noteTitle = '';
        $this->noteBody = '';
    }

    public function deleteNote($id)
    {
        WorkspaceNote::where('id', $id)->where('user_id', auth()->id())->delete();
    }

    public function getNotesProperty()
    {
        return WorkspaceNote::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getPendingAppointmentsCountProperty()
    {
        return Appointment::where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->count();
    }

    public function getUnpaidInvoicesCountProperty()
    {
        return Invoice::whereIn('status', ['draft', 'pending'])->count();
    }

    public function render()
    {
        return view('livewire.workspace.right-panel');
    }
}
