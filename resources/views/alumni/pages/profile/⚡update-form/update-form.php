<?php

use App\Models\Batch;
use App\Models\Company;
use App\Models\Course;
use App\Models\TracerStudy;
use App\Models\CivilStatusEmployment;
use App\Models\FurtherStudy;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Propaganistas\LaravelPhone\Rules\Phone;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1 — Personal (now real columns)
    public string $gender = '';
    public string $contact_number_1 = '';
    public string $contact_number_2 = '';
    public bool $consentGiven = false;


    // Step 1 — Address (stays in location JSON)
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $barangayCode = '';
    public string $street_address = '';

    // Step 1 — Coordinates (location JSON)
    public float $latitude = 10.45;
    public float $longitude = 123.88;

    // Step 2
    public string $civil_status = '';
    public $course_id = '';
    public $batch_id = '';

    // Step 3 — Employment
    public string $employment_status = '';
    public string $current_job_position = '';
    public string $employed_related_to_degree = '';
    public string $employment_type = '';
    public string $organization_type = '';
    public string $employment_area = '';
    public string $abroad_country = '';
    public string $months_to_first_job = '';

    // Step 3 — Company
    public ?int $company_id = null;
    public string $date_hired = '';

    // Step 3 — Inline new company
    public bool $showNewCompanyForm = false;
    public $new_company_logo = null;
    public string $new_company_name = '';
    public string $new_company_address = '';
    public string $new_company_desc = '';

    // Step 4
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
            // --- Columns ---
            $this->gender           = $profile->gender ? ucfirst($profile->gender) : '';
            $this->contact_number_1 = $profile->contact_number_1 ?? '';
            $this->contact_number_2 = $profile->contact_number_2 ?? '';
            $this->consentGiven     = (bool) $profile->consent_given_at;
            $this->batch_id         = $profile->batch_id ?? '';

            // --- course_id from pivot ---
            $this->course_id = $profile->courses()->value('courses.id') ?? '';

            // --- location JSON ---
            $location = $profile->location ?? [];

            $this->street_address = $location['street_address'] ?? '';
            $this->regionCode     = $location['region_code'] ?? '';
            $this->provinceCode   = $location['province_code'] ?? '';
            $this->cityCode       = $location['city_code'] ?? '';
            $this->barangayCode   = $location['barangay_code'] ?? '';
            $this->latitude       = (float) ($location['latitude'] ?? 10.45);
            $this->longitude      = (float) ($location['longitude'] ?? 123.88);
        }

        $tracerStudy = TracerStudy::where('user_id', $user->id)->first();

        if ($tracerStudy) {
            $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();
            if ($employment) {
                $this->civil_status               = $employment->civil_status ?? '';
                $this->employment_status          = $employment->employment_status ?? '';
                $this->current_job_position       = $employment->current_job_position ?? '';
                $this->employed_related_to_degree = $employment->employed_related_to_degree ?? '';
                $this->employment_type            = $employment->employment_type ?? '';
                $this->organization_type          = $employment->organization_type ?? '';
                $this->employment_area            = $employment->employment_area ?? '';
                $this->abroad_country             = $employment->abroad_country ?? '';
                $this->months_to_first_job        = $employment->months_to_first_job ?? '';
            }

            $further = FurtherStudy::where('tracer_study_id', $tracerStudy->id)->first();
            if ($further) {
                $this->is_pursued_further_studies = (bool) $further->is_pursued_further_studies;
                $this->level_of_study             = $further->level_of_study ?? '';
            }
        }

        $currentWork = WorkHistory::where('user_id', $user->id)
            ->where('is_current_job', true)
            ->first();

        if ($currentWork) {
            $this->company_id = $currentWork->company_id;
            $this->date_hired = $currentWork->date_hired?->format('Y-m-d') ?? '';
        }
    }

    // ===== Cascading address =====

    public function updatedRegionCode()
    {
        $this->provinceCode = '';
        $this->cityCode = '';
        $this->barangayCode = '';
    }

    public function updatedProvinceCode()
    {
        $this->cityCode = '';
        $this->barangayCode = '';
    }

    public function updatedCityCode()
    {
        $this->barangayCode = '';
    }

    // ===== Computed =====

    #[Computed]
    public function regions()
    {
        return app(PhAddressService::class)->regions();
    }

    #[Computed]
    public function provinces()
    {
        return $this->regionCode
            ? app(PhAddressService::class)->provinces($this->regionCode)
            : collect();
    }

    #[Computed]
    public function cities()
    {
        return $this->provinceCode
            ? app(PhAddressService::class)->cities($this->provinceCode)
            : collect();
    }

    #[Computed]
    public function barangays()
    {
        return $this->cityCode
            ? app(PhAddressService::class)->barangays($this->cityCode)
            : collect();
    }

    #[Computed]
    public function companies()
    {
        return Company::orderBy('company_name')
            ->get(['id', 'company_name', 'company_logo', 'company_address']);
    }

    // ===== Inline company creation =====

    public function toggleNewCompanyForm(): void
    {
        $this->showNewCompanyForm = ! $this->showNewCompanyForm;

        if (! $this->showNewCompanyForm) {
            $this->reset(['new_company_logo', 'new_company_name', 'new_company_address', 'new_company_desc']);
            $this->resetErrorBag(['new_company_logo', 'new_company_name', 'new_company_address', 'new_company_desc']);
        } else {
            $this->company_id = null;
        }
    }

    public function updatedEmploymentStatus(): void
    {
        // When switching away from "employed", clear all employment-only fields
        if ($this->employment_status !== 'employed') {
            $this->reset([
                'current_job_position',
                'company_id',
                'date_hired',
                'employed_related_to_degree',
                'employment_type',
                'organization_type',
                'employment_area',
                'abroad_country',
            ]);

            $this->resetErrorBag([
                'current_job_position',
                'company_id',
                'date_hired',
                'employed_related_to_degree',
                'employment_type',
                'organization_type',
                'employment_area',
                'abroad_country',
            ]);

            // Also close the inline new-company form if it was open
            if ($this->showNewCompanyForm) {
                $this->showNewCompanyForm = false;
                $this->reset(['new_company_logo', 'new_company_name', 'new_company_address', 'new_company_desc']);
            }
        }
    }

    public function createCompany(): void
    {
        $this->validate([
            'new_company_name'    => 'required|string|max:255|unique:companies,company_name',
            'new_company_logo'    => 'nullable|image|max:2048',
            'new_company_address' => 'nullable|string|max:500',
            'new_company_desc'    => 'nullable|string|max:2000',
        ]);

        try {
            $logoPath = null;
            if ($this->new_company_logo) {
                $logoPath = $this->new_company_logo->store('companies', 'public');
            }

            $company = Company::create([
                'company_name'    => trim($this->new_company_name),
                'company_address' => trim($this->new_company_address) ?: null,
                'company_logo'    => $logoPath,
                'company_desc'    => trim($this->new_company_desc) ?: null,
            ]);

            $this->company_id         = $company->id;
            $this->showNewCompanyForm = false;

            $this->reset(['new_company_logo', 'new_company_name', 'new_company_address', 'new_company_desc']);
            unset($this->companies);

            session()->flash('company_created', 'Company "' . $company->company_name . '" created and selected.');
        } catch (\Throwable $e) {
            logger()->error('Company creation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    // ===== Validation rules =====

    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'gender'           => 'required|in:Male,Female,Other',
                'contact_number_1' => ['required', 'string', 'max:20', new Phone('PH')],
                'contact_number_2' => ['nullable', 'string', 'max:20', new Phone('PH')],
                'street_address'   => 'required|string|max:500',
                'regionCode'       => 'required|string',
                'provinceCode'     => 'required|string',
                'cityCode'         => 'required|string',
                'barangayCode'     => 'required|string',
                'latitude'         => 'required|numeric',
                'longitude'        => 'required|numeric',
                'consentGiven'     => 'accepted',
            ],
            2 => [
                'civil_status' => 'required|in:single,married,widowed,separated,single-parent',
                'course_id'    => 'required|exists:courses,id',
                'batch_id'     => 'required|exists:batches,id',
            ],
            3 => [
                'employment_status'          => 'required|in:employed,unemployed,self-employed,other',
                'current_job_position'       => 'required_if:employment_status,employed|nullable|string|max:255',
                'company_id'                 => 'required_if:employment_status,employed|nullable|exists:companies,id',
                'date_hired'                 => 'required_if:employment_status,employed|nullable|date|before_or_equal:today',
                'employed_related_to_degree' => 'required_if:employment_status,employed|nullable|in:yes,no,partially-related',
                'employment_type'            => 'required_if:employment_status,employed|nullable|in:full-time,part-time,contractual-project-based,freelance,other',
                'organization_type'          => 'required_if:employment_status,employed|nullable|in:private-company,government-agency,non-government-organization,educational-institution,self-employed-business,other',
                'employment_area'            => 'required_if:employment_status,employed|nullable|in:philippines,abroad',
                'abroad_country'             => 'required_if:employment_area,abroad|nullable|string|max:255',
                'months_to_first_job'        => 'required_if:employment_status,employed|nullable|in:1-3-months,4-6-months,more-than-6-months,more-than-1-year',
            ],
            4 => [
                'is_pursued_further_studies' => 'required|boolean',
                'level_of_study'             => 'required_if:is_pursued_further_studies,true|nullable|in:Certificate,Bachelor,Master,Post Doctorate',
            ],
            default => [],
        };
    }

    protected function stepMessages(): array
    {
        return [
            'gender.required'                        => 'Please select your sex.',
            'contact_number_1.required'              => 'Mobile number is required.',
            'contact_number_2.required'              => 'Alternate number is required.',
            'street_address.required'                => 'Street address is required.',
            'regionCode.required'                    => 'Please select your region.',
            'provinceCode.required'                  => 'Please select your province.',
            'cityCode.required'                      => 'Please select your city/municipality.',
            'barangayCode.required'                  => 'Please select your barangay.',
            'consentGiven.accepted'                  => 'You must agree to the Privacy Policy and Terms and Conditions before continuing.',
            'latitude.required'                      => 'Please pin your location on the map.',
            'longitude.required'                     => 'Please pin your location on the map.',
            'civil_status.required'                  => 'Please select your civil status.',
            'course_id.required'                     => 'Please select your program.',
            'batch_id.required'                      => 'Please select your year graduated.',
            'employment_status.required'             => 'Please select your employment status.',
            'current_job_position.required_if'       => 'Job position is required.',
            'company_id.required_if'                 => 'Please select a company.',
            'date_hired.required_if'                 => 'Please enter the date you were hired.',
            'employed_related_to_degree.required_if' => 'Please answer if your job is related to your degree.',
            'employment_type.required_if'            => 'Please select type of employment.',
            'organization_type.required_if'          => 'Please select type of organization.',
            'employment_area.required_if'            => 'Please select employment area.',
            'abroad_country.required_if'             => 'Please specify the country.',
            'months_to_first_job.required_if'        => 'Please select how long it took to get your first job.',
            'is_pursued_further_studies.required'    => 'Please answer the further studies question.',
            'level_of_study.required_if'             => 'Please select the level of study.',
        ];
    }

    // ===== Navigation =====

    public function nextStep()
    {
        $this->validate($this->stepRules($this->step), $this->stepMessages());

        if ($this->step < $this->totalSteps) {
            $this->step++;
            $this->dispatch('step-changed');
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->resetErrorBag();
            $this->dispatch('step-changed');
        }
    }

    // ===== Submit =====

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
        $service = app(PhAddressService::class);

        $region   = $service->findByCode($this->regionCode);
        $province = $service->findByCode($this->provinceCode);
        $city     = $service->findByCode($this->cityCode);
        $barangay = $service->findByCode($this->barangayCode);

        $fullAddress = collect([
            $this->street_address,
            $barangay->name ?? null,
            $city->name ?? null,
            $province->name ?? null,
            $region->name ?? null,
        ])->filter()->implode(', ');



        DB::transaction(function () use ($user, $region, $province, $city, $barangay, $fullAddress) {
            $existingProfile = UserProfile::where('user_id', $user->id)->first();

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar'           => $existingProfile->avatar ?? null,
                    'gender'           => strtolower($this->gender), // enum: male|female|other
                    'contact_number_1' => $this->contact_number_1,
                    'contact_number_2' => $this->contact_number_2 ?: null,
                    'location' => array_merge(
                        $existingProfile->location ?? [],
                        [
                            'street_address' => $this->street_address,
                            'region_code'    => $this->regionCode,
                            'region_name'    => $region->name ?? null,
                            'province_code'  => $this->provinceCode,
                            'province_name'  => $province->name ?? null,
                            'city_code'      => $this->cityCode,
                            'city_name'      => $city->name ?? null,
                            'barangay_code'  => $this->barangayCode,
                            'barangay_name'  => $barangay->name ?? null,
                            'address'        => $fullAddress,
                            'latitude'       => $this->latitude,
                            'longitude'      => $this->longitude,
                        ]
                    ),
                    'batch_id'    => $this->batch_id,
                    'is_private'  => $existingProfile->is_private ?? false,
                    'is_verified' => $existingProfile->is_verified ?? false,
                ]
            );

            $profile->courses()->sync([$this->course_id]);

            $tracerStudy = TracerStudy::firstOrCreate(['user_id' => $user->id]);

            $isEmployed = $this->employment_status === 'employed';


            CivilStatusEmployment::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'civil_status'      => $this->civil_status,
                    'employment_status' => $this->employment_status,

                    // Only persist employment details when the alumnus is employed.
                    // Everything else gets wiped so a switch from employed → unemployed
                    // doesn't leave stale data in the record.
                    'current_job_position'       => $isEmployed ? ($this->current_job_position ?: null) : null,
                    'employed_related_to_degree' => $isEmployed ? ($this->employed_related_to_degree ?: null) : null,
                    'employment_type'            => $isEmployed ? ($this->employment_type ?: null) : null,
                    'organization_type'          => $isEmployed ? ($this->organization_type ?: null) : null,
                    'employment_area'            => $isEmployed ? ($this->employment_area ?: null) : null,
                    'abroad_country'             => $isEmployed && $this->employment_area === 'abroad'
                        ? ($this->abroad_country ?: null)
                        : null,
                    'months_to_first_job' => $this->months_to_first_job ?: null,
                ]
            );

            FurtherStudy::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'is_pursued_further_studies' => $this->is_pursued_further_studies,
                    'level_of_study'             => $this->is_pursued_further_studies ? $this->level_of_study : null,
                ]
            );
            // ===== Work history =====
            if ($this->employment_status === 'employed' && $this->company_id && $this->date_hired) {

                // Update or create the current job record
                $existingCurrent = WorkHistory::where('user_id', $user->id)
                    ->where('is_current_job', true)
                    ->first();

                if ($existingCurrent) {
                    $existingCurrent->update([
                        'work_name'  => $this->current_job_position ?: 'Position not specified',
                        'company_id' => $this->company_id,
                        'date_hired' => $this->date_hired,
                    ]);
                } else {
                    WorkHistory::create([
                        'user_id'        => $user->id,
                        'work_name'      => $this->current_job_position ?: 'Position not specified',
                        'company_id'     => $this->company_id,
                        'date_hired'     => $this->date_hired,
                        'is_current_job' => true,
                    ]);
                }
            } else {
                // Not employed (unemployed / self-employed / other)
                // → unset the current-job flag on any existing record,
                //   but keep the record itself so past jobs are preserved.
                WorkHistory::where('user_id', $user->id)
                    ->where('is_current_job', true)
                    ->update(['is_current_job' => false]);
            }
        });

        session()->flash('status', 'Your tracer study has been updated successfully.');

        return redirect()->route('alumni.dashboard');
    }
};
