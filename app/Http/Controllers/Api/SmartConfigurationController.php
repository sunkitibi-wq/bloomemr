<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SmartConfigurationController extends Controller
{
    /**
     * SMART on FHIR .well-known/smart-configuration endpoint.
     */
    public function __invoke(): JsonResponse
    {
        $baseUrl = config('app.url');

        return response()->json([
            'authorization_endpoint' => "{$baseUrl}/oauth/authorize",
            'token_endpoint' => "{$baseUrl}/oauth/token",
            'token_endpoint_auth_methods_supported' => [
                'client_secret_basic',
                'client_secret_post',
            ],
            'grant_types_supported' => [
                'authorization_code',
                'client_credentials',
                'refresh_token',
            ],
            'scopes_supported' => [
                'patient.read',
                'patient.write',
                'encounter.read',
                'encounter.write',
                'observation.read',
                'medication.read',
                'medication.write',
                'document.read',
                'document.write',
                'patient.all',
                'clinical.all',
                'openid',
                'fhirUser',
            ],
            'response_types_supported' => ['code', 'token'],
            'capabilities' => [
                'launch-ehr',
                'client-public',
                'client-confidential-symmetric',
                'sso-openid-connect',
                'context-passthrough-banner',
                'context-passthrough-style',
                'permission-v2',
                'authorize-post',
            ],
        ]);
    }
}
