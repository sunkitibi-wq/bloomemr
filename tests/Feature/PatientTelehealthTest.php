<?php

use App\Livewire\Patients\PatientTelehealth;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->provider = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->provider->assignRole('attending');

    $this->patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'Joey',
        'last_name' => 'Doe',
    ]);
});

test('clinician can load telehealth consultation room', function () {
    Livewire::actingAs($this->provider, 'web')
        ->test(PatientTelehealth::class, ['patient' => $this->patient])
        ->assertOk()
        ->assertSee('Clinician Telehealth Portal')
        ->call('joinCall')
        ->assertSet('isCallActive', true)
        ->assertSet('callStatus', 'connecting')
        ->call('endCall')
        ->assertSet('isCallActive', false)
        ->assertSet('callStatus', 'ended');
});

test('clinician can join call with Daily.co API key configured', function () {
    // Configure Daily API key
    config(['services.daily.key' => 'test-api-key']);

    // Fake Daily.co API responses
    Http::fake([
        'api.daily.co/v1/rooms/bloom-telehealth-patient-*' => Http::response([
            'url' => 'https://bloom-test.daily.co/bloom-telehealth-patient-1',
            'name' => 'bloom-telehealth-patient-1',
        ], 200),
        'api.daily.co/v1/meeting-tokens' => Http::response([
            'token' => 'mock-token-123',
        ], 200),
    ]);

    Livewire::actingAs($this->provider, 'web')
        ->test(PatientTelehealth::class, ['patient' => $this->patient])
        ->assertOk()
        ->call('joinCall')
        ->assertSet('isCallActive', true)
        ->assertSet('callStatus', 'connected')
        ->assertSet('roomUrl', 'https://bloom-test.daily.co/bloom-telehealth-patient-1')
        ->assertSet('meetingToken', 'mock-token-123');

    // Assert the HTTP calls were made
    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/rooms/bloom-telehealth-patient-') &&
               $request->method() === 'GET';
    });

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/meeting-tokens') &&
               $request->method() === 'POST' &&
               $request['properties']['is_owner'] === true;
    });
});

test('guardian can join call with Daily.co API key configured via portal', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $this->patient->update(['portal_user_id' => $guardian->id]);

    // Configure Daily API key
    config(['services.daily.key' => 'test-api-key']);

    // Fake Daily.co API responses
    Http::fake([
        'api.daily.co/v1/rooms/bloom-telehealth-patient-*' => Http::response([
            'url' => 'https://bloom-test.daily.co/bloom-telehealth-patient-1',
            'name' => 'bloom-telehealth-patient-1',
        ], 200),
        'api.daily.co/v1/meeting-tokens' => Http::response([
            'token' => 'mock-token-123',
        ], 200),
    ]);

    Livewire::actingAs($guardian, 'portal')
        ->test(App\Livewire\Portal\PortalTelehealth::class)
        ->assertOk()
        ->call('joinCall')
        ->assertSet('isCallActive', true)
        ->assertSet('callStatus', 'connected')
        ->assertSet('roomUrl', 'https://bloom-test.daily.co/bloom-telehealth-patient-1')
        ->assertSet('meetingToken', 'mock-token-123');

    // Assert the HTTP calls were made with owner as false
    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/meeting-tokens') &&
               $request->method() === 'POST' &&
               $request['properties']['is_owner'] === false;
    });
});
