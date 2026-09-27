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
            // Safest default — just a user. If you need a registrar,
            // override it explicitly or use the ->withRegistrar() state below.
            'created_by'            => User::factory(),
        ];
    }

    /** Prefer an existing registrar if one exists; otherwise create one. */
    public function withRegistrar(): static
    {
        return $this->state(function () {
            $registrar = User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->inRandomOrder()
                ->first();

            if ($registrar) {
                return ['created_by' => $registrar->id];
            }

            // Fall back to creating a plain user — avoids the
            // "role does not exist" failure when roles aren't seeded yet.
            return ['created_by' => User::factory()];
        });
    }
}