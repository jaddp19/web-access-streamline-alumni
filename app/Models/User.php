<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['first_name', 'middle_name', 'last_name', 'email', 'password', 'school_id', 'last_seen_posts_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    /** Fields to exclude from audit logs. */
    protected array $auditExclude = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty(['first_name', 'middle_name', 'last_name'])) {
                $parts = array_filter([
                    $user->first_name,
                    $user->middle_name,
                    $user->last_name,
                ]);

                if (! empty($parts)) {
                    $user->name = implode(' ', $parts);
                }
            }
        });
    }

    /**
     * Composed full name — falls back to the `name` column if parts are empty.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return $parts ? implode(' ', $parts) : ($this->name ?? '');
    }

    public const DEFAULT_PASSWORDS = [
        'csav.alumni',
        'csav.program-head',
        'csav.registrar',
    ];

    public function usesDefaultPassword(): bool
    {
        if (! $this->password) {
            return false;
        }

        // Hash::check is slow by design, and this runs on every page request,
        // so cache the result per password hash. A new password means a new
        // hash, so it gets rechecked automatically.
        return Cache::remember(
            'default-pw:'.sha1($this->password),
            now()->addHour(),
            function () {
                foreach (self::DEFAULT_PASSWORDS as $default) {
                    if (Hash::check($default, $this->password)) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    public function needsForcedPasswordChange(): bool
    {
        // Any role still using a default password
        if ($this->usesDefaultPassword()) {
            return true;
        }

        // Alumni first-time login (account never updated since creation)
        return $this->hasRole('alumni')
            && $this->created_at
            && $this->updated_at
            && $this->created_at->eq($this->updated_at);
    }

    // ===== Relations =====

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class, 'user_id', 'id');
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function tracerStudy(): HasOne
    {
        return $this->hasOne(TracerStudy::class, 'user_id', 'id');
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, 'program_head_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->latest();
    }

    // ===== Employment helpers =====

    public function currentWork(): ?WorkHistory
    {
        return $this->workHistories()->where('is_current_job', true)->first();
    }

    public function currentJobTitle(): ?string
    {
        return $this->currentWork()?->work_name
            ?? $this->tracerStudy?->civilStatusEmployment?->current_job_position;
    }

    public function isCurrentlyEmployed(): bool
    {
        return $this->currentWork() !== null
            || $this->tracerStudy?->civilStatusEmployment?->employment_status === 'employed';
    }

    public function eventRsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function eventsAttending(): HasMany
    {
        return $this->hasMany(EventRsvp::class)->where('response', 'yes');
    }
}
