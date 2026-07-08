<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Models\WebhookSubscription;
use App\Services\WebhookDispatcherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = WebhookSubscription::where('practice_id', auth()->user()->practice_id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => $subscriptions->count(),
            'entry' => $subscriptions->map(fn ($s) => [
                'resourceType' => 'Subscription',
                'id' => (string) $s->id,
                'status' => $s->status,
                'end' => $s->paused_until?->toIso8601String(),
                'channel' => [
                    'type' => 'rest-hook',
                    'endpoint' => $s->endpoint_url,
                    'payload' => 'application/fhir+json',
                ],
                'criteria' => implode(',', $s->events),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'endpoint_url' => 'required|url|max:500',
            'secret' => 'nullable|string|min:16|max:255',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:'.implode(',', WebhookDispatcherService::supportedEvents()),
        ]);

        $subscription = WebhookSubscription::create([
            'practice_id' => auth()->user()->practice_id,
            'name' => $validated['name'],
            'endpoint_url' => $validated['endpoint_url'],
            'secret' => $validated['secret'] ?? null,
            'events' => $validated['events'],
            'status' => 'active',
        ]);

        return response()->json([
            'resourceType' => 'Subscription',
            'id' => (string) $subscription->id,
            'status' => $subscription->status,
            'channel' => [
                'type' => 'rest-hook',
                'endpoint' => $subscription->endpoint_url,
            ],
            'criteria' => implode(',', $subscription->events),
        ], 201);
    }

    public function destroy(WebhookSubscription $subscription): JsonResponse
    {
        $subscription->delete();

        return response()->json(null, 204);
    }
}
