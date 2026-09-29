<?php

namespace Database\Factories;

use App\Models\BoardExam;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardExamFactory extends Factory
{
    protected $model = BoardExam::class;

    public function definition(): array
    {
        $passed = fake()->boolean(85);

        $rate = $passed
            ? fake()->randomFloat(2, 75, 99)
            : fake()->randomFloat(2, 60, 74.99);

        return [
            'user_profile_id'  => UserProfile::factory(),
            'exam_name'        => null,
            'attempt_number'   => 1,
            'date_taken'       => fake()->dateTimeBetween('-6 years', 'now'),
            'rate'             => $rate,
            'passed'           => $passed,
            'is_verified'      => false,
            'verified_at'      => null,
            'is_top_notcher'   => false,
            'top_notcher_rank' => null,
            'remarks'          => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function verifiedPassing(): static
    {
        return $this->state(fn () => [
            'rate'        => fake()->randomFloat(2, 75, 99),
            'passed'      => true,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function verifiedFailing(): static
    {
        return $this->state(fn () => [
            'rate'        => fake()->randomFloat(2, 60, 74.99),
            'passed'      => false,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function topNotcher(?int $rank = null): static
    {
        return $this->state(fn () => [
            'rate'             => fake()->randomFloat(2, 88, 99),
            'passed'           => true,
            'is_verified'      => true,
            'verified_at'      => now(),
            'is_top_notcher'   => true,
            'top_notcher_rank' => $rank ?? fake()->numberBetween(1, 10),
        ]);
    }
}