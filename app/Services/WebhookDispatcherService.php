<?php

namespace App\Services;

use App\Jobs\DispatchWebhook;
use App\Models\WebhookSubscription;
use Illuminate\Support\Facades\Log;

class WebhookDispatcherService
{
    /**
     * Dispatch an event to all active webhook subscriptions that subscribe to it.
     */
    public function dispatch(string $event, array $payload, ?int $practiceId = null): void
    {
        $query = WebhookSubscription::active()->whereJsonContains('events', $event);

        if ($practiceId) {
            $query->where('practice_id', $practiceId);
        }

        $subscriptions = $query->cursor();

        foreach ($subscriptions as $subscription) {
            try {
                DispatchWebhook::dispatch($subscription, $event, $payload);
            } catch (\Throwable $e) {
                Log::error('Failed to queue webhook', [
                    'subscription' => $subscription->id,
                    'event' => $event,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * List of supported webhook event types.
     */
    public static function supportedEvents(): array
    {
        return [
            'patient.created',
            'patient.updated',
            'encounter.created',
            'encounter.updated',
            'encounter.signed',
            'lab.result.received',
            'lab.result.critical',
            'medication.prescribed',
            'medication.dispensed',
            'document.uploaded',
            'appointment.created',
            'appointment.updated',
        ];
    }
}
