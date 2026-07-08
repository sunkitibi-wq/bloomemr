<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecureMessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $senderName,
        public readonly string $patientName,
    ) {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New Secure Message — Bloom'))
            ->greeting(__('Hello, :name', ['name' => $notifiable->name]))
            ->line(__('You have received a new secure message regarding a patient.'))
            ->line(__('Please log in to Bloom to read the message. No protected health information is included in this notification.'))
            ->action(__('Log in to Bloom'), url('/portal/messages'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'secure_message_received',
            'sender_name' => $this->senderName,
            'message' => __('You have a new secure message.'),
            'action_url' => '/portal/messages',
        ];
    }
}
