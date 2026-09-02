<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'cat_name' => 'Technology',
                'cat_desc' => 'Articles about the latest trends and news in technology.',
            ],
            [
                'cat_name' => 'Lifestyle',
                'cat_desc' => 'Tips and stories about everyday living and personal growth.',
            ],
            [
                'cat_name' => 'Travel',
                'cat_desc' => 'Guides, tips, and stories from destinations around the world.',
            ],
            [
                'cat_name' => 'Health & Fitness',
                'cat_desc' => 'Advice and insights on staying healthy and fit.',
            ],
            [
                'cat_name' => 'Food & Recipes',
                'cat_desc' => 'Delicious recipes and cooking tips for every occasion.',
            ],
            [
                'cat_name' => 'Business',
                'cat_desc' => 'News, strategies, and insights for entrepreneurs and businesses.',
            ],
            [
                'cat_name' => 'Education',
                'cat_desc' => 'Learning resources, study tips, and educational content.',
            ],
            [
                'cat_name' => 'Entertainment',
                'cat_desc' => 'Updates and reviews on movies, music, and pop culture.',
            ],
            [
                'cat_name' => 'Jokes & Humor',
                'cat_desc' => 'Funny posts, jokes, memes, and lighthearted content to make you laugh.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'cat_name' => $category['cat_name'],
                'cat_slug' => Str::slug($category['cat_name']),
                'cat_desc' => $category['cat_desc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
