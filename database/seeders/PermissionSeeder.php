<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    protected array $defaultPermission = [
        'can_view_any',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
    ];

    public function run(): void
    {
        // Clear Spatie's cached permission list BEFORE writing —
        // without this, a stale cache can make hasPermissionTo()
        // throw even after firstOrCreate() inserts the row.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->defaultPermission as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Forget again so the next process reads from DB.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}   