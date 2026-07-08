<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fhir\ObservationResource;
use App\Models\LabResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $results = LabResult::query()
            ->when($request->filled('patient'), function ($q) use ($request) {
                $patientId = str_replace('Patient/', '', $request->patient);
                $q->where('patient_id', $patientId);
            })
            ->when($request->filled('date'), fn ($q) => $q->whereDate('result_date', $request->date))
            ->when($request->filled('code'), fn ($q) => $q->where('test_name', 'like', '%'.$request->code.'%'))
            ->when($request->filled('_count'), fn ($q) => $q->take(min((int) $request->_count, 100)))
            ->orderBy('id')
            ->get();

        $observations = ObservationResource::collection($results)->toArray($request);
        $flattened = collect($observations)->flatten(1)->all();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => count($flattened),
            'entry' => $flattened,
        ]);
    }
}
