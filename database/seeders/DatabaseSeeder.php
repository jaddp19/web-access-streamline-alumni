<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            PermissionSeeder::class,
        ]);
        // Create roles if not already seeded
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $alumniRole = Role::firstOrCreate(['name' => 'alumni']);

        // Admin account
        $superAdmin = User::firstOrCreate(
            ['email' => 'super-admin@example.com'],
            [
                'name' => 'Super Admin User',
                'school_id' => '2021-2022', // Add a school_id for the super-admin
                'password' => Hash::make('password123'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'school_id' => '2022-2023', // Add a school_id for the admin
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole($adminRole);

        // Alumni account
        $alumni = User::firstOrCreate(
            ['email' => 'alumni@example.com'],
            [
                'name' => 'Alumni User',
                'school_id' => '2023-2024', // Add a school_id for the alumni
                'password' => Hash::make('password123'),
            ]
        );
        $alumni->assignRole($alumniRole);
    }
}
