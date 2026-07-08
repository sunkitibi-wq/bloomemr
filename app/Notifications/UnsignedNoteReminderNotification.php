<?php

namespace App\Notifications;

use App\Models\Encounter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UnsignedNoteReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Encounter $encounter) {}

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
            'type' => 'unsigned_note_reminder',
            'encounter_id' => $this->encounter->id,
            'patient_id' => $this->encounter->patient_id,
            'message' => __('You have an unsigned clinical note from :date that requires your attention.', [
                'date' => $this->encounter->encounter_date->format('M j, Y'),
            ]),
            'action_url' => '/encounters/'.$this->encounter->id.'/note',
        ];
    }
}
