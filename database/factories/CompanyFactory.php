<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'company_name'    => fake()->unique()->company(),
            'company_logo'    => null,
            'company_address' => fake()->address(),
            'company_desc'    => fake()->catchPhrase(),
        ];
    }
}