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
            $changes = collect($model->getAttributes())
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
            $changes = collect($model->getOriginal())
                ->except(array_merge($excluded, ['created_at', 'updated_at']))
                ->all();

            static::writeAuditLog($model, 'deleted', $changes);
        });
    }

    public static function deleteWithAudit($query): int
    {
        $actor = auth()->user();
        $excluded = (new static)->auditExclude ?? [];
        $count = 0;

        $query->clone()->chunkById(200, function ($models) use ($actor, $excluded, &$count) {
            $now = now();
            $rows = $models->map(fn ($m) => [
                'user_id' => $actor?->id,
                'user_name' => $actor?->name,
                'auditable_type' => $m::class,
                'auditable_id' => $m->getKey(),
                'action' => 'deleted',
                'changes' => json_encode(
                    collect($m->getAttributes())
                        ->except(array_merge($excluded, ['created_at', 'updated_at']))
                        ->all()
                ),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
                'created_at' => $now,
            ])->all();

            AuditLog::insert($rows);

            static::whereKey($models->modelKeys())->delete();
            $count += $models->count();
        });

        return $count;
    }

    protected static function writeAuditLog(Model $model, string $action, array $changes): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'changes' => $changes ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}
