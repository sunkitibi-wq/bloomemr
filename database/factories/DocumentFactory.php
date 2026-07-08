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
            'category' => \fake()->randomElement(['outside_records', 'iep', 'behavioral_plan', 'school_report', 'prior_auth', 'other']),
            'original_name' => \fake()->word().'.pdf',
            'file_path' => 'documents/'.\fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => \fake()->numberBetween(10000, 5000000),
            'version' => 1,
        ];
    }
}
