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
            'is_active'         => true,          // ← new: default everyone is active
        ];
    }

    // ===== Account state =====

    /**
     * Deactivated account — can no longer log in.
     * Analytics + reports exclude these; the users index still shows them.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    /**
     * Never-verified email. Useful for testing the verification banner
     * and any "verify your email" flows.
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
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