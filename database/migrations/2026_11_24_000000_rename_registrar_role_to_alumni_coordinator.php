<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameRole('registrar', 'alumni coordinator');
        $this->renameSeededAccount('Registrar', 'Alumni Coordinator');
        $this->renameEmailTemplates();
        $this->forgetCaches();
    }

    public function down(): void
    {
        $this->renameSeededAccount('Alumni Coordinator', 'Registrar');
        $this->renameRole('alumni coordinator', 'registrar');
        $this->renameEmailTemplates(reverse: true);
        $this->forgetCaches();
    }

    /**
     * The default service account was originally named after the role.
     * Keep its login email; only the display name changes.
     */
    protected function renameSeededAccount(string $from, string $to): void
    {
        DB::table('users')
            ->where('email', 'registrar@csav.edu.ph')
            ->where('first_name', $from)
            ->where('last_name', 'CSAV')
            ->update([
                'first_name' => $to,
                'name' => $to.' CSAV',
                'updated_at' => now(),
            ]);
    }

    /**
     * Rename a Spatie role while preserving user assignments and permissions.
     *
     * If both names already exist, the old role is merged into the new one
     * before being removed.
     */
    protected function renameRole(string $from, string $to): void
    {
        $old = DB::table('roles')
            ->where('name', $from)
            ->where('guard_name', 'web')
            ->first();

        if (! $old) {
            return;
        }

        $new = DB::table('roles')
            ->where('name', $to)
            ->where('guard_name', 'web')
            ->first();

        if (! $new) {
            DB::table('roles')->where('id', $old->id)->update([
                'name' => $to,
                'updated_at' => now(),
            ]);

            return;
        }

        // Both names exist: carry assignments and permissions over, then drop
        // the old row.
        foreach (DB::table('model_has_roles')->where('role_id', $old->id)->get() as $assignment) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $new->id,
                'model_type' => $assignment->model_type,
                'model_id' => $assignment->model_id,
            ]);
        }
        DB::table('model_has_roles')->where('role_id', $old->id)->delete();

        foreach (DB::table('role_has_permissions')->where('role_id', $old->id)->get() as $permission) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permission->permission_id,
                'role_id' => $new->id,
            ]);
        }
        DB::table('role_has_permissions')->where('role_id', $old->id)->delete();

        DB::table('roles')->where('id', $old->id)->delete();
    }

    /**
     * Email templates store their slug/subject/message inside a JSON column.
     */
    protected function renameEmailTemplates(bool $reverse = false): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        $slugMap = $reverse
            ? [
                'welcome-alumni-coordinator' => 'welcome-registrar',
                'password-reset-by-alumni-coordinator' => 'password-reset-by-registrar',
            ]
            : [
                'welcome-registrar' => 'welcome-alumni-coordinator',
                'password-reset-by-registrar' => 'password-reset-by-alumni-coordinator',
            ];

        $textMap = $reverse
            ? [
                'ALUMNI COORDINATOR' => 'REGISTRAR',
                'Alumni Coordinator' => 'Registrar',
                'alumni coordinator' => 'registrar',
            ]
            : [
                'REGISTRAR' => 'ALUMNI COORDINATOR',
                'Registrar' => 'Alumni Coordinator',
                "registrar's office" => "alumni coordinator's office",
                'registrar office' => "alumni coordinator's office",
                'registrar' => 'alumni coordinator',
            ];

        foreach (DB::table('email_templates')->orderBy('id')->get() as $row) {
            $template = json_decode($row->template, true);

            if (! is_array($template)) {
                continue;
            }

            $changed = false;

            foreach (['slug', 'subject', 'message'] as $field) {
                if (! isset($template[$field]) || ! is_string($template[$field])) {
                    continue;
                }

                $original = $template[$field];
                $value = $original;

                if ($field === 'slug') {
                    $value = $slugMap[$value] ?? $value;
                }

                $value = str_replace(array_keys($textMap), array_values($textMap), $value);

                // Fix the article that the rename leaves behind:
                // "A Alumni Coordinator" -> "An Alumni Coordinator".
                $value = $reverse
                    ? str_replace(
                        ['An Alumni Coordinator', 'an Alumni Coordinator'],
                        ['A Registrar', 'a Registrar'],
                        $value
                    )
                    : str_replace(
                        ['A Alumni Coordinator', 'a Alumni Coordinator'],
                        ['An Alumni Coordinator', 'an Alumni Coordinator'],
                        $value
                    );

                if ($value !== $original) {
                    $template[$field] = $value;
                    $changed = true;
                }
            }

            if ($changed) {
                DB::table('email_templates')->where('id', $row->id)->update([
                    'template' => json_encode($template, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected function forgetCaches(): void
    {
        Cache::forget('registrar_user_ids');
        Cache::forget('alumni_coordinator_user_ids');
        Cache::forget('roles:name-list');
        Cache::forget('roles:editable-list:v2');

        if (app()->bound(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
};
