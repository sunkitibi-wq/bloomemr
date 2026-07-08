<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Appointment $appointment) {}

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
            ->subject(__('Upcoming Appointment Reminder — Bloom'))
            ->greeting(__('Hello, :name', ['name' => $notifiable->name]))
            ->line(__('This is a reminder that you have an upcoming appointment.'))
            ->line(__('Date: :date', ['date' => $this->appointment->appointment_date->format('l, F j, Y')]))
            ->line(__('Time: :time', ['time' => $this->appointment->start_time]))
            ->action(__('View Appointments'), url('/portal/appointments'))
            ->line(__('Please contact us if you need to reschedule.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'appointment_date' => $this->appointment->appointment_date->toDateString(),
            'message' => __('Upcoming appointment on :date at :time', [
                'date' => $this->appointment->appointment_date->format('M j, Y'),
                'time' => $this->appointment->start_time,
            ]),
            'action_url' => '/portal/appointments',
        ];
    }
}
