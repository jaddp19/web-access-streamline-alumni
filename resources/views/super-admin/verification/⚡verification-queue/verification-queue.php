<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Department;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $rejectReasonInput = '';

    public ?int $rejectingUserId = null;

    /**
     * Registrar-entered top notcher ranks, keyed by board_exam id.
     * e.g. [12 => 3, 15 => null] means attempt #12 is Top 3, #15 is not a top notcher.
     */
    public array $topNotcherRank = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // =========================================================
    //  SCOPE
    // =========================================================

    #[Computed]
    public function scope(): int|false|null
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('registrar')) {
            return null;
        }

        return Department::where('program_head_id', $user->id)->value('id') ?? false;
    }

    #[Computed]
    public function hasNoDepartment(): bool
    {
        return $this->scope === false;
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        return is_int($this->scope)
            ? Department::whereKey($this->scope)->value('dept_name')
            : null;
    }

    // =========================================================
    //  PENDING USERS
    // =========================================================

    #[Computed]
    public function pendingUsers()
    {
        if ($this->scope === false) {
            return User::query()->whereRaw('1 = 0')->paginate(10);
        }

        $scope = $this->scope;

        return User::role('alumni')
            ->whereHas('userProfile', function ($q) use ($scope) {
                $q->whereHas('boardExams', fn ($b) => $b->where('is_verified', false))
                    ->whereHas('courses', function ($c) use ($scope) {
                        $c->where('course_type', 'board');

                        if (is_int($scope)) {
                            $c->where('department_id', $scope);
                        }
                    });
            })
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('school_id', 'like', $term);
                });
            })
            ->with([
                'userProfile.batch:id,batch_name',
                'userProfile.courses:id,course_title,course_type,department_id',
                'userProfile.courses.department:id,dept_name',
                'userProfile.boardExams' => fn ($q) => $q->orderByDesc('date_taken'),
            ])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function rejectingUserName(): ?string
    {
        if (! $this->rejectingUserId) {
            return null;
        }

        return User::query()->whereKey($this->rejectingUserId)->value('name');
    }

    // =========================================================
    //  APPROVE
    // =========================================================

    public function approve(int $userId): void
    {
        $scope = $this->scope;
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        // Validate any top notcher ranks the registrar typed in.
        $this->validate([
            'topNotcherRank'   => 'array',
            'topNotcherRank.*' => 'nullable|integer|min:1|max:100',
        ], [
            'topNotcherRank.*.integer' => 'Top notcher rank must be a whole number.',
            'topNotcherRank.*.min'     => 'Top notcher rank must be at least 1.',
            'topNotcherRank.*.max'     => 'Top notcher rank cannot exceed 100.',
        ]);

        $user = User::with('userProfile.courses:id,course_title,course_type,department_id')->find($userId);

        if (! $user) {
            session()->flash('status', 'User not found.');

            return;
        }

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        $profile = $user->userProfile;

        if (! $profile) {
            session()->flash('status', 'This alumni has no profile.');

            return;
        }

        // Snapshot the ranks the registrar entered (keyed by board_exam id).
        $rankInput = $this->topNotcherRank;

        try {
            DB::transaction(function () use ($user, $profile, $rankInput) {
                $lockedProfile = $profile->newQuery()->whereKey($profile->id)->lockForUpdate()->first();

                if (! $lockedProfile) {
                    return;
                }

                $user->syncRoles(['alumni']);

                // Legacy fallback: profile has board data but no attempt row.
                if (
                    $lockedProfile->board_taken
                    && $lockedProfile->board_rate !== null
                    && $lockedProfile->boardExams()->count() === 0
                ) {
                    $lockedProfile->loadMissing('courses:id,course_title,course_type,department_id');

                    $lockedProfile->recordBoardAttempt(
                        $lockedProfile->board_taken->format('Y-m-d'),
                        (float) $lockedProfile->board_rate,
                        $lockedProfile->courses->firstWhere('course_type', 'board')?->course_title
                    );
                }

                // Apply per-attempt verification + top notcher rank.
                $pendingAttempts = $lockedProfile->boardExams()
                    ->where('is_verified', false)
                    ->get();

                foreach ($pendingAttempts as $attempt) {
                    $rawRank = $rankInput[$attempt->id] ?? null;
                    $rank    = is_numeric($rawRank) ? (int) $rawRank : null;
                    $isTop   = $rank !== null && $rank > 0;

                    $attempt->update([
                        'is_verified'      => true,
                        'verified_at'      => now(),
                        'is_top_notcher'   => $isTop,
                        'top_notcher_rank' => $isTop ? $rank : null,
                    ]);
                }

                // Sync the profile's cached mirror.
                $lockedProfile->syncBoardMirrors();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('status', 'Could not approve user. Please try again.');

            return;
        }

        // Reload so we read the fresh is_verified flag after syncBoardMirrors().
        $profile->refresh();

        // ─── Conditional email ─────────────────────────────────────
        try {
            if ($profile->is_verified) {
                EmailTemplateService::send('profile-approved', $user->email, [
                    'name'        => $user->name,
                    'approved_at' => now()->format('F j, Y · g:i A'),
                    'approved_by' => Auth::user()?->name ?? 'the registrar',
                    'login_url'   => route('login'),
                ]);
            } else {
                EmailTemplateService::send('profile-verified-not-passer', $user->email, [
                    'name'        => $user->name,
                    'verified_at' => now()->format('F j, Y · g:i A'),
                    'verified_by' => Auth::user()?->name ?? 'the registrar',
                    'login_url'   => route('login'),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Approval email failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
        // ────────────────────────────────────────────────────────────

        // Clear the input state so the next alumni starts fresh.
        $this->topNotcherRank = [];

        Cache::forget('verification:pending-count');

        session()->flash('status', match (true) {
            $profile->is_verified
                => "{$user->name} has been verified as a board passer.",
            default
                => "{$user->name}'s board records were verified, but no passing attempt was found — the profile stays unverified publicly.",
        });
    }

    // =========================================================
    //  REJECT
    // =========================================================

    public function openRejectModal(int $userId): void
    {
        $this->rejectingUserId = $userId;
        $this->rejectReasonInput = '';
        $this->resetErrorBag('rejectReasonInput');
    }

    public function closeRejectModal(): void
    {
        $this->rejectingUserId = null;
        $this->rejectReasonInput = '';
        $this->resetErrorBag('rejectReasonInput');
    }

    public function confirmReject(): void
    {
        $scope = $this->scope;
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        $this->validate([
            'rejectReasonInput' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $this->rejectingUserId) {
            session()->flash('status', 'No user selected.');

            return;
        }

        $user = User::with('userProfile')->find($this->rejectingUserId);

        if (! $user) {
            session()->flash('status', 'User not found.');
            $this->closeRejectModal();

            return;
        }

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        $profile = $user->userProfile;

        if (! $profile) {
            session()->flash('status', 'This user has no profile to reject.');
            $this->closeRejectModal();

            return;
        }

        $reason = trim($this->rejectReasonInput);

        try {
            DB::transaction(function () use ($profile) {
                // Drop only unverified attempts (the new submission).
                $profile->boardExams()->where('is_verified', false)->delete();

                // Recompute the mirrors.
                $profile->syncBoardMirrors();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('status', 'Could not reject user. Please try again.');

            return;
        }

        try {
            EmailTemplateService::send('alumni-verification-rejected', $user->email, [
                'name' => $user->name,
                'reason' => $reason !== ''
                    ? $reason
                    : 'No specific reason was provided by the reviewer.',
                'login_url' => route('login'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Rejection email failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Cache::forget('verification:pending-count');

        session()->flash('status', "{$user->name}'s application was rejected.");

        $this->closeRejectModal();
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function userBelongsToDepartment(User $user, int $departmentId): bool
    {
        if (! $user->relationLoaded('userProfile')) {
            $user->load('userProfile.courses:id,course_type,department_id');
        }

        return $user->userProfile
            && $user->userProfile->courses->contains('department_id', $departmentId);
    }
};