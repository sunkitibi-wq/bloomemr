<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\SecureMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Message Center')]
class MessageCenter extends Component
{
    use WithPagination;

    public $activeTab = 'inbox'; // inbox, sent

    public $selectedMessageId = null;

    // Compose form
    public $showCompose = false;

    public $recipientId = '';

    public $patientId = '';

    public $subject = '';

    public $body = '';

    public function selectMessage(int $id)
    {
        $this->selectedMessageId = $id;
        $message = SecureMessage::findOrFail($id);

        if ($message->recipient_id === Auth::id() && $message->read_at === null) {
            $message->update(['read_at' => now()]);
        }
    }

    public function openCompose()
    {
        $this->resetCompose();
        $this->showCompose = true;
    }

    public function resetCompose()
    {
        $this->recipientId = '';
        $this->patientId = '';
        $this->subject = '';
        $this->body = '';
    }

    public function sendMessage()
    {
        $this->validate([
            'recipientId' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'patientId' => 'nullable|exists:patients,id',
        ]);

        SecureMessage::create([
            'practice_id' => Auth::user()->practice_id,
            'sender_id' => Auth::id(),
            'recipient_id' => $this->recipientId,
            'patient_id' => $this->patientId ?: null,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        $this->showCompose = false;
        $this->resetCompose();
        $this->activeTab = 'sent';
        session()->flash('message', __('Message sent successfully.'));
    }

    public function render()
    {
        $query = SecureMessage::with(['sender', 'recipient', 'patient'])
            ->where('practice_id', Auth::user()->practice_id)
            ->latest();

        if ($this->activeTab === 'inbox') {
            $query->where('recipient_id', Auth::id());
        } else {
            $query->where('sender_id', Auth::id());
        }

        $messages = $query->paginate(20);
        $selectedMessage = $this->selectedMessageId ? SecureMessage::with(['sender', 'recipient', 'patient'])->find($this->selectedMessageId) : null;

        // Potential recipients (staff in same practice)
        $staff = User::where('practice_id', Auth::user()->practice_id)
            ->where('id', '!=', Auth::id())
            ->where('role', '!=', 'guardian')
            ->orderBy('name')
            ->get();

        $patients = Patient::where('practice_id', Auth::user()->practice_id)->orderBy('last_name')->get();

        return view('livewire.message-center', [
            'messages' => $messages,
            'selectedMessage' => $selectedMessage,
            'staff' => $staff,
            'patients' => $patients,
        ]);
    }
}
