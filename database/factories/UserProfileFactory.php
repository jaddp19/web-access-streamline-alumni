<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'avatar'           => null,
            'gender'           => fake()->randomElement(['male', 'female', 'other']),
            'contact_number_1' => fake()->unique()->numerify('09#########'),
            'contact_number_2' => null,
            'location'         => $this->sampleLocation(),
            'batch_id'         => Batch::query()->inRandomOrder()->value('id') ?? Batch::factory(),
            'is_private'       => false,
            'is_verified'      => fake()->boolean(70),
            'board_taken'      => null,
            'board_rate'       => null,
        ];
    }

    public function withBoardExam(): static
    {
        return $this->state(function () {
            // Most alumni passed. A few didn't.
            $passed = fake()->boolean(85);

            $rate = $passed
                ? fake()->randomFloat(2, 75, 99)
                : fake()->randomFloat(2, 60, 74.99);

            return [
                'board_taken' => fake()->dateTimeBetween('-6 years', 'now'),
                'board_rate'  => $rate,
            ];
        });
    }

    /** Philippines location shape that matches the profile form. */
    protected function sampleLocation(): array
    {
        $street = fake()->streetAddress();

        return [
            'address_type'   => 'philippines',
            'street_address' => $street,
            'region_code'    => '0600000000',
            'region_name'    => 'Region VI (Western Visayas)',
            'province_code'  => '0604500000',
            'province_name'  => 'Negros Occidental',
            'city_code'      => '0604506000',
            'city_name'      => 'Victorias City',
            'barangay_code'  => '0604506001',
            'barangay_name'  => 'Barangay I (Poblacion)',
            'address'        => $street . ', Victorias City, Negros Occidental',
            'intl_country'   => null,
            'intl_state'     => null,
            'intl_city'      => null,
        ];
    }
}