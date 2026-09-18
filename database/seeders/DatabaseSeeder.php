<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            PermissionSeeder::class,
            BatchSeeder::class,
            EmailSeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            PhAddressSeeder::class,
        ]);

        // Create roles if not already seeded
        $superAdminRole = Role::firstOrCreate(['name' => 'registrar']);
        $adminRole      = Role::firstOrCreate(['name' => 'program head']);
        $alumniRole     = Role::firstOrCreate(['name' => 'alumni']);

        // Registrar account
        $superAdmin = User::firstOrCreate(
            ['email' => 'registrar@example.com'],
            [
                'first_name'  => 'Registrar',
                'middle_name' => null,
                'last_name'   => 'User',
                'school_id'   => '2021-2022',
                'password'    => Hash::make('password123'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Program Head account
        $admin = User::firstOrCreate(
            ['email' => 'program-head@example.com'],
            [
                'first_name'  => 'Program',
                'middle_name' => 'Head',
                'last_name'   => 'User',
                'school_id'   => '2022-2023',
                'password'    => Hash::make('password123'),
            ]
        );
        $admin->assignRole($adminRole);

        // Alumni account
        $alumni = User::firstOrCreate(
            ['email' => 'alumni@example.com'],
            [
                'first_name'  => 'Alumni',
                'middle_name' => null,
                'last_name'   => 'User',
                'school_id'   => '2023-2024',
                'password'    => Hash::make('password123'),
            ]
        );
        $alumni->assignRole($alumniRole);
    }
}