<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public $batch_id = '';

    public $course_id = '';

    public bool $is_public = true;

    public ?string $board_taken = null;

    public ?string $board_rate = '';

    // Snapshot of original values for change detection
    public ?string $original_board_taken = null;

    public string $original_board_rate = '';

    public ?int $original_course_id = null;

    /** Earliest allowed board exam date. */
    public const BOARD_EXAM_MIN_DATE = '2015-01-01';

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Is the currently-selected course a board program?
     * Reads directly from the DB so we never depend on a memoized
     * computed or a stale property.
     */
    protected function targetIsBoardCourse(): bool
    {
        if (! $this->course_id) {
            return false;
        }

        return Course::query()
            ->whereKey($this->course_id)
            ->value('course_type') === 'board';
    }

    /**
     * Update the latest pending board exam in place, or create one if none
     * exists yet. Used whenever the alumni is unverified — they're still
     * refining their submission, so no new history row should be created.
     */
    protected function updateOrCreatePendingExam(UserProfile $profile, array $validated, ?string $examName): void
    {
        $pendingExam = $profile->boardExams()
            ->where('is_verified', false)
            ->reorder()
            ->latest('id')
            ->first();

        $payload = [
            'exam_name'  => $examName,
            'date_taken' => $validated['board_taken'] ?: null,
            'rate'       => filled($validated['board_rate'])
                ? round((float) $validated['board_rate'], 2)
                : null,
            'passed'     => filled($validated['board_rate'])
                ? ((float) $validated['board_rate'] >= UserProfile::BOARD_PASSING_RATE)
                : false,
        ];

        if ($pendingExam) {
            $pendingExam->update($payload);
        } else {
            // No pending row yet → this is the first submission.
            $profile->recordBoardAttempt(
                $validated['board_taken'] ?: null,
                $validated['board_rate'] ?: null,
                $examName
            );
        }
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        // Board rules ONLY apply when the target course is a board program.
        $isBoard = $this->targetIsBoardCourse();

        return [
            'batch_id'  => ['required', 'integer', Rule::exists('batches', 'id')],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'is_public' => ['boolean'],

            'board_taken' => $isBoard ? [
                'nullable',
                'date',
                'after_or_equal:'.self::BOARD_EXAM_MIN_DATE,
                'before_or_equal:today',
                Rule::requiredIf(fn () => filled($this->board_rate)),
            ] : ['nullable'],

            'board_rate' => $isBoard ? [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                Rule::requiredIf(fn () => filled($this->board_taken)),
            ] : ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'batch_id.required' => 'Please select your batch.',
            'batch_id.exists' => 'Selected batch is invalid.',
            'course_id.required' => 'Please select your degree program.',
            'course_id.exists' => 'Selected degree program is invalid.',

            'board_taken.date' => 'Board exam date must be a valid date.',
            'board_taken.after_or_equal' => 'Board exam date cannot be earlier than Jan 1, 2015.',
            'board_taken.before_or_equal' => 'Board exam date cannot be in the future.',
            'board_taken.required' => 'Please provide the board exam date — it is required when a rating is entered.',

            'board_rate.numeric' => 'Board rating must be a number.',
            'board_rate.min' => 'Board rating cannot be less than 0.',
            'board_rate.max' => 'Board rating cannot be more than 100.',
            'board_rate.required' => 'Please provide your board rating — it is required when a date is entered.',
        ];
    }

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(): void
    {
        Gate::authorize('can_update');
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'Please complete your personal information first.');
            $this->redirect(route('alumni.profile.update', $user->id));

            return;
        }

        $this->batch_id = $profile->batch_id ?? '';

        $existingCourse = $profile->courses()->first();
        if ($existingCourse) {
            $this->course_id = $existingCourse->id;
            $this->original_course_id = $existingCourse->id;
        }

        $this->is_public = ! $profile->is_private;

        $this->board_taken = $profile->board_taken
            ? Carbon::parse($profile->board_taken)->format('Y-m-d')
            : null;

        $this->board_rate = $profile->board_rate !== null
            ? (string) $profile->board_rate
            : '';

        $this->original_board_taken = $this->board_taken;
        $this->original_board_rate = $this->board_rate ?? '';
    }

    // =========================================================
    //  HOOKS
    // =========================================================

    /** When switching to a non-board course, clear the board fields. */
    public function updatedCourseId(): void
    {
        unset($this->selectedCourse);

        if (! $this->targetIsBoardCourse()) {
            $this->board_taken = null;
            $this->board_rate = null;
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

    public function updatedBoardTaken(): void
    {
        if (filled($this->board_taken)) {
            $this->resetErrorBag('board_rate');
        }
        if (blank($this->board_taken) && blank($this->board_rate)) {
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

    public function updatedBoardRate(): void
    {
        if (filled($this->board_rate)) {
            $this->resetErrorBag('board_taken');
        }
        if (blank($this->board_taken) && blank($this->board_rate)) {
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function update()
    {
        Gate::authorize('can_update');

        if (! $this->targetIsBoardCourse()) {
            $this->board_taken = null;
            $this->board_rate = null;
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }

        $validated = $this->validate();

        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'No profile found. Please complete your personal information first.');

            return redirect()->route('alumni.profile.update', $user->id);
        }

        $courseId = (int) $validated['course_id'];
        $batchId  = (int) $validated['batch_id'];

        $selectedCourse = Course::query()
            ->select('id', 'course_title', 'course_type', 'department_id')
            ->find($courseId);

        $isBoardCourse = $selectedCourse?->course_type === 'board';
        $examName      = $selectedCourse?->course_title;

        $wasBoardCourse = $this->original_course_id
            ? (Course::whereKey($this->original_course_id)->value('course_type') === 'board')
            : false;

        $courseChanged = $courseId !== (int) $this->original_course_id;
        $wasVerified   = (bool) $profile->is_verified;

        // ── Build the profile payload ────────────────────────────────
        $profileData = [
            'batch_id'   => $batchId,
            'is_private' => ! $validated['is_public'],
        ];

        if ($isBoardCourse) {
            $profileData['board_taken'] = $validated['board_taken'] ?: null;
            $profileData['board_rate']  = filled($validated['board_rate'])
                ? round((float) $validated['board_rate'], 2)
                : null;
        } else {
            $profileData['board_taken'] = null;
            $profileData['board_rate']  = null;
        }

        if ($courseChanged) {
            $profileData['is_approved']           = false;
            $profileData['last_rejection_reason'] = null;
        }

        // ── Did the alumni change their board details? ───────────────
        $boardChanged = $this->board_taken !== $this->original_board_taken
            || round((float) ($this->board_rate ?? 0), 2) !== round((float) ($this->original_board_rate ?? 0), 2);

        try {
            DB::transaction(function () use (
                $profile, $profileData, $courseId, $validated,
                $isBoardCourse, $wasBoardCourse, $courseChanged, $examName,
                $boardChanged, $wasVerified
            ) {
                $profile->update($profileData);

                if (! $isBoardCourse) {
                    // ══════════════════════════════════════════════════
                    //  TARGET IS NON-BOARD → wipe everything
                    // ══════════════════════════════════════════════════
                    $profile->boardExams()->reorder()->delete();
                    $profile->syncBoardMirrors();

                    $profile->forceFill([
                        'board_taken' => null,
                        'board_rate'  => null,
                        'is_verified' => true,
                    ])->saveQuietly();
                } else {
                    // ══════════════════════════════════════════════════
                    //  TARGET IS BOARD
                    // ══════════════════════════════════════════════════

                    if ($courseChanged && $wasBoardCourse) {
                        // board → DIFFERENT board: reset every existing attempt
                        // then treat the submission as "unverified edit in place".
                        $profile->boardExams()->reorder()->update([
                            'is_verified'      => false,
                            'verified_at'      => null,
                            'is_top_notcher'   => false,
                            'top_notcher_rank' => null,
                        ]);

                        $this->updateOrCreatePendingExam($profile, $validated, $examName);

                        $profile->syncBoardMirrors();
                        $profile->forceFill(['is_verified' => false])->saveQuietly();
                    } elseif ($courseChanged && ! $wasBoardCourse) {
                        // non-board → board: fresh start
                        $profile->boardExams()->reorder()->delete();

                        $profile->recordBoardAttempt(
                            $validated['board_taken'] ?: null,
                            $validated['board_rate'] ?: null,
                            $examName
                        );

                        $profile->forceFill(['is_verified' => false])->saveQuietly();
                    } else {
                        // ── No course change ─────────────────────────────
                        if ($wasVerified) {
                            // Verified alumni editing → create a NEW history row,
                            // then flip is_verified back to false for re-review.
                            if ($boardChanged) {
                                $profile->recordBoardAttempt(
                                    $validated['board_taken'] ?: null,
                                    $validated['board_rate'] ?: null,
                                    $examName
                                );

                                $profile->forceFill(['is_verified' => false])->saveQuietly();
                            }
                        } else {
                            // Unverified alumni editing → update the pending row
                            // IN PLACE. No new history row is created.
                            $this->updateOrCreatePendingExam($profile, $validated, $examName);
                            $profile->syncBoardMirrors();
                        }
                    }
                }

                $profile->courses()->sync([$courseId]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not save changes. Please try again.');

            return;
        }

        // Refresh snapshots so a second save has the correct baseline.
        $this->original_board_taken = $this->board_taken;
        $this->original_board_rate = $this->board_rate ?? '';
        $this->original_course_id = $courseId;

        unset($this->selectedCourse, $this->courses);

        session()->flash('success', match (true) {
            ! $isBoardCourse
                => 'Educational background updated successfully.',

            $courseChanged
                => 'Course updated. Your board exam details are now pending verification.',

            $wasVerified && $boardChanged && ($this->board_taken || $this->board_rate !== '')
                => 'Educational background updated. A new board attempt has been recorded — please wait for the registrar to verify it.',

            ! $wasVerified && $boardChanged && ($this->board_taken || $this->board_rate !== '')
                => 'Your board exam details have been updated — please wait for the registrar to verify them.',

            $boardChanged
                => 'Educational background updated. Board exam details cleared.',

            default
                => 'Educational background updated successfully.',
        });

        return redirect()->route('alumni.profile');
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function courses()
    {
        return Course::query()
            ->select('id', 'course_title', 'course_type', 'department_id')
            ->with('department:id,dept_name')
            ->where('is_active', true)
            ->orderBy('course_title')
            ->get();
    }

    #[Computed]
    public function batches()
    {
        return Batch::query()
            ->select('id', 'batch_name')
            ->orderBy('batch_name')
            ->get();
    }

    #[Computed]
    public function selectedCourse(): ?Course
    {
        if (! $this->course_id) {
            return null;
        }

        return Course::query()
            ->select('id', 'course_title', 'course_type', 'department_id')
            ->with('department:id,dept_name')
            ->find($this->course_id);
    }
};