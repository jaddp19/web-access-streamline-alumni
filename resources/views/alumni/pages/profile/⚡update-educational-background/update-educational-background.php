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
    public ?int $batch_id = null;
    public ?int $degree_program_id = null;

    protected function rules()
    {
        return [
            'batch_id'          => 'required|exists:batches,id',
            'degree_program_id' => 'required|exists:degree_programs,id',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $profile = AlumniProfile::where('user_id', $user->id)->first();
        $alumniProfile = EducationalBackground::where('alumni_profile_id', $profile->id)->first();

        if ($alumniProfile) {
            $this->batch_id          = $alumniProfile->batch_id;
            $this->degree_program_id = $alumniProfile->degree_program_id;
        }
    }

    public function update()
    {
        $validated = $this->validate();

        $validated['batch_id'] = $this->sanitizeNumeric($validated['batch_id']);
        $validated['degree_program_id'] = $this->sanitizeNumeric($validated['degree_program_id']);

        $user = Auth::user();
        $profile = $user->alumniProfile;

        if (! $profile) {
            session()->flash('error', 'No alumni profile found.');
            return redirect()->route('alumni.profile');
        }

        $profile->educationalBackground()->updateOrCreate(
            [],
            [
                'batch_id'          => $validated['batch_id'],
                'degree_program_id' => $validated['degree_program_id'],
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
    public function batches()
    {
        return Batch::orderBy('batch_year')->get();
    }

    #[Computed]
    public function degreePrograms()
    {
        return DegreeProgram::orderBy('program_name')->get();
    }
};
