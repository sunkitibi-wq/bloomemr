<?php

use App\Models\Practice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pharmacist users are redirected to the pharmacy portal after login', function () {
    $practice = Practice::create(['name' => 'Test Practice', 'slug' => 'test-practice']);

    $user = User::factory()->create([
        'practice_id' => $practice->id,
        'role' => 'pharmacist',
        'email' => 'pharmacist-login@test.com',
    ]);

    $user->assignRole('pharmacist');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('pharmacy.portal'));
});
