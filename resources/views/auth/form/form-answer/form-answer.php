<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\TracerStudy;
use App\Models\CivilStatusEmployment;
use App\Models\FurtherStudy;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1 — Personal Information
    public string $gender = '';
    public string $phone_number_1 = '';
    public string $current_address = '';
    public ?float $latitude = null;
    public ?float $longitude = null;

    // Step 2 — Civil Status & Program
    public string $civil_status = '';
    public $course_id = '';
    public $batch_id = '';

    // Step 3 — Employment Data
    public string $employment_status = '';
    public string $current_job_position = '';
    public string $employed_related_to_degree = '';
    public string $employment_type = '';
    public string $organization_type = '';
    public string $employment_area = '';
    public string $abroad_country = '';
    public string $months_to_first_job = '';

    // Step 4 — Further Studies
    public bool $is_pursued_further_studies = false;
    public string $level_of_study = '';

    public $courses = [];
    public $batches = [];

    public function mount()
    {
        $this->courses = Course::orderBy('course_title')->pluck('course_title', 'id');
        $this->batches = Batch::orderBy('batch_name', 'desc')->pluck('batch_name', 'id');

        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($profile) {
            $this->gender = $profile->gender ?? '';
            $this->phone_number_1 = $profile->phone_number_1 ?? '';
            $this->current_address = $profile->current_address ?? '';
            $this->batch_id = $profile->batch_id ?? '';
        }

        $tracerStudy = TracerStudy::where('user_id', $user->id)->first();

        if ($tracerStudy) {
            $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();
            if ($employment) {
                $this->civil_status = $employment->civil_status ?? '';
                $this->employment_status = $employment->employment_status ?? '';
                $this->current_job_position = $employment->current_job_position ?? '';
                $this->employed_related_to_degree = $employment->employed_related_to_degree ?? '';
                $this->employment_type = $employment->employment_type ?? '';
                $this->organization_type = $employment->organization_type ?? '';
                $this->employment_area = $employment->employment_area ?? '';
                $this->abroad_country = $employment->abroad_country ?? '';
                $this->months_to_first_job = $employment->months_to_first_job ?? '';
            }

            $further = FurtherStudy::where('tracer_study_id', $tracerStudy->id)->first();
            if ($further) {
                $this->is_pursued_further_studies = (bool) $further->is_pursued_further_studies;
                $this->level_of_study = $further->level_of_study ?? '';
            }
        }
    }

    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'gender' => 'required|in:Male,Female',
                'phone_number_1' => 'required|string|max:20',
                'current_address' => 'required|string|max:500',
            ],
            2 => [
                'civil_status' => 'required|in:single,married,widowed,separated,single-parent',
                'course_id' => 'required|exists:courses,id',
                'batch_id' => 'required|exists:batches,id',
            ],
            3 => [
                'employment_status' => 'required|in:employed,unemployed,self-employed,other',
                'current_job_position' => 'nullable|string|max:255',
                'employed_related_to_degree' => 'nullable|in:yes,no,partially-related',
                'employment_type' => 'nullable|in:full-time,part-time,contractual-project-based,freelance,other',
                'organization_type' => 'nullable|in:private-company,government-agency,non-government-organization,educational-institution,self-employed-business,other',
                'employment_area' => 'nullable|in:philippines,abroad',
                'abroad_country' => 'nullable|required_if:employment_area,abroad|string|max:255',
                'months_to_first_job' => 'nullable|in:1-3-months,4-6-months,more-than-6-months,more-than-1-year,not-yet-employed',
            ],
            4 => [
                'is_pursued_further_studies' => 'boolean',
                'level_of_study' => 'nullable|required_if:is_pursued_further_studies,true|in:Bachelor,Master,Certificate,Post Doctorate',
            ],
            default => [],
        };
    }

    public function nextStep()
    {
        // existing validation...
        $this->step++;
        $this->dispatch('step-changed');
    }

    public function previousStep()
    {
        $this->step--;
        $this->dispatch('step-changed');
    }

    public function submit()
    {
        $rules = array_merge(
            $this->stepRules(1),
            $this->stepRules(2),
            $this->stepRules(3),
            $this->stepRules(4),
        );

        $this->validate($rules);

        $user = Auth::user();

        DB::transaction(function () use ($user) {
            $existingProfile = UserProfile::where('user_id', $user->id)->first();

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar' => $existingProfile->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
                    'location' => array_merge(
                        $existingProfile->location ?? [],
                        [
                            'gender' => $this->gender,
                            'phone_number_1' => $this->phone_number_1,
                            'address' => $this->current_address,
                            'latitude' => $this->latitude,
                            'longitude' => $this->longitude,
                        ]
                    ),
                    'batch_id' => $this->batch_id,
                    'is_private' => $existingProfile->is_private ?? false,
                    'is_verified' => $existingProfile->is_verified ?? false,
                ]
            );

            $profile->courses()->sync([$this->course_id]);

            $tracerStudy = TracerStudy::firstOrCreate(['user_id' => $user->id]);

            CivilStatusEmployment::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'civil_status' => $this->civil_status,
                    'employment_status' => $this->employment_status,
                    'current_job_position' => $this->current_job_position ?: null,
                    'employed_related_to_degree' => $this->employed_related_to_degree ?: null,
                    'employment_type' => $this->employment_type ?: null,
                    'organization_type' => $this->organization_type ?: null,
                    'employment_area' => $this->employment_area ?: null,
                    'abroad_country' => $this->abroad_country ?: null,
                    'months_to_first_job' => $this->months_to_first_job ?: null,
                ]
            );

            FurtherStudy::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'is_pursued_further_studies' => $this->is_pursued_further_studies,
                    'level_of_study' => $this->is_pursued_further_studies ? $this->level_of_study : null,
                ]
            );
        });

        session()->flash('status', 'Thank you! Please wait for the admin to approve your form.');

        return redirect()->route('verification.pending');
    }
};
