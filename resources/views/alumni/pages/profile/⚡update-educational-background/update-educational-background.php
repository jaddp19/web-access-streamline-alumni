<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public ?int $batch_id = null;
    public ?int $course_id = null;
    public bool $is_public = true;

    protected function rules()
    {
        return [
            'batch_id'  => 'required|exists:batches,id',
            'course_id' => 'required|exists:courses,id',
            'is_public' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'batch_id.required' => 'Please select your batch.',
            'batch_id.exists'   => 'Selected batch is invalid.',
            'course_id.required'=> 'Please select your degree program.',
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
        }

        $this->is_public = ! $profile->is_private;
    }

    public function update()
    {
        $validated = $this->validate();

        $user    = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (! $profile) {
            session()->flash('error', 'No profile found. Please complete your personal information first.');
            return redirect()->route('alumni.profile.update', $user->id);
        }

        try {
            $profile->update([
                'batch_id'   => $validated['batch_id'],
                'is_private' => ! $validated['is_public'],
            ]);

            $profile->courses()->sync([$validated['course_id']]);

            session()->flash('success', 'Educational Background updated successfully.');
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
