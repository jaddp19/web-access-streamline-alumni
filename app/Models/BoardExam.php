<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardExam extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_profile_id',
        'exam_name',
        'attempt_number',
        'date_taken',
        'rate',
        'passed',
        'is_verified',
        'verified_at',
        'is_top_notcher',
        'top_notcher_rank',
        'remarks',
    ];

    protected $casts = [
        'date_taken'     => 'date',
        'rate'           => 'decimal:2',
        'passed'         => 'boolean',
        'is_verified'    => 'boolean',
        'verified_at'    => 'datetime',
        'is_top_notcher' => 'boolean',
    ];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class);
    }

    // =========================================================
    //  SCOPES
    // =========================================================

    public function scopePassed($q)
    {
        return $q->where('passed', true);
    }

    public function scopeTopNotchers($q)
    {
        return $q->where('is_top_notcher', true);
    }

    public function scopeVerified($q)
    {
        return $q->where('is_verified', true);
    }

    public function scopeUnverified($q)
    {
        return $q->where('is_verified', false);
    }

    // =========================================================
    //  ACCESSORS
    // =========================================================

    /** "1st attempt", "2nd attempt", "3rd attempt"… */
    public function getAttemptLabelAttribute(): string
    {
        return match ($this->attempt_number) {
            1 => '1st attempt',
            2 => '2nd attempt',
            3 => '3rd attempt',
            default => "{$this->attempt_number}th attempt",
        };
    }

    /** "Top 1", "Top 5" — null when not a top notcher. */
    public function getTopNotcherLabelAttribute(): ?string
    {
        if (! $this->is_top_notcher || ! $this->top_notcher_rank) {
            return null;
        }

        return "Top {$this->top_notcher_rank}";
    }
}