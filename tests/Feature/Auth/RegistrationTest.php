<?php

use App\Models\Patient;
use App\Models\Practice;
use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
    Practice::create(['name' => 'Bloom Clinic', 'slug' => 'bloom-clinic']);
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('clinician can register and get attending role', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Dr. Jane Smith',
        'email' => 'jane.smith@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
        'role' => 'clinician',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated('web');

    $user = User::where('email', 'jane.smith@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('attending')
        ->and($user->practice_id)->not->toBeNull();
});

test('guardian can register with child name and link patient', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Guardian Papa',
        'email' => 'papa@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
        'role' => 'guardian',
        'child_name' => 'Child Kiddo',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('portal.dashboard'));

    $this->assertAuthenticated('portal');

    $user = User::where('email', 'papa@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('guardian')
        ->and($user->practice_id)->not->toBeNull();

    $patient = Patient::where('portal_user_id', $user->id)->first();
    expect($patient)->not->toBeNull()
        ->and($patient->first_name)->toBe('Child')
        ->and($patient->last_name)->toBe('Kiddo');
});
