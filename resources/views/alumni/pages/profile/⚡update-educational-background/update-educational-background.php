<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public ?int $batch_id = null;
    public ?int $course_id = null;
    public bool $is_public = true;

    // Board exam fields — optional even for board programs
    // (alumni may not have taken the exam yet)
    public ?string $board_taken = null;
    public string $board_rate = '';

    // Snapshot of original values for change detection
    public ?string $original_board_taken = null;
    public string $original_board_rate = '';
    public ?int $original_course_id = null;

    protected function rules()
    {
        return [
            'batch_id'    => 'required|exists:batches,id',
            'course_id'   => 'required|exists:courses,id',
            'is_public'   => 'boolean',
            'board_taken' => 'nullable|date|before_or_equal:today',
            'board_rate'  => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'batch_id.required'           => 'Please select your batch.',
            'batch_id.exists'             => 'Selected batch is invalid.',
            'course_id.required'          => 'Please select your degree program.',
            'course_id.exists'            => 'Selected degree program is invalid.',
            'board_taken.date'            => 'Board exam date must be a valid date.',
            'board_taken.before_or_equal' => 'Board exam date cannot be in the future.',
            'board_rate.numeric'          => 'Board rating must be a number.',
            'board_rate.min'              => 'Board rating cannot be less than 0.',
            'board_rate.max'              => 'Board rating cannot be more than 100.',
        ];
    }

    public function mount()
    {
        $user    = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'Please complete your personal information first.');
            $this->redirect(route('alumni.profile.update', $user->id));
            return;
        }

        if ($profile->batch) {
            $this->batch_id = $profile->batch->id;
        }

        $existingCourse = $profile->courses()->first();
        if ($existingCourse) {
            $this->course_id = $existingCourse->id;
            $this->original_course_id = $existingCourse->id;
        }

        $this->is_public = ! $profile->is_private;

        $this->board_taken = $profile->board_taken
            ? \Carbon\Carbon::parse($profile->board_taken)->format('Y-m-d')
            : null;

        $this->board_rate = $profile->board_rate !== null
            ? (string) $profile->board_rate
            : '';

        // Snapshot originals
        $this->original_board_taken = $this->board_taken;
        $this->original_board_rate  = $this->board_rate;
    }

    /**
     * When switching to a non-board course, clear the board fields.
     */
    public function updatedCourseId(): void
    {
        if ($this->selectedCourse?->course_type !== 'board') {
            $this->board_taken = null;
            $this->board_rate  = '';
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

    public function update()
    {
        // Board fields are always nullable — no conditional "required" rules
        $validated = $this->validate($this->rules());

        $user    = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'No profile found. Please complete your personal information first.');
            return redirect()->route('alumni.profile.update', $user->id);
        }

        try {
            $isBoardCourse = $this->selectedCourse?->course_type === 'board';

            // ===== Detect changes =====
            $boardChanged = $this->board_taken !== $this->original_board_taken
                || round((float) $this->board_rate, 2) !== round((float) $this->original_board_rate, 2);

            $courseChanged = (int) $this->course_id !== (int) $this->original_course_id;

            // ===== Build update payload =====
            $profileData = [
                'batch_id'   => $validated['batch_id'],
                'is_private' => ! $validated['is_public'],
            ];

            if ($isBoardCourse) {
                // Save whatever the user provided (both can be null)
                $profileData['board_taken'] = $validated['board_taken'] ?: null;
                $profileData['board_rate']  = ($validated['board_rate'] !== '' && $validated['board_rate'] !== null)
                    ? round((float) $validated['board_rate'], 2)
                    : null;
            } else {
                // Non-board course — wipe board exam data
                $profileData['board_taken'] = null;
                $profileData['board_rate']  = null;
            }

            // ===== Verification reset logic =====
            // Reset is_verified when:
            //   • Course switched to non-board
            //   • Board data changed (added, edited, or cleared)
            //   • Course program changed while still on a board program
            $mustResetVerification = ! $isBoardCourse
                || ($isBoardCourse && ($boardChanged || $courseChanged));

            if ($mustResetVerification) {
                $profileData['is_verified'] = false;
            }

            // ===== Persist =====
            $profile->update($profileData);
            $profile->courses()->sync([$validated['course_id']]);

            // Update snapshot
            $this->original_board_taken = $this->board_taken;
            $this->original_board_rate  = $this->board_rate;
            $this->original_course_id   = (int) $this->course_id;

            // ===== Flash message =====
            if (! $isBoardCourse) {
                session()->flash('success', 'Educational Background updated successfully.');
            } elseif ($mustResetVerification && ($this->board_taken || $this->board_rate !== '')) {
                session()->flash('success', 'Educational Background updated. Your board exam details changed — please wait for the registrar to re-verify your submission.');
            } elseif ($mustResetVerification) {
                session()->flash('success', 'Educational Background updated. Board exam details cleared.');
            } else {
                session()->flash('success', 'Educational Background updated successfully.');
            }

            return redirect()->route('alumni.profile');

        } catch (\Throwable $e) {
            logger()->error('Educational background save failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while saving: ' . $e->getMessage());
            return;
        }
    }

    #[Computed]
    public function courses()
    {
        return Course::with('department')->where('is_active', true)->orderBy('course_title')->get();
    }

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name')->get();
    }

    #[Computed]
    public function recentYears()
    {
        $current = (int) date('Y');
        return range($current, $current - 4);
    }

    #[Computed]
    public function selectedCourse()
    {
        return $this->course_id
            ? $this->courses->firstWhere('id', (int) $this->course_id)
            : null;
    }
};