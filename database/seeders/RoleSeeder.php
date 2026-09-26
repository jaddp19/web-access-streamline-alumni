<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Base permissions. Spatie stores these as rows in the
     * `permissions` table — each gets an auto-increment `id`
     * and the `name` you see here.
     */
    protected array $permissions = [
        'can_view_any',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
    ];

    /**
     * Which permissions each role receives.
     * Keys are role names, values are arrays of permission names.
     */
    protected array $rolePermissions = [
        'registrar'    => [
            'can_view_any',
            'can_view',
            'can_create',
            'can_update',
            'can_delete',
        ],
        'program head' => [
            'can_view',
            'can_create',
            'can_update',
        ],
        'alumni'       => [
            'can_view',
            'can_create',
            'can_update',
        ],
    ];

    public function run(): void
    {
        // Spatie caches roles + permissions aggressively. Without this,
        // a cached (empty or stale) list can make firstOrCreate() look
        // like it succeeded but later findByName() still throws.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Permissions ────────────────────────────────────
        foreach ($this->permissions as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        // ── 2. Roles ──────────────────────────────────────────
        foreach (array_keys($this->rolePermissions) as $name) {
            Role::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        // ── 3. Role → Permission mapping ──────────────────────
        // syncPermissions() is idempotent: it REPLACES the role's
        // permissions with the given list. Re-running this seeder
        // won't create duplicates.
        foreach ($this->rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                $role->syncPermissions($perms);
            }
        }

        // Clear again so the next process sees the freshly written rows.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}