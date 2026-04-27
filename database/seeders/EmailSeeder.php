<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Email;
use Illuminate\Support\Str;

class EmailSeeder extends Seeder
{
    public function run(): void
    {
        Email::updateOrCreate(
            ['slug' => 'alumni-accepted'],
            [
                'subject' => 'Alumni Application Accepted',
                'message' => "Dear {{name}},\n\nCongratulations! Your alumni application in Colegio de Sta. Ana de Victorias has been accepted. You are now officially part of our alumni community.\n\nBest regards,\nAlumni Management Team - Colegio de Sta. Ana de Victorias",
            ]
        );
    }
}
