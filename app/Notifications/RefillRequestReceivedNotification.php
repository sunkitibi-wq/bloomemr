<?php

namespace App\Notifications;

use App\Models\RefillRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RefillRequestReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly RefillRequest $refillRequest) {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'refill_request_received',
            'refill_request_id' => $this->refillRequest->id,
            'patient_id' => $this->refillRequest->patient_id,
            'message' => __('A new refill request has been submitted via the patient portal.'),
            'action_url' => '/patients/'.$this->refillRequest->patient_id,
        ];
    }
}
