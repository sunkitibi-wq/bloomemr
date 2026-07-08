<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Encounter>
 */
class EncounterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'provider_id' => User::factory(),
            'type' => \fake()->randomElement(['soap', 'dap', 'narrative', 'intake']),
            'status' => 'draft',
            'encounter_date' => \fake()->dateTimeBetween('-1 month', 'now'),
            'chief_complaint' => \fake()->optional()->sentence(),
        ];
    }

    public function signed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'signed',
            'signed_at' => now(),
            'signed_by' => User::factory(),
        ]);
    }
}
