<?php

use App\Livewire\Portal\PatientPortalDashboard;
use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->practice = Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
    $this->user = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'attending',
    ]);
    $this->user->assignRole('attending');
});

test('it can load the patient portal dashboard for clinician fallback', function () {
    $this->actingAs($this->user);

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'Leo',
        'last_name' => 'Care',
    ]);

    Livewire::test(PatientPortalDashboard::class)
        ->assertOk()
        ->assertSee("Leo's Care");
});

test('guest cannot access portal dashboard', function () {
    $response = $this->get(route('portal.dashboard'));
    $response->assertRedirect(route('login'));
});

test('guardian can access portal dashboard when logged in to portal guard', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $patient = Patient::factory()->create([
        'practice_id' => $this->practice->id,
        'first_name' => 'Leo',
        'last_name' => 'Care',
        'portal_user_id' => $guardian->id,
    ]);

    Livewire::actingAs($guardian, 'portal')
        ->test(PatientPortalDashboard::class)
        ->assertOk()
        ->assertSee("Leo's Care");
});

test('guardian is blocked from EMR dashboard and settings', function () {
    $guardian = User::factory()->create([
        'practice_id' => $this->practice->id,
        'role' => 'guardian',
    ]);
    $guardian->assignRole('guardian');

    $this->actingAs($guardian, 'portal');

    // Trying to access EMR dashboard
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));

    // Trying to access settings
    $this->get(route('profile.edit'))
        ->assertRedirect(route('login'));
});
