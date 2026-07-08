<?php

namespace Database\Factories;

use App\Models\ClinicalNote;
use App\Models\Encounter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClinicalNote>
 */
class ClinicalNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'encounter_id' => Encounter::factory(),
            'template_type' => $this->faker->randomElement(['soap', 'dap', 'narrative', 'intake']),
            'body' => $this->faker->optional()->paragraphs(3, true),
            'sections' => null,
            'draft' => null,
            'version' => 1,
        ];
    }
}
