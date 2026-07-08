<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mrn' => \fake()->unique()->numerify('MRN-#####'),
            'first_name' => \fake()->firstName(),
            'last_name' => \fake()->lastName(),
            'date_of_birth' => \fake()->date('Y-m-d', '-5 years'),
            'gender_identity' => \fake()->randomElement(['male', 'female', 'non-binary']),
            'pronouns' => \fake()->randomElement(['he/him', 'she/her', 'they/them']),
            'race_ethnicity' => \fake()->optional()->randomElement(['White', 'Black', 'Hispanic', 'Asian', 'Other']),
            'preferred_language' => 'en',
            'phone' => \fake()->phoneNumber(),
            'email' => \fake()->optional()->safeEmail(),
            'address' => \fake()->optional()->address(),
            'emergency_contact_name' => \fake()->optional()->name(),
            'emergency_contact_phone' => \fake()->optional()->phoneNumber(),
            'emergency_contact_relationship' => \fake()->optional()->randomElement(['Parent', 'Sibling', 'Spouse', 'Guardian']),
            'primary_provider_id' => User::factory(),
        ];
    }
}
