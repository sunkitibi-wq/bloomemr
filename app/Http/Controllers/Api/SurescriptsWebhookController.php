<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SurescriptsWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Surescripts.
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Surescripts Webhook Received', $request->all());

        // Basic verification (e.g., check signature or token)
        $token = $request->header('Authorization');
        if (! $token || $token !== 'Bearer '.config('services.surescripts.secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $eventType = $request->input('event_type');
        $prescriptionId = $request->input('prescription_id');
        $status = $request->input('status');

        if ($prescriptionId && $status) {
            $prescription = Prescription::find($prescriptionId);
            if ($prescription) {
                // Map Surescripts status to our internal status
                // e.g. "Verified", "Error", "Filled"
                $prescription->update(['status' => $status]);

                return response()->json(['message' => 'Prescription updated successfully']);
            }
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }
}
