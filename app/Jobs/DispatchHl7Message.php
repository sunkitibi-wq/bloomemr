<?php

namespace App\Jobs;

use App\Models\Practice;
use App\Services\Hl7IntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchHl7Message implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Practice $practice,
        public string $hl7Payload,
        public string $endpoint,
        public ?string $authToken = null,
    ) {}

    public function handle(Hl7IntegrationService $hl7): void
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/xml',
        ];

        if ($this->authToken) {
            $headers['Authorization'] = 'Bearer '.$this->authToken;
        }

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->send('POST', $this->endpoint, [
                'body' => http_build_query(['hl7' => $this->hl7Payload]),
            ]);

        if ($response->failed()) {
            Log::error('HL7 dispatch failed', [
                'practice_id' => $this->practice->id,
                'endpoint' => $this->endpoint,
                'status' => $response->status(),
            ]);

            $this->release(30);
        }

        Log::info('HL7 message dispatched successfully', [
            'practice_id' => $this->practice->id,
            'endpoint' => $this->endpoint,
        ]);
    }
}
