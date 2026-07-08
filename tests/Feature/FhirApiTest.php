<?php

use App\Models\Document;
use App\Models\Encounter;
use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Laravel\Passport\Passport;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');

    $this->patient = Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'MRN-FHIR-001',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'date_of_birth' => '2010-03-15',
        'gender_identity' => 'female',
        'phone' => '555-0100',
        'email' => 'jane@example.com',
        'preferred_language' => 'en',
    ]);
});

test('metadata endpoint returns capability statement', function () {
    $this->getJson('/api/fhir/metadata')
        ->assertSuccessful()
        ->assertJson([
            'resourceType' => 'CapabilityStatement',
            'fhirVersion' => '4.0.1',
        ])
        ->assertJsonPath('rest.0.mode', 'server')
        ->assertJsonPath('rest.0.resource.0.type', 'Patient')
        ->assertJsonPath('rest.0.resource.1.type', 'Encounter');
});

test('smart configuration endpoint returns oauth metadata', function () {
    $this->getJson('/api/.well-known/smart-configuration')
        ->assertSuccessful()
        ->assertJsonStructure([
            'authorization_endpoint',
            'token_endpoint',
            'grant_types_supported',
            'scopes_supported',
            'capabilities',
        ]);
});

test('unauthenticated request to fhir patient search returns 401', function () {
    $this->getJson('/api/fhir/Patient')
        ->assertUnauthorized();
});

test('authenticated request can search patients', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $response = $this->getJson('/api/fhir/Patient');

    $response->assertSuccessful()
        ->assertJson([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
        ]);

    expect($response->json('entry'))->toHaveCount(1)
        ->and($response->json('entry.0.resourceType'))->toBe('Patient');
});

test('patient search filters by family name', function () {
    Passport::actingAs($this->user, ['patient.read']);

    Patient::create([
        'practice_id' => $this->practice->id,
        'mrn' => 'MRN-FHIR-002',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '2005-07-20',
    ]);

    $this->getJson('/api/fhir/Patient?family=Doe')
        ->assertSuccessful()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('entry.0.name.0.family', 'Doe');
});

test('patient search filters by mrn identifier', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $this->getJson('/api/fhir/Patient?identifier=MRN-FHIR-001')
        ->assertSuccessful()
        ->assertJsonPath('total', 1);
});

test('patient show returns single resource', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $this->getJson('/api/fhir/Patient/'.$this->patient->id)
        ->assertSuccessful()
        ->assertJson([
            'resourceType' => 'Patient',
            'id' => (string) $this->patient->id,
        ]);
});

test('patient resource includes identifier with mrn', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $response = $this->getJson('/api/fhir/Patient/'.$this->patient->id);

    expect($response->json('identifier'))->toHaveCount(1)
        ->and($response->json('identifier.0.value'))->toBe('MRN-FHIR-001');
});

test('patient resource maps gender correctly', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $this->getJson('/api/fhir/Patient/'.$this->patient->id)
        ->assertJsonPath('gender', 'female');
});

test('encounter search returns bundle', function () {
    Passport::actingAs($this->user, ['patient.read']);

    Encounter::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'provider_id' => $this->user->id,
        'type' => 'office_visit',
        'status' => 'signed',
        'encounter_date' => now(),
    ]);

    $this->getJson('/api/fhir/Encounter')
        ->assertSuccessful()
        ->assertJson([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
        ])
        ->assertJsonPath('total', 1);
});

test('encounter filters by patient reference', function () {
    Passport::actingAs($this->user, ['patient.read']);

    Encounter::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'provider_id' => $this->user->id,
        'type' => 'telehealth',
        'status' => 'signed',
        'encounter_date' => now(),
    ]);

    $this->getJson('/api/fhir/Encounter?patient=Patient/'.$this->patient->id)
        ->assertSuccessful()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('entry.0.subject.reference', 'Patient/'.$this->patient->id);
});

test('medication request search returns bundle', function () {
    Medication::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'name' => 'Sertraline',
        'dose' => '50mg',
        'frequency' => 'daily',
        'status' => 'active',
    ]);

    Passport::actingAs($this->user, ['patient.read']);

    $this->getJson('/api/fhir/MedicationRequest')
        ->assertSuccessful()
        ->assertJsonPath('total', 1);
});

test('document reference search returns bundle', function () {
    Passport::actingAs($this->user, ['patient.read']);

    Document::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'uploaded_by' => $this->user->id,
        'category' => 'iep',
        'original_name' => 'iep-2026.pdf',
        'file_path' => 'documents/iep-2026.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
    ]);

    $this->getJson('/api/fhir/DocumentReference')
        ->assertSuccessful()
        ->assertJsonPath('total', 1);
});

test('observation search returns lab bundle', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $order = LabOrder::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'panel' => 'Thyroid Panel',
        'status' => 'ordered',
    ]);

    LabResult::create([
        'practice_id' => $this->practice->id,
        'patient_id' => $this->patient->id,
        'lab_order_id' => $order->id,
        'result_data' => [
            ['name' => 'TSH', 'value' => 3.5, 'unit' => 'mIU/L', 'range' => '0.4-4.0', 'flag' => 'N'],
        ],
    ]);

    $this->getJson('/api/fhir/Observation')
        ->assertSuccessful()
        ->assertJsonPath('resourceType', 'Bundle');
});

test('scope enforcement denies access without correct scope', function () {
    Passport::actingAs($this->user, ['medication.read']);

    $this->getJson('/api/fhir/Patient')
        ->assertForbidden();
});

test('subscription management requires write scope', function () {
    Passport::actingAs($this->user, ['patient.read']);

    $this->postJson('/api/fhir/Subscription', [
        'name' => 'Test Webhook',
        'endpoint_url' => 'https://example.com/webhook',
        'events' => ['patient.created'],
    ])->assertForbidden();
});

test('can create and list subscriptions with write scope', function () {
    Passport::actingAs($this->user, ['patient.write']);

    $this->postJson('/api/fhir/Subscription', [
        'name' => 'Patient Updates',
        'endpoint_url' => 'https://ehr.example.com/webhook',
        'secret' => 'abcdef1234567890abcdef12',
        'events' => ['patient.created', 'patient.updated'],
    ])->assertCreated()
        ->assertJson([
            'resourceType' => 'Subscription',
            'status' => 'active',
        ]);

    $this->getJson('/api/fhir/Subscription')
        ->assertSuccessful()
        ->assertJsonPath('total', 1);
});
