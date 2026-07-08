<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MirthConnectService
{
    private string $baseUrl;

    private string $apiKey;

    private string $channelId;

    public function __construct()
    {
        $this->baseUrl = config('services.mirth.base_url', 'https://mirth:8443');
        $this->apiKey = config('services.mirth.api_key', '');
        $this->channelId = config('services.mirth.channel_id', '');
    }

    /**
     * Send an HL7 message to the configured Mirth Connect channel.
     */
    public function sendMessage(string $hl7Payload): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->timeout(60)->post("{$this->baseUrl}/api/channels/{$this->channelId}/messages", [
            'message' => $hl7Payload,
        ]);

        if ($response->failed()) {
            Log::error('Mirth Connect send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        }

        return ['success' => true, 'messageId' => $response->json('messageId')];
    }

    /**
     * Get the status of a previously sent message.
     */
    public function getMessageStatus(string $messageId): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->timeout(15)->get("{$this->baseUrl}/api/channels/{$this->channelId}/messages/{$messageId}");

        if ($response->failed()) {
            return ['success' => false, 'error' => $response->body()];
        }

        return ['success' => true, 'status' => $response->json('status')];
    }

    /**
     * Check Mirth Connect server health.
     */
    public function healthCheck(): array
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->timeout(10)->get("{$this->baseUrl}/api/server/status");

        if ($response->failed()) {
            return ['connected' => false, 'error' => $response->body()];
        }

        return ['connected' => true, 'status' => $response->json()];
    }
}
