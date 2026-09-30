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
    public ?int $batch_id = null;

    public ?int $course_id = null;

    public bool $is_public = true;

    public ?string $board_taken = null;

    public string $board_rate = '';

    // Snapshot of original values for change detection
    public ?string $original_board_taken = null;

    public string $original_board_rate = '';

    public ?int $original_course_id = null;

    /** Earliest allowed board exam date. */
    public const BOARD_EXAM_MIN_DATE = '2015-01-01';

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'is_public' => ['boolean'],

            'board_taken' => [
                'nullable',
                'date',
                'after_or_equal:'.self::BOARD_EXAM_MIN_DATE,
                'before_or_equal:today',
                Rule::requiredIf(fn () => filled($this->board_rate)),
            ],

            'board_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                Rule::requiredIf(fn () => filled($this->board_taken)),
            ],
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

        $this->batch_id = $profile->batch_id;

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
        $this->original_board_rate = $this->board_rate;
    }

    // =========================================================
    //  HOOKS
    // =========================================================

    /** When switching to a non-board course, clear the board fields. */
    public function updatedCourseId(): void
    {
        if ($this->selectedCourse?->course_type !== 'board') {
            $this->board_taken = null;
            $this->board_rate = '';
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
        $validated = $this->validate();

        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'No profile found. Please complete your personal information first.');

            return redirect()->route('alumni.profile.update', $user->id);
        }

        $isBoardCourse = $this->selectedCourse?->course_type === 'board';
        $examName = $this->selectedCourse?->course_title;

        // Snapshot the previous course's type BEFORE we sync the new course.
        $wasBoardCourse = $this->original_course_id
            ? (Course::whereKey($this->original_course_id)->value('course_type') === 'board')
            : false;

        // ===== Build payload =====
        $profileData = [
            'batch_id'   => $validated['batch_id'],
            'is_private' => ! $validated['is_public'],
        ];

        if ($isBoardCourse) {
            $profileData['board_taken'] = $validated['board_taken'] ?: null;
            $profileData['board_rate'] = filled($validated['board_rate'])
                ? round((float) $validated['board_rate'], 2)
                : null;
        } else {
            $profileData['board_taken'] = null;
            $profileData['board_rate'] = null;
            $profileData['is_verified'] = true;   // non-board programs don't need board verification
        }
        // For board programs we do NOT touch is_verified here —
        // recordBoardAttempt() / syncBoardMirrors() computes it from board_exams.

        // ===== Did the alumni change anything that matters? =====
        $boardChanged = $this->board_taken !== $this->original_board_taken
            || round((float) $this->board_rate, 2) !== round((float) $this->original_board_rate, 2);

        try {
            DB::transaction(function () use (
                $profile, $profileData, $validated,
                $isBoardCourse, $examName
            ) {
                $profile->update($profileData);

                if (! $isBoardCourse) {
                    // Switched to a non-board program → board history is gone.
                    $profile->boardExams()->delete();
                } else {
                    // Record the attempt. recordBoardAttempt():
                    //  - dedupes identical date+rate submissions,
                    //  - creates a new numbered attempt otherwise,
                    //  - preserves verification of previously verified rows,
                    //  - calls syncBoardMirrors() internally.
                    $profile->recordBoardAttempt(
                        $validated['board_taken'] ?: null,
                        $validated['board_rate'] ?: null,
                        $examName
                    );
                }

                $profile->courses()->sync([$validated['course_id']]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not save changes. Please try again.');

            return;
        }

        // Refresh snapshots for a second save
        $this->original_board_taken = $this->board_taken;
        $this->original_board_rate = $this->board_rate;
        $this->original_course_id = (int) $this->course_id;

        unset($this->selectedCourse, $this->courses);

        session()->flash('success', match (true) {
            ! $isBoardCourse
                => 'Educational background updated successfully.',

            $isBoardCourse && $boardChanged && ($this->board_taken || $this->board_rate !== '')
                => 'Educational background updated. A new board attempt has been recorded — please wait for the registrar to verify it.',

            $isBoardCourse && $boardChanged
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

    /**
     * The course the user currently has selected.
     * Loaded directly — NOT filtered by is_active — so that a user
     * assigned to a since-deactivated course still resolves to the
     * right `course_type` (board vs non-board).
     */
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