<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\WorkHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkHistoryFactory extends Factory
{
    protected $model = WorkHistory::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'work_name'      => fake()->jobTitle(),
            'company_id'     => Company::factory(),
            'date_hired'     => fake()->dateTimeBetween('-5 years', 'now'),
            'is_current_job' => fake()->boolean(60),
        ];
    }
}