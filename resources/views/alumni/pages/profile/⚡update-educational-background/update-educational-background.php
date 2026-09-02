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
    public ?int $batch_year = null;
    public ?int $course_id = null;
    public bool $is_public = true;

    protected function rules()
    {
        return [
            'batch_year' => 'required|integer|digits:4|min:1950|max:' . (date('Y') + 1),
            'course_id'  => 'required|exists:courses,id',
            'is_public'  => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'batch_year.required' => 'Please enter your batch year.',
            'batch_year.integer'  => 'Batch year must be a number.',
            'batch_year.digits'   => 'Batch year must be a 4-digit year (e.g. 2026).',
            'batch_year.min'      => 'Batch year must be a valid year.',
            'batch_year.max'      => 'Batch year cannot be in the future beyond next year.',
            'course_id.required'  => 'Please select your degree program.',
        ];
    }

    public function mount()
    {
        $user    = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        // No personal profile yet — send them there first instead of crashing.
        if (! $profile) {
            session()->flash('error', 'Please complete your personal information first.');
            $this->redirect(route('alumni.profile.update', $user->id));
            return;
        }

        if ($profile->batch) {
            $year = (int) preg_replace('/\D/', '', $profile->batch->batch_name);
            $this->batch_year = $year > 0 ? $year : null;
        }

        $existingCourse = $profile->courses()->first();
        if ($existingCourse) {
            $this->course_id = $existingCourse->id;
        }

        $this->is_public = ! $profile->is_private;
    }

    public function pickYear(int $year)
    {
        $this->batch_year = $year;
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
            // Find the batch matching the typed year, or create it if it doesn't exist yet.
            $batch = Batch::firstOrCreate(['batch_name' => (string) $validated['batch_year']]);

            $profile->update([
                'batch_id'   => $batch->id,
                'is_private' => ! $validated['is_public'],
            ]);

            // One course per profile for now — swap sync() for attach() if you need multiple.
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

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed]
    public function courses()
    {
        return Course::with('department')->where('is_active', true)->orderBy('course_title')->get();
    }

    #[Computed]
    public function selectedCourse()
    {
        if (! $this->course_id) {
            return null;
        }

        return $this->courses->firstWhere('id', (int) $this->course_id);
    }

    #[Computed]
    public function recentYears()
    {
        $current = (int) date('Y');
        return range($current, $current - 4);
    }
};
