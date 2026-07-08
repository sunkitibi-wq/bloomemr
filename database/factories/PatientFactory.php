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
            'mrn' => $this->faker->unique()->numerify('MRN-#####'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-5 years'),
            'gender_identity' => $this->faker->randomElement(['male', 'female', 'non-binary']),
            'pronouns' => $this->faker->randomElement(['he/him', 'she/her', 'they/them']),
            'race_ethnicity' => $this->faker->optional()->randomElement(['White', 'Black', 'Hispanic', 'Asian', 'Other']),
            'preferred_language' => 'en',
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'emergency_contact_name' => $this->faker->optional()->name(),
            'emergency_contact_phone' => $this->faker->optional()->phoneNumber(),
            'emergency_contact_relationship' => $this->faker->optional()->randomElement(['Parent', 'Sibling', 'Spouse', 'Guardian']),
            'primary_provider_id' => User::factory(),
        ];
    }
}
