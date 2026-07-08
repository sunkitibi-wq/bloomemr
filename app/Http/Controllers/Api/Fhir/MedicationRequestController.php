<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fhir\MedicationRequestResource;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $medications = Medication::query()
            ->when($request->filled('patient'), function ($q) use ($request) {
                $patientId = str_replace('Patient/', '', $request->patient);
                $q->where('patient_id', $patientId);
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('_count'), fn ($q) => $q->take(min((int) $request->_count, 100)))
            ->orderBy('id')
            ->get();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => $medications->count(),
            'entry' => MedicationRequestResource::collection($medications)->toArray($request),
        ]);
    }

    public function show(Medication $medication): JsonResponse
    {
        return response()->json(new MedicationRequestResource($medication));
    }
}
