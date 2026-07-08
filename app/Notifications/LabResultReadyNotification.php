<?php

namespace App\Notifications;

use App\Models\LabResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LabResultReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly LabResult $labResult) {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $criticality = $this->labResult->is_critical ? 'CRITICAL — ' : '';

        return (new MailMessage)
            ->subject(__(':critical New Lab Result Available — Bloom', ['critical' => $criticality]))
            ->greeting(__('Hello, :name', ['name' => $notifiable->name]))
            ->line($this->labResult->is_critical
                ? __('A critical lab result has been received and requires your immediate attention.')
                : __('A new lab result is available for your review in Bloom.')
            )
            ->action(__('Review Lab Results'), url('/patients/'.$this->labResult->patient_id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->labResult->is_critical ? 'lab_result_critical' : 'lab_result_ready',
            'lab_result_id' => $this->labResult->id,
            'patient_id' => $this->labResult->patient_id,
            'is_critical' => $this->labResult->is_critical,
            'message' => $this->labResult->is_critical
                ? __('CRITICAL lab result received — immediate review required.')
                : __('New lab result is available for review.'),
            'action_url' => '/patients/'.$this->labResult->patient_id,
        ];
    }
}
