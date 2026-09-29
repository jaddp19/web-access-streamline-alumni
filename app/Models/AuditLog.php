<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    /**
     * Audit logs are immutable — only `created_at`, no `updated_at`.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'auditable_type',
        'auditable_id',
        'action',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    /** The user who performed the action (nullable — user may be deleted later). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The model that was affected (User, Department, Course, Post, etc.). */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    // =========================================================
    //  SCOPES
    // =========================================================

    public function scopeForModel($query, string $type, int $id)
    {
        return $query->where('auditable_type', $type)
                     ->where('auditable_id', $id);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // =========================================================
    //  ACCESSORS
    // =========================================================

    /** Pre-formatted summary string, e.g. "Maria Santos updated a User". */
    public function getSummaryAttribute(): string
    {
        $who  = $this->user_name ?? 'System';
        $what = class_basename($this->auditable_type);

        return match ($this->action) {
            'created'  => "{$who} created a {$what}",
            'updated'  => "{$who} updated a {$what}",
            'deleted'  => "{$who} deleted a {$what}",
            'restored' => "{$who} restored a {$what}",
            default    => "{$who} performed {$this->action} on a {$what}",
        };
    }

    /**
     * Human-readable label pulled out of the stored changes JSON.
     *
     * Handles both shapes:
     *   • created / deleted → flat scalars: {"title": "Foo"}
     *   • updated          → {old, new} pairs: {"title": {"old": "Foo", "new": "Bar"}}
     *
     * Returns null when nothing usable is present, so Blade can fall back
     * to "ModelName #id".
     */
    public function getSubjectLabelAttribute(): ?string
    {
        $changes = $this->changes ?? [];

        if (empty($changes)) {
            return null;
        }

        // First matching field wins. Order roughly by specificity.
        $candidates = [
            'title',         // Post, Event
            'post_title',
            'event_title',
            'course_title',  // Course
            'company_name',  // Company
            'dept_name',     // Department
            'batch_name',    // Batch
            'work_name',     // WorkHistory
            'name',          // User, Role
        ];

        foreach ($candidates as $field) {
            if (! array_key_exists($field, $changes)) {
                continue;
            }

            $value = $changes[$field];

            // updated shape: {old, new}
            if (is_array($value)) {
                $resolved = $value['new'] ?? $value['old'] ?? null;

                if (filled($resolved)) {
                    return Str::limit((string) $resolved, 60);
                }

                continue;
            }

            // created / deleted shape: flat scalar
            if (filled($value)) {
                return Str::limit((string) $value, 60);
            }
        }

        // EmailTemplate stores its label inside a nested JSON blob.
        if (isset($changes['template']) && is_string($changes['template'])) {
            $decoded = json_decode($changes['template'], true);

            if (is_array($decoded) && filled($decoded['subject'] ?? null)) {
                return Str::limit((string) $decoded['subject'], 60);
            }
        }

        return null;
    }
}