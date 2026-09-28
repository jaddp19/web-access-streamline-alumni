<?php

namespace App\Models;

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'location',
        'gender',
        'contact_number_1',
        'contact_number_2',
        'batch_id',
        'is_private',
        'board_taken',
        'board_rate',
        'is_verified',
        'is_approved',
        'last_rejection_reason',
    ];

    protected $casts = [
        'location'     => 'array',
        'is_private'   => 'boolean',
        'is_verified'  => 'boolean',
        'is_approved'  => 'boolean',
        'board_taken'  => 'date',
        'board_rate'   => 'decimal:2',
    ];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'student_course', 'user_profile_id', 'course_id');
    }

    // =========================================================
    //  SCOPES
    // =========================================================

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeRejected($query)
    {
        return $query->where('is_approved', false)
                     ->whereNotNull('last_rejection_reason');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    public function approvalLabel(): string
    {
        if ($this->is_approved) {
            return 'Approved';
        }

        if (filled($this->last_rejection_reason)) {
            return 'Rejected';
        }

        return 'Awaiting Review';
    }

    public function approvalColor(): string
    {
        if ($this->is_approved) {
            return 'emerald';
        }

        if (filled($this->last_rejection_reason)) {
            return 'red';
        }

        return 'amber';
    }

    public function approvalBadgeClasses(): string
    {
        return match (true) {
            $this->is_approved                   => 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
            filled($this->last_rejection_reason) => 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400',
            default                              => 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400',
        };
    }
}