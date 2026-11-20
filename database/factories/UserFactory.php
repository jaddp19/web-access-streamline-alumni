<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected const SCHOOL_ID_MIN_YEAR = 2011;
    protected const SCHOOL_ID_MAX_YEAR = 2023;
    protected const SCHOOL_ID_PER_YEAR = 9999;

    /** Static in-memory counter: [year => last_used_number]. */
    protected static array $schoolIdCounter = [];

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.7)->lastName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'school_id' => $this->nextSchoolId(),
            'email_verified_at' => now(),
            'password' => 'password123',
            'is_active' => true,
        ];
    }

    /**
     * Returns the next free school_id, skipping IDs already in `users`.
     * Format: YYYY-NNNN, YYYY ∈ [2011, 2023], NNNN ∈ [0001, 9999].
     * Rolls to the next year when NNNN > 9999.
     */
    protected function nextSchoolId(): string
    {
        // Start at the earliest year that still has room, otherwise
        // resume from the highest year we've touched.
        $year = static::$schoolIdCounter
            ? max(static::SCHOOL_ID_MIN_YEAR, min(array_keys(static::$schoolIdCounter)))
            : static::SCHOOL_ID_MIN_YEAR;

        while ($year <= static::SCHOOL_ID_MAX_YEAR) {
            $last = static::$schoolIdCounter[$year] ?? 0;
            $candidate = $last + 1;

            // Walk forward through this year until we find a free slot
            // or exhaust it.
            while ($candidate <= static::SCHOOL_ID_PER_YEAR) {
                $schoolId = sprintf('%d-%04d', $year, $candidate);

                if (! User::where('school_id', $schoolId)->exists()) {
                    static::$schoolIdCounter[$year] = $candidate;

                    return $schoolId;
                }

                $candidate++;
            }

            // Year exhausted — mark it full and move to the next year.
            static::$schoolIdCounter[$year] = static::SCHOOL_ID_PER_YEAR;
            $year++;
        }

        throw new \RuntimeException(
            'School ID space exhausted (2011-0001 through 2023-9999).'
        );
    }

    // ===== Account state =====

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
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