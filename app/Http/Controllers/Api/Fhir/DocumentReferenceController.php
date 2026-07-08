<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fhir\DocumentReferenceResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentReferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $documents = Document::query()
            ->when($request->filled('patient'), function ($q) use ($request) {
                $patientId = str_replace('Patient/', '', $request->patient);
                $q->where('patient_id', $patientId);
            })
            ->when($request->filled('type'), fn ($q) => $q->where('category', $request->type))
            ->when($request->filled('_count'), fn ($q) => $q->take(min((int) $request->_count, 100)))
            ->orderBy('id')
            ->get();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => $documents->count(),
            'entry' => DocumentReferenceResource::collection($documents)->toArray($request),
        ]);
    }

    public function show(Document $document): JsonResponse
    {
        return response()->json(new DocumentReferenceResource($document));
    }
}
