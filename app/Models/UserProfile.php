<?php

namespace App\Models;

use App\Models\Batch;
use App\Models\BoardExam;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'email_notifications',
    ];

    protected $casts = [
        'location'            => 'array',
        'is_private'          => 'boolean',
        'is_verified'         => 'boolean',
        'is_approved'         => 'boolean',
        'email_notifications' => 'boolean',
        'board_taken'         => 'date',
        'board_rate'          => 'decimal:2',
    ];

    /** Passing rate threshold (%), tweak to match PRC rules per program. */
    public const BOARD_PASSING_RATE = 75.00;

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

    /** All board exam attempts, newest first. */
    public function boardExams(): HasMany
    {
        return $this->hasMany(BoardExam::class, 'user_profile_id')
            ->orderByDesc('date_taken')
            ->orderByDesc('attempt_number');
    }

    // =========================================================
    //  BOARD EXAM HELPERS
    // =========================================================

    public function latestBoardExam(): ?BoardExam
    {
        return $this->boardExams()->first();
    }

    public function bestPassingBoardExam(): ?BoardExam
    {
        return $this->boardExams()->passed()->orderByDesc('rate')->first();
    }

    /**
     * The attempt that represents this alumni in list views / reports.
     * Prefer the best VERIFIED passing attempt → latest verified →
     * best unverified passing → latest unverified.
     */
    public function featuredBoardExam(): ?BoardExam
    {
        return $this->boardExams()->verified()->passed()->orderByDesc('rate')->first()
            ?? $this->boardExams()->verified()->orderByDesc('date_taken')->first()
            ?? $this->boardExams()->passed()->orderByDesc('rate')->first()
            ?? $this->boardExams()->orderByDesc('date_taken')->first();
    }

    /**
     * Mirror the featured attempt back into board_taken / board_rate so
     * every existing query in the app keeps working unchanged.
     *
     * Does NOT touch is_verified — that's handled by syncVerificationStatus().
     */
    public function syncPrimaryBoardAttempt(): void
    {
        $featured = $this->featuredBoardExam();

        $this->forceFill([
            'board_taken' => $featured?->date_taken,
            'board_rate'  => $featured?->rate,
        ])->saveQuietly();
    }

    /**
     * Cached mirror of "does this profile have at least one verified PASSING
     * board attempt?".
     *
     * Note: an attempt can be verified (registrar confirms the data matches
     * PRC records) but still FAILED — that does NOT promote the profile.
     * The public "Verified Board Passer" badge, the board data visibility,
     * and the congrats email all key off this flag.
     */
    public function syncVerificationStatus(): void
    {
        $hasVerifiedPassing = $this->boardExams()
            ->where('is_verified', true)
            ->where('passed', true)
            ->exists();

        if ((bool) $this->is_verified !== $hasVerifiedPassing) {
            $this->forceFill(['is_verified' => $hasVerifiedPassing])->saveQuietly();
        }
    }

    /** Convenience: sync both mirrors at once. */
    public function syncBoardMirrors(): void
    {
        $this->syncPrimaryBoardAttempt();
        $this->syncVerificationStatus();
    }

    /**
     * Record a new board exam attempt in the history.
     *
     * - No attempt yet → creates attempt #1 (unverified).
     * - Same date+rate as the latest attempt → metadata refresh only;
     *   verification state of that attempt is preserved.
     * - Different date/rate → creates the next numbered attempt (unverified).
     */
    public function recordBoardAttempt(
        ?string $date,
        $rate,
        ?string $examName = null,
        ?bool $passed = null,
        ?bool $isTopNotcher = null,
        ?int $topNotcherRank = null,
        ?string $remarks = null,
    ): ?BoardExam {
        if (blank($date) || $rate === null || $rate === '') {
            return null;
        }

        $rate    = round((float) $rate, 2);
        $passed  = $passed ?? ($rate >= self::BOARD_PASSING_RATE);
        $latest  = $this->boardExams()->first();

        // Same attempt as the latest one? Don't duplicate — and DO NOT touch
        // its verification flags (keeps an already-verified attempt verified).
        if ($latest
            && $latest->date_taken?->format('Y-m-d') === $date
            && (float) $latest->rate === $rate) {

            $latest->update([
                'exam_name'        => $examName ?? $latest->exam_name,
                'passed'           => $passed,
                'is_top_notcher'   => $isTopNotcher ?? $latest->is_top_notcher,
                'top_notcher_rank' => $topNotcherRank ?? $latest->top_notcher_rank,
                'remarks'          => $remarks ?? $latest->remarks,
            ]);

            $this->syncBoardMirrors();

            return $latest;
        }

        $nextAttempt = ((int) $this->boardExams()->max('attempt_number')) + 1;

        $exam = $this->boardExams()->create([
            'exam_name'        => $examName,
            'attempt_number'   => $nextAttempt,
            'date_taken'       => $date,
            'rate'             => $rate,
            'passed'           => $passed,
            'is_verified'      => false,   // new attempts always start unverified
            'verified_at'      => null,
            'is_top_notcher'   => $isTopNotcher ?? false,
            'top_notcher_rank' => $topNotcherRank,
            'remarks'          => $remarks,
        ]);

        $this->syncBoardMirrors();

        return $exam;
    }

    public function hasVerifiedBoardAttempt(): bool
    {
        return $this->boardExams()->where('is_verified', true)->exists();
    }

    /**
     * True only when at least one VERIFIED PASSING attempt exists.
     * Same logic as the mirror stored on `is_verified`.
     */
    public function hasVerifiedPassingBoardAttempt(): bool
    {
        return $this->boardExams()
            ->where('is_verified', true)
            ->where('passed', true)
            ->exists();
    }

    public function hasUnverifiedBoardAttempt(): bool
    {
        return $this->boardExams()->where('is_verified', false)->exists();
    }

    public function isTopNotcher(): bool
    {
        return $this->boardExams()->topNotchers()->exists();
    }

    public function bestTopNotcherRank(): ?int
    {
        return $this->boardExams()
            ->topNotchers()
            ->whereNotNull('top_notcher_rank')
            ->min('top_notcher_rank');
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

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
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

    public function wantsEmailNotifications(): bool
    {
        return $this->email_notifications ?? true;
    }
}