<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement([
                'super_admin',
                'hospital_administrator',
                'doctor',
                'nurse',
                'receptionist',
                'laboratory_officer',
                'pharmacist',
                'cashier',
                'medical_records_officer',
                'radiologist',
                'guardian',
                'insurance_officer',
                'public_health_officer',
            ]),
            'npi_number' => fake()->optional()->numerify('1########'),
            'phone' => fake()->optional()->phoneNumber(),
            'timezone' => 'America/New_York',
            'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function attending(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'attending']);
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'super_admin']);
    }

    public function hospitalAdministrator(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'hospital_administrator']);
    }

    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'doctor']);
    }

    public function nurse(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'nurse']);
    }

    public function receptionist(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'receptionist']);
    }

    public function laboratoryOfficer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'laboratory_officer']);
    }

    public function pharmacist(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'pharmacist']);
    }

    public function cashier(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'cashier']);
    }

    public function medicalRecordsOfficer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'medical_records_officer']);
    }

    public function radiologist(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'radiologist']);
    }

    public function guardian(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'guardian']);
    }

    public function insuranceOfficer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'insurance_officer']);
    }

    public function publicHealthOfficer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'public_health_officer']);
    }

    public function clinicalStaff(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'clinical_staff']);
    }
}
