<?php

namespace Database\Seeders;

use App\Models\User;
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
         // Create roles if not already seeded
            $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $alumniRole = Role::firstOrCreate(['name' => 'alumni']);

            // Admin account
            $superAdmin = User::firstOrCreate(
                ['email' => 'super-admin@example.com'],
                [
                    'name' => 'Super Admin User',
                    'password' => Hash::make('password123'),
                ]
            );
            $superAdmin->assignRole($superAdminRole);

            // Shop Owner account
            $admin = User::firstOrCreate(
                ['email' => 'owner@example.com'],
                [
                    'name' => 'Shop Owner User',
                    'password' => Hash::make('password123'),
                ]
            );
            $admin->assignRole($adminRole);

            // Employee account
            $alumni = User::firstOrCreate(
                ['email' => 'employee@example.com'],
                [
                    'name' => 'Employee User',
                    'password' => Hash::make('password123'),
                ]
            );
            $alumni->assignRole($alumniRole);
        }
}
