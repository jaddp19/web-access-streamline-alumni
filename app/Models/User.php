<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['first_name', 'middle_name', 'last_name', 'email', 'password', 'school_id', 'last_seen_posts_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
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

    // ===== Employment helpers =====

    /**
     * The current job record — single source of truth.
     */
    public function currentWork(): ?WorkHistory
    {
        return $this->workHistories()->where('is_current_job', true)->first();
    }

    /**
     * Prefers live WorkHistory; falls back to tracer snapshot.
     */
    public function currentJobTitle(): ?string
    {
        return $this->currentWork()?->work_name
            ?? $this->tracerStudy?->civilStatusEmployment?->current_job_position;
    }

    /**
     * True if the user has a current job OR the tracer snapshot says employed.
     */
    public function isCurrentlyEmployed(): bool
    {
        return $this->currentWork() !== null
            || $this->tracerStudy?->civilStatusEmployment?->employment_status === 'employed';
    }
}