<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fhir\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patients = Patient::query()
            ->when($request->filled('identifier'), fn ($q) => $q->where('mrn', $request->identifier))
            ->when($request->filled('family'), fn ($q) => $q->where('last_name', 'like', $request->family.'%'))
            ->when($request->filled('given'), fn ($q) => $q->where('first_name', 'like', $request->given.'%'))
            ->when($request->filled('birthdate'), fn ($q) => $q->whereDate('date_of_birth', $request->birthdate))
            ->when($request->filled('phone'), fn ($q) => $q->where('phone', $request->phone))
            ->when($request->filled('email'), fn ($q) => $q->where('email', $request->email))
            ->when($request->filled('_count'), fn ($q) => $q->take(min((int) $request->_count, 100)))
            ->orderBy('id')
            ->get();

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => $patients->count(),
            'entry' => PatientResource::collection($patients)->toArray($request),
        ]);
    }

    public function show(Patient $patient): JsonResponse
    {
        return response()->json(new PatientResource($patient));
    }

    public function store(Request $request): JsonResponse
    {
        // Minimal FHIR parsing for demonstration
        $patient = Patient::create([
            'practice_id' => $request->user()->practice_id ?? 1,
            'mrn' => $request->input('identifier.0.value'),
            'first_name' => $request->input('name.0.given.0'),
            'last_name' => $request->input('name.0.family'),
            'date_of_birth' => $request->input('birthDate'),
            'gender_identity' => $request->input('gender'),
            'phone' => $request->input('telecom.0.value'),
        ]);

        return response()->json(new PatientResource($patient), 201);
    }

    public function update(Request $request, Patient $patient): JsonResponse
    {
        $patient->update([
            'mrn' => $request->input('identifier.0.value', $patient->mrn),
            'first_name' => $request->input('name.0.given.0', $patient->first_name),
            'last_name' => $request->input('name.0.family', $patient->last_name),
            'date_of_birth' => $request->input('birthDate', $patient->date_of_birth),
            'gender_identity' => $request->input('gender', $patient->gender_identity),
            'phone' => $request->input('telecom.0.value', $patient->phone),
        ]);

        return response()->json(new PatientResource($patient));
    }
}
