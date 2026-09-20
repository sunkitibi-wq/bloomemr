<?php

namespace App\Livewire\Portal;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
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
        $name = auth('portal')->user() ? explode(' ', auth('portal')->user()->name)[0] : 'User';
        $this->chatMessages = [
            ['role' => 'assistant', 'content' => 'Hello ' . $name . ', have a health or billing question?']
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
            'content' => "I am a Health Assistant stub and cannot process \"{$this->chatInput}\" yet, but I've noted your request!"
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
            'user_id' => auth('portal')->id(),
            'title' => $this->noteTitle,
            'body' => $this->noteBody,
        ]);

        $this->isAddingNote = false;
        $this->noteTitle = '';
        $this->noteBody = '';
    }

    public function deleteNote($id)
    {
        WorkspaceNote::where('id', $id)->where('user_id', auth('portal')->id())->delete();
    }

    public function getNotesProperty()
    {
        return WorkspaceNote::where('user_id', auth('portal')->id())
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getPatientProperty()
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            return Patient::where('portal_user_id', auth('portal')->id())->first();
        }
        
        return null;
    }

    public function getPendingAppointmentsCountProperty()
    {
        if (! $this->patient) {
            return 0;
        }

        return Appointment::where('patient_id', $this->patient->id)
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->count();
    }

    public function getUnpaidInvoicesCountProperty()
    {
        if (! $this->patient) {
            return 0;
        }

        return Invoice::where('patient_id', $this->patient->id)
            ->whereIn('status', ['draft', 'pending'])
            ->count();
    }

    public function render()
    {
        return view('livewire.portal.right-panel');
    }
}
