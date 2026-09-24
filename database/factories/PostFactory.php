<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'user_id'     => User::factory(),
            'title'       => $title,
            'slug'        => Str::slug($title) . '-' . Str::random(6),
            'description' => fake()->paragraph(6),
            'image'       => null,
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),
            'status'      => 'public',
            'attachments' => [],
        ];
    }
}