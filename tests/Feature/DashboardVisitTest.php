<?php

namespace Tests\Feature;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_visit_dashboard(): void
    {
        $practice = Practice::create([
            'name' => 'Test Practice',
            'slug' => 'test-practice',
        ]);

        $user = User::factory()->attending()->create([
            'practice_id' => $practice->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
