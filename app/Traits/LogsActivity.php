<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            $excluded = $model->auditExclude ?? [];
            $changes  = collect($model->getAttributes())
                ->except(array_merge($excluded, ['created_at', 'updated_at']))
                ->all();

            static::writeAuditLog($model, 'created', $changes);
        });

        static::updated(function (Model $model) {
            $excluded = $model->auditExclude ?? [];

            $changes = collect($model->getChanges())
                ->except(array_merge($excluded, ['updated_at']))
                ->mapWithKeys(fn ($new, $field) => [
                    $field => [
                        'old' => $model->getOriginal($field),
                        'new' => $new,
                    ],
                ])
                ->all();

            if (! empty($changes)) {
                static::writeAuditLog($model, 'updated', $changes);
            }
        });

        static::deleted(function (Model $model) {
            $excluded = $model->auditExclude ?? [];
            $changes  = collect($model->getOriginal())
                ->except(array_merge($excluded, ['created_at', 'updated_at']))
                ->all();

            static::writeAuditLog($model, 'deleted', $changes);
        });
    }

    protected static function writeAuditLog(Model $model, string $action, array $changes): void
    {
        AuditLog::create([
            'user_id'        => auth()->id(),
            'user_name'      => auth()->user()?->name,
            'auditable_type' => $model::class,
            'auditable_id'   => $model->getKey(),
            'action'         => $action,
            'changes'        => $changes ?: null,
            'ip_address'     => request()->ip(),
            'user_agent'     => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}