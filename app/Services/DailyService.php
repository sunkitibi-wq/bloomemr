<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.daily.co/v1';

    public function __construct()
    {
        $this->apiKey = config('services.daily.key') ?? '';
    }

    /**
     * Get or create a private Daily.co room for a patient consultation.
     *
     * @param string $roomName
     * @return array<string, mixed>|null
     */
    public function getOrCreateRoom(string $roomName): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Daily API key is not configured.');

            return null;
        }

        // 1. Try to retrieve the room configuration
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/rooms/{$roomName}");

        if ($response->successful()) {
            return $response->json();
        }

        // 2. If the room was not found, create a new private room
        if ($response->status() === 404) {
            $createResponse = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/rooms", [
                    'name' => $roomName,
                    'properties' => [
                        'privacy' => 'private',
                        'enable_chat' => true,
                    ],
                ]);

            if ($createResponse->successful()) {
                return $createResponse->json();
            }

            Log::error('Daily Room Creation Failed: '.$createResponse->body());
        } else {
            Log::error('Daily Room Retrieval Failed: '.$response->body());
        }

        return null;
    }

    /**
     * Generate an access/meeting token for a private Daily.co room.
     *
     * @param string $roomName
     * @param string $userName
     * @param bool $isOwner
     * @return string|null
     */
    public function createMeetingToken(string $roomName, string $userName, bool $isOwner = false): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('Daily API key is not configured.');

            return null;
        }

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/meeting-tokens", [
                'properties' => [
                    'room_name' => $roomName,
                    'user_name' => $userName,
                    'is_owner' => $isOwner,
                ],
            ]);

        if ($response->successful()) {
            return $response->json('token');
        }

        Log::error('Daily Meeting Token Creation Failed: '.$response->body());

        return null;
    }
}
