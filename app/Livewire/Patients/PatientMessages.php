<?php

namespace App\Livewire\Patients;

use App\Actions\LogAudit;
use App\Models\Patient;
use App\Models\SecureMessage;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PatientMessages extends Component
{
    public Patient $patient;

    public $replyBody = '';

    public ?SecureMessage $activeMessage = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
    }

    public function selectMessage(int $messageId): void
    {
        $message = SecureMessage::where('patient_id', $this->patient->id)
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('recipient_id', Auth::id());
            })
            ->findOrFail($messageId);

        $this->activeMessage = $message;
        $this->replyBody = '';

        if ($message->recipient_id === Auth::id() && is_null($message->read_at)) {
            $message->update(['read_at' => now()]);
        }
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyBody' => 'required|string',
        ]);

        if (! $this->activeMessage) {
            return;
        }

        $recipientId = ($this->activeMessage->sender_id === Auth::id())
            ? $this->activeMessage->recipient_id
            : $this->activeMessage->sender_id;

        $newMsg = SecureMessage::create([
            'practice_id' => $this->patient->practice_id ?? Auth::user()->practice_id,
            'sender_id' => Auth::id(),
            'recipient_id' => $recipientId,
            'patient_id' => $this->patient->id,
            'subject' => 'Re: '.$this->activeMessage->subject,
            'body' => $this->replyBody,
        ]);

        app(LogAudit::class)(
            user: Auth::user(),
            action: 'send_secure_message',
            entityType: 'secure_message',
            entityId: $newMsg->id,
            patientId: $this->patient->id,
        );

        Flux::toast(variant: 'success', text: __('Reply sent successfully.'));
        $this->replyBody = '';

        $this->selectMessage($this->activeMessage->id);
    }

    public function render(): View
    {
        // Get all secure messages relating to this patient involving the authenticated clinician
        $messages = SecureMessage::where('patient_id', $this->patient->id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.patients.patient-messages', [
            'messages' => $messages,
        ]);
    }
}
