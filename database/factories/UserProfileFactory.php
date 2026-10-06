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
        $hasSecondContact = fake()->boolean(25);   // ~25% have a backup number
        $isPrivate        = fake()->boolean(10);   // ~10% hide their profile

        return [
            'user_id'               => User::factory(),
            'avatar'                => null,        // seeded alumni don't have real photos
            'gender'                => fake()->randomElement(['male', 'female', 'other']),
            'contact_number_1'      => fake()->unique()->numerify('09#########'),
            'contact_number_2'      => $hasSecondContact ? fake()->numerify('09#########') : null,
            'location'              => $this->sampleLocation(),
            'batch_id'              => Batch::query()->inRandomOrder()->value('id') ?? Batch::factory(),
            'is_private'            => $isPrivate,

            // Cached mirror — kept in sync by syncBoardMirrors().
            // Default false; real value is derived from board_exams by the seeder.
            'is_verified'           => false,

            // Approval workflow — default is pending (matches migration default).
            'is_approved'           => false,
            'last_rejection_reason' => null,

            // Mirror columns — filled by syncBoardMirrors() after attempts exist.
            'board_taken'           => null,
            'board_rate'            => null,
        ];
    }

    // =========================================================
    //  APPROVAL STATES
    // =========================================================

    /** Approved alumni — the "good to go" state. */
    public function approved(): static
    {
        return $this->state(fn () => [
            'is_approved'           => true,
            'last_rejection_reason' => null,
            // is_verified is left to the seeder to derive from board_exams.
        ]);
    }

    /** Awaiting review — no reason set, still pending. */
    public function pending(): static
    {
        return $this->state(fn () => [
            'is_approved'           => false,
            'last_rejection_reason' => null,
        ]);
    }

    /** Rejected — always includes a reason. */
    public function rejected(?string $reason = null): static
    {
        return $this->state(fn () => [
            'is_approved'           => false,
            'is_verified'           => false,
            'last_rejection_reason' => $reason ?? fake()->randomElement([
                'Board rating doesn\'t match the official PRC records. Please update and resubmit.',
                'School ID appears incorrect. Please double-check and resubmit.',
                'Name in the profile does not match our records. Please correct it.',
                'Contact number appears invalid. Please provide a reachable number.',
            ]),
        ]);
    }

    // =========================================================
    //  LOCATION STATES
    // =========================================================

    /** Alumni who lives abroad (for the employment_area = 'abroad' cohort). */
    public function abroad(): static
    {
        return $this->state(fn () => [
            'location' => $this->abroadLocation(),
        ]);
    }

    /**
     * Alumni from a different PH region — makes the alumniByRegion()
     * chart non-uniform so it actually shows multiple bars.
     */
    public function fromOtherRegion(): static
    {
        return $this->state(fn () => [
            'location' => $this->randomPhLocation(),
        ]);
    }

    /** Hide the profile from other alumni. */
    public function private(): static
    {
        return $this->state(fn () => [
            'is_private' => true,
        ]);
    }

    // =========================================================
    //  LOCATION HELPERS
    // =========================================================

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

    protected function randomPhLocation(): array
    {
        // Small fixed set so group-by region stays meaningful —
        // doesn't produce 30 random regions the chart can't display.
        $regions = [
            ['code' => '1300000000', 'name' => 'National Capital Region (NCR)'],
            ['code' => '0700000000', 'name' => 'Region VII (Central Visayas)'],
            ['code' => '1100000000', 'name' => 'Region XI (Davao Region)'],
            ['code' => '0300000000', 'name' => 'Region III (Central Luzon)'],
            ['code' => '0400000000', 'name' => 'Region IV-A (CALABARZON)'],
        ];

        $region = fake()->randomElement($regions);
        $street = fake()->streetAddress();

        return [
            'address_type'   => 'philippines',
            'street_address' => $street,
            'region_code'    => $region['code'],
            'region_name'    => $region['name'],
            'province_code'  => null,
            'province_name'  => null,
            'city_code'      => null,
            'city_name'      => null,
            'barangay_code'  => null,
            'barangay_name'  => null,
            'address'        => $street . ', ' . $region['name'],
            'intl_country'   => null,
            'intl_state'     => null,
            'intl_city'      => null,
        ];
    }

    protected function abroadLocation(): array
    {
        $countries = ['United States', 'Canada', 'United Arab Emirates', 'Saudi Arabia', 'Australia', 'Japan', 'Singapore', 'United Kingdom'];
        $country   = fake()->randomElement($countries);

        $street = fake()->streetAddress();
        $city   = fake()->city();

        return [
            'address_type'   => 'abroad',
            'street_address' => $street,
            'region_code'    => null,
            'region_name'    => null,
            'province_code'  => null,
            'province_name'  => null,
            'city_code'      => null,
            'city_name'      => null,
            'barangay_code'  => null,
            'barangay_name'  => null,
            'address'        => $street . ', ' . $city . ', ' . $country,
            'intl_country'   => $country,
            'intl_state'     => null,
            'intl_city'      => $city,
        ];
    }
}   