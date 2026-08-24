<?php

use App\Models\AlumniProfile;
use App\Models\Batch;
use App\Models\DegreeProgram;
use App\Models\EducationalBackground;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public ?int $batch_year = null;
    public ?int $degree_program_id = null;
    public bool $is_public = true;

    protected function rules()
    {
        return [
            'batch_year'         => 'required|integer|digits:4|min:1950|max:' . (date('Y') + 1),
            'degree_program_id'  => 'required|exists:degree_programs,id',
            'is_public'          => 'boolean',
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
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $profile = AlumniProfile::where('user_id', $user->id)->first();

        // No personal profile yet — send them there first instead of crashing.
        if (! $profile) {
            session()->flash('error', 'Please complete your personal information first.');
            $this->redirect(route('alumni.profile.update', $user->id));
            return;
        }

        $educationalBackground = EducationalBackground::where('alumni_profile_id', $profile->id)->first();

        if ($educationalBackground) {
            $this->batch_year        = $educationalBackground->batch->batch_year ?? null;
            $this->degree_program_id = $educationalBackground->degree_program_id;
            $this->is_public         = $educationalBackground->is_public;
        }
    }

    public function update()
    {
        $validated = $this->validate();

        $validated['degree_program_id'] = $this->sanitizeNumeric($validated['degree_program_id']);

        $user = Auth::user();
        $profile = $user->alumniProfile;

        if (! $profile) {
            session()->flash('error', 'No alumni profile found. Please complete your personal information first.');
            return redirect()->route('alumni.profile.update', $user->id);
        }

        // Find the batch matching the typed year, or create it if it doesn't exist yet.
        $batch = Batch::firstOrCreate(['batch_year' => $validated['batch_year']]);

        $profile->educationalBackground()->updateOrCreate(
            [],
            [
                'batch_id'          => $batch->id,
                'degree_program_id' => $validated['degree_program_id'],
                'is_public'         => $validated['is_public'],
            ]
        );

        session()->flash('success', 'Educational Background updated successfully.');
        return redirect()->route('alumni.profile');
    }

    protected function sanitizeNumeric($data)
    {
        return $data !== null ? (int) preg_replace('/\D/', '', $data) : null;
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed]
    public function alumniProfile()
    {
        return Auth::user()->alumniProfile;
    }

    #[Computed]
    public function degreePrograms()
    {
        return DegreeProgram::orderBy('program_name')->get();
    }
};
