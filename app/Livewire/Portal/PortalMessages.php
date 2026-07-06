<?php

namespace App\Livewire\Portal;

use App\Models\Patient;
use App\Models\SecureMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Secure Messages')]
class PortalMessages extends Component
{
    public ?Patient $patient = null;

    // Send New Message Form
    public $subject = '';

    public $body = '';

    public $providerId = '';

    public bool $isNewFormOpen = false;

    public ?SecureMessage $activeMessage = null;

    public $replyBody = '';

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)->first();
        }

        if ($this->patient) {
            $this->providerId = $this->patient->primary_provider_id ?? User::where('practice_id', $this->patient->practice_id)->first()?->id ?? '';
        }
    }

    public function selectMessage(int $messageId): void
    {
        $message = SecureMessage::where('patient_id', $this->patient->id)
            ->where(function ($query) {
                $query->where('sender_id', auth('portal')->id())
                    ->orWhere('recipient_id', auth('portal')->id());
            })
            ->findOrFail($messageId);

        $this->activeMessage = $message;
        $this->isNewFormOpen = false;
        $this->replyBody = '';

        // Mark as read if receiving and unread
        if ($message->recipient_id === auth('portal')->id() && is_null($message->read_at)) {
            $message->update(['read_at' => now()]);
        }
    }

    public function openNewForm(): void
    {
        $this->subject = '';
        $this->body = '';
        $this->isNewFormOpen = true;
        $this->activeMessage = null;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'providerId' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        SecureMessage::create([
            'practice_id' => $this->patient->practice_id,
            'sender_id' => auth('portal')->id(),
            'recipient_id' => $this->providerId,
            'patient_id' => $this->patient->id,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        session()->flash('message', __('Message sent successfully.'));
        $this->isNewFormOpen = false;
        $this->subject = '';
        $this->body = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyBody' => 'required|string',
        ]);

        $recipientId = ($this->activeMessage->sender_id === auth('portal')->id())
            ? $this->activeMessage->recipient_id
            : $this->activeMessage->sender_id;

        SecureMessage::create([
            'practice_id' => $this->patient->practice_id,
            'sender_id' => auth('portal')->id(),
            'recipient_id' => $recipientId,
            'patient_id' => $this->patient->id,
            'subject' => 'Re: '.$this->activeMessage->subject,
            'body' => $this->replyBody,
        ]);

        session()->flash('message', __('Reply sent successfully.'));
        $this->replyBody = '';

        // Refresh active message thread
        $this->selectMessage($this->activeMessage->id);
    }

    public function render(): View
    {
        $messages = collect();
        $providers = collect();

        if ($this->patient) {
            // Get root messages (for simplicity we group threads by subject, or show as distinct conversations)
            $messages = SecureMessage::where('patient_id', $this->patient->id)
                ->where(function ($query) {
                    $query->where('sender_id', auth('portal')->id())
                        ->orWhere('recipient_id', auth('portal')->id());
                })
                ->with(['sender', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->get();

            $providers = User::where('practice_id', $this->patient->practice_id)
                ->whereIn('role', ['attending', 'super_admin'])
                ->get();
        }

        return view('livewire.portal.portal-messages', [
            'messages' => $messages,
            'providers' => $providers,
        ])->layout('layouts.blank');
    }
}
