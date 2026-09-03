<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        // Example batch years
        $years = [2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026];

        foreach ($years as $year) {
            Batch::updateOrCreate(
                ['batch_name' => $year]
            );
        }
    }
}
