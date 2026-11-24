<?php

namespace Database\Factories;

use App\Models\BoardExam;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardExamFactory extends Factory
{
    protected $model = BoardExam::class;

    /**
     * Rate bands per top-notcher rank (Top 1 = highest).
     * Top 1: 99.00–100.00, Top 2: 98.00–98.99, … Top 10: 90.00–90.99
     * Non-top-notcher passers are capped at 88.99 (see verifiedPassing()).
     */
    protected function topNotcherBand(int $rank): array
    {
        $high = 100.00 - (($rank - 1) * 1.0);
        $low = $high - 0.99;

        return [round($low, 2), round($high, 2)];
    }

    public function definition(): array
    {
        $passed = fake()->boolean(72);

        $rate = $passed
            ? fake()->randomFloat(2, 75, 88.99)
            : fake()->randomFloat(2, 60, 74.99);

        return [
            'user_profile_id' => UserProfile::factory(),
            'exam_name' => null,
            'attempt_number' => 1,
            'date_taken' => fake()->dateTimeBetween('-6 years', 'now'),
            'rate' => $rate,
            'passed' => $passed,
            'is_verified' => false,
            'verified_at' => null,
            'is_top_notcher' => false,
            'top_notcher_rank' => null,
            'remarks' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Regular passing attempt — NEVER a top notcher, so capped below the
     * Top 10 band (90.00) so it can never outscore an actual top notcher.
     */
    public function verifiedPassing(): static
    {
        return $this->state(fn () => [
            'rate' => fake()->randomFloat(2, 75, 88.99),
            'passed' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'is_top_notcher' => false,
            'top_notcher_rank' => null,
        ]);
    }

    public function verifiedFailing(): static
    {
        return $this->state(fn () => [
            'rate' => fake()->randomFloat(2, 60, 74.99),
            'passed' => false,
            'is_verified' => true,
            'verified_at' => now(),
            'is_top_notcher' => false,
            'top_notcher_rank' => null,
        ]);
    }

    /**
     * Top notcher with a rank-aware rate band. Higher rank (1) ⇒ higher rate.
     * Top 1 ≥ Top 2 ≥ … ≥ Top 10. A lower rank can NEVER outscore a higher one
     * because bands do not overlap.
     */
    public function topNotcher(?int $rank = null): static
    {
        return $this->state(function () use ($rank) {
            $rank = $rank ?? fake()->numberBetween(1, 10);
            [$low, $high] = $this->topNotcherBand($rank);

            return [
                'rate' => round(fake()->randomFloat(2, $low, $high), 2),
                'passed' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'is_top_notcher' => true,
                'top_notcher_rank' => $rank,
            ];
        });
    }
}
