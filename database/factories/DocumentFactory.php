<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'uploaded_by' => User::factory(),
            'category' => $this->faker->randomElement(['outside_records', 'iep', 'behavioral_plan', 'school_report', 'prior_auth', 'other']),
            'original_name' => $this->faker->word().'.pdf',
            'file_path' => 'documents/'.$this->faker->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(10000, 5000000),
            'version' => 1,
        ];
    }
}
