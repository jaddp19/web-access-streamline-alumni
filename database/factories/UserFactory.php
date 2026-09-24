<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->optional(0.7)->lastName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'school_id'         => fake()->unique()->numerify('####-####'),
            'email_verified_at' => now(),
            'password'          => 'password123', // auto-hashed by cast
        ];
    }

    // ===== Role states =====

    public function registrar(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('registrar'));
    }

    public function programHead(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('program head'));
    }

    public function alumni(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('alumni'));
    }
}