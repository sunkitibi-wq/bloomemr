<?php

namespace App\Jobs;

use App\Models\WebhookSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhook implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WebhookSubscription $subscription,
        public string $event,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $signature = $this->subscription->secret
            ? hash_hmac('sha256', json_encode($this->payload), $this->subscription->secret)
            : null;

        $headers = [
            'Content-Type' => 'application/json',
            'X-Webhook-Event' => $this->event,
            'X-Webhook-Delivery' => (string) str()->uuid(),
        ];

        if ($signature) {
            $headers['X-Webhook-Signature'] = $signature;
        }

        $response = Http::timeout(10)
            ->withHeaders($headers)
            ->post($this->subscription->endpoint_url, $this->payload);

        if ($response->successful()) {
            $this->subscription->markSent();

            Log::info('Webhook dispatched', [
                'subscription' => $this->subscription->id,
                'event' => $this->event,
                'endpoint' => $this->subscription->endpoint_url,
            ]);
        } else {
            $this->subscription->markFailed();

            Log::warning('Webhook dispatch failed', [
                'subscription' => $this->subscription->id,
                'event' => $this->event,
                'status' => $response->status(),
            ]);

            if ($this->subscription->status === 'active') {
                $this->release(30);
            }
        }
    }
}
