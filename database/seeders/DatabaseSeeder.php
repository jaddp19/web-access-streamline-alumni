<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // Reference data first — roles, permissions, batches, categories,
        // email templates, departments, courses, PSGC addresses.
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            CategorySeeder::class,
            BatchSeeder::class,
            EmailSeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            PhAddressSeeder::class,
            // DemoSeeder::class,
            EventSeeder::class,
            EventRsvpSeeder::class,
            PostSeeder::class,
        ]);

        // Seed the three default accounts. Roles are already created
        // by RoleSeeder, so we just assign by name.
        $this->seedDefaultAccount(
            email: 'alumni-coordinator@csav.edu.ph',
            firstName: 'Alumni Coordinator',
            lastName: 'CSAV',
            schoolId: '0001-0001',
            role: 'alumni coordinator',
        );

        $this->seedDefaultAccount(
            email: 'program-head@csav.edu.ph',
            firstName: 'Program Head',
            lastName: 'CSAV',
            schoolId: '0001-0002',
            role: 'program head',
        );

        $this->seedDefaultAccount(
            email: 'alumni@csav.edu.ph',
            firstName: 'Alumni',
            lastName: 'CSAV',
            schoolId: '0001-0003',
            role: 'alumni',
        );
    }

    /**
     * Idempotent default-account creation. Safe to re-run.
     */
    protected function seedDefaultAccount(
        string $email,
        string $firstName,
        string $lastName,
        string $schoolId,
        string $role,
    ): User {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'middle_name' => null,
                'last_name' => $lastName,
                'school_id' => $schoolId,
                'password' => Hash::make('password123'),
            ]
        );

        // syncRoles replaces any existing roles — avoids duplicates
        // if this seeder runs multiple times.
        $user->syncRoles([$role]);

        return $user;
    }
}
