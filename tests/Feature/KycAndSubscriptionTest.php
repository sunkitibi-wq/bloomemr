<?php

namespace Tests\Feature;

use App\Livewire\Auth\KycOnboarding;
use App\Livewire\Auth\UserSubscription;
use App\Livewire\SystemAdmin\KycVerificationManager;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
        'kyc_status' => 'unsubmitted',
        'subscribed_until' => null,
    ]);
    $this->user->assignRole('attending');

    $this->admin = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'super_admin',
        'is_system_admin' => true,
    ]);
    $this->admin->assignRole('super_admin');
});

test('unverified clinician is redirected to kyc onboarding', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertRedirect(route('kyc'));
});

test('clinician can submit kyc details', function () {
    Livewire::actingAs($this->user)
        ->test(KycOnboarding::class)
        ->set('legal_first_name', 'John')
        ->set('legal_last_name', 'Doe')
        ->set('date_of_birth', '1985-05-15')
        ->set('selected_role', 'attending')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('license_number', 'MD123456')
        ->set('license_state', 'NY')
        ->set('npi_number', '1234567890')
        ->call('nextStep')
        ->assertSet('step', 3)
        ->call('uploadMockDocument')
        ->assertSet('document_uploaded', true)
        ->call('submitKyc')
        ->assertSet('step', 4);

    $this->user->refresh();
    expect($this->user->kyc_status)->toBe('pending')
        ->and($this->user->kyc_data)->not->toBeEmpty()
        ->and($this->user->kyc_data['license_number'])->toBe('MD123456');
});

test('admin can manage pending kyc applications', function () {
    $this->user->update([
        'kyc_status' => 'pending',
        'kyc_data' => ['legal_first_name' => 'John', 'legal_last_name' => 'Doe', 'license_number' => 'MD123456'],
    ]);

    // Test Admin Approval
    Livewire::actingAs($this->admin)
        ->test(KycVerificationManager::class)
        ->call('approveKyc', $this->user->id);

    $this->user->refresh();
    expect($this->user->kyc_status)->toBe('approved');

    // Test Admin Rejection
    $this->user->update(['kyc_status' => 'pending']);
    Livewire::actingAs($this->admin)
        ->test(KycVerificationManager::class)
        ->set('rejectionReasons.'.$this->user->id, 'Missing registration stamp.')
        ->call('rejectKyc', $this->user->id);

    $this->user->refresh();
    expect($this->user->kyc_status)->toBe('rejected')
        ->and($this->user->kyc_rejection_reason)->toBe('Missing registration stamp.');
});

test('verified but unsubscribed clinician is redirected to subscribe page', function () {
    $this->user->update(['kyc_status' => 'approved', 'subscribed_until' => null]);

    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertRedirect(route('subscribe'));
});

test('clinician can subscribe using mock card payment', function () {
    $this->user->update(['kyc_status' => 'approved', 'subscribed_until' => null]);

    Livewire::actingAs($this->user)
        ->test(UserSubscription::class)
        ->set('selectedPlan', '6_months')
        ->set('cardNumber', '4000123456789010')
        ->set('cardExpiry', '12/28')
        ->set('cardCvc', '123')
        ->call('processPayment')
        ->assertRedirect(route('dashboard'));

    $this->user->refresh();
    expect($this->user->isSubscribed())->toBeTrue()
        ->and($this->user->subscribed_until->isFuture())->toBeTrue();
});

test('guardians are exempt from kyc and subscription checks', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $response = $this->actingAs($guardian)
        ->get(route('portal.dashboard'));

    $response->assertOk();
});

test('super admins are exempt from kyc and subscription checks', function () {
    $superAdmin = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'super_admin',
        'kyc_status' => 'unsubmitted',
        'subscribed_until' => null,
    ]);
    $superAdmin->assignRole('super_admin');

    $response = $this->actingAs($superAdmin)
        ->get(route('dashboard'));

    $response->assertOk();
});
