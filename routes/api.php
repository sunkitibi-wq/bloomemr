<?php

use App\Http\Controllers\Api\Fhir\DocumentReferenceController;
use App\Http\Controllers\Api\Fhir\EncounterController;
use App\Http\Controllers\Api\Fhir\MedicationRequestController;
use App\Http\Controllers\Api\Fhir\MetadataController;
use App\Http\Controllers\Api\Fhir\ObservationController;
use App\Http\Controllers\Api\Fhir\PatientController;
use App\Http\Controllers\Api\Fhir\SubscriptionController;
use App\Http\Controllers\Api\SmartConfigurationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// SMART on FHIR well-known configuration (unauthenticated)
Route::get('/.well-known/smart-configuration', SmartConfigurationController::class);

// FHIR server metadata / capability statement (unauthenticated)
Route::get('/fhir/metadata', MetadataController::class);

// FHIR RESTful read/search endpoints
Route::prefix('fhir')->middleware(['auth:api', 'scopes:patient.read'])->group(function () {
    Route::get('/Patient', [PatientController::class, 'index']);
    Route::get('/Patient/{patient}', [PatientController::class, 'show']);

    Route::get('/Encounter', [EncounterController::class, 'index']);
    Route::get('/Encounter/{encounter}', [EncounterController::class, 'show']);

    Route::get('/Observation', [ObservationController::class, 'index']);

    Route::get('/MedicationRequest', [MedicationRequestController::class, 'index']);
    Route::get('/MedicationRequest/{medication}', [MedicationRequestController::class, 'show']);

    Route::get('/DocumentReference', [DocumentReferenceController::class, 'index']);
    Route::get('/DocumentReference/{document}', [DocumentReferenceController::class, 'show']);
});

// FHIR RESTful write endpoints
Route::prefix('fhir')->middleware(['auth:api', 'scopes:patient.write'])->group(function () {
    Route::post('/Patient', [PatientController::class, 'store']);
    Route::put('/Patient/{patient}', [PatientController::class, 'update']);

    Route::post('/Encounter', [EncounterController::class, 'store']);
    Route::put('/Encounter/{encounter}', [EncounterController::class, 'update']);
});

// FHIR subscription management
Route::prefix('fhir/Subscription')->middleware(['auth:api', 'scopes:patient.write'])->group(function () {
    Route::get('/', [SubscriptionController::class, 'index']);
    Route::post('/', [SubscriptionController::class, 'store']);
    Route::delete('/{subscription}', [SubscriptionController::class, 'destroy']);
});
