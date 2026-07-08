<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fhir\EncounterResource;
use App\Models\Encounter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EncounterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $encounters = Encounter::query()
            ->when($request->filled('patient'), function ($q) use ($request) {
                $patientId = str_replace('Patient/', '', $request->patient);
                $q->where('patient_id', $patientId);
            })
            ->when($request->filled('date'), fn ($q) => $q->whereDate('encounter_date', $request->date))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('_count'), fn ($q) => $q->take(min((int) $request->_count, 100)))
            ->orderBy('id')
            ->get();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => $encounters->count(),
            'entry' => EncounterResource::collection($encounters)->toArray($request),
        ]);
    }

    public function show(Encounter $encounter): JsonResponse
    {
        return response()->json(new EncounterResource($encounter));
    }
}
