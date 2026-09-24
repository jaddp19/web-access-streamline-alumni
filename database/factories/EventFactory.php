<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title  = fake()->unique()->sentence(4);
        $starts = fake()->dateTimeBetween('-6 months', '+6 months');

        return [
            'title'                 => $title,
            'slug'                  => Str::slug($title) . '-' . Str::random(6),
            'description'           => fake()->paragraph(4),
            'image'                 => null,
            'starts_at'             => $starts,
            'ends_at'               => (clone $starts)->modify('+2 hours'),
            'registration_deadline' => (clone $starts)->modify('-1 day'),
            'location'              => fake()->randomElement([
                'CSAV Gymnasium', 'Covered Court', 'AVR Building', 'Online',
            ]),
            'capacity'              => fake()->randomElement([50, 100, 200, 500, null]),
            'status'                => 'published',
            'created_by'            => User::factory()->registrar(),
        ];
    }
}