<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRsvpFactory extends Factory
{
    protected $model = EventRsvp::class;

    public function definition(): array
    {
        $response = fake()->randomElement(['yes', 'yes', 'yes', 'maybe', 'no']);

        return [
            'event_id'     => Event::factory(),
            'user_id'      => User::factory(),
            'response'     => $response,
            'responded_at' => now(),
            'attended_at'  => $response === 'yes' && fake()->boolean(60) ? now() : null,
            'notes'        => null,
        ];
    }
}