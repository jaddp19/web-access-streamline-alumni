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

new #[Layout('layouts.app-form')] class extends Component
{
    use WithFileUploads;

    public bool $alreadyFilled = false;

    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1 — Personal
    public string $gender = '';
    public string $contact_number_1 = '';
    public string $contact_number_2 = '';
    public bool $consentGiven = false;

    // Step 1 — Address
    public string $address_type = 'philippines'; // 'philippines' | 'abroad'
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $barangayCode = '';
    public string $street_address = '';

    // Step 1 — International address (only used when address_type === 'abroad')
    public string $intl_country = '';
    public string $intl_state = '';
    public string $intl_city = '';

    // Step 2
    public string $civil_status = '';
    public $course_id = '';
    public $batch_id = '';

    // Step 2 — Board exam (only for board programs)
    public ?string $board_taken = null;
    public string $board_rate = '';

    // Step 3
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
        $user = Auth::user();

        // If the user already has a profile, lock the form.
        $existingProfile = UserProfile::where('user_id', $user->id)->first();

        if ($existingProfile) {
            $this->alreadyFilled = true;
            // Don't hydrate or allow saving — they must use the settings update form instead.
            return;
        }

        $this->courses = Course::orderBy('course_title')->pluck('course_title', 'id');
        $this->batches = Batch::orderBy('batch_name', 'desc')->pluck('batch_name', 'id');
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

    public function updatedAddressType($value)
    {
        $this->resetErrorBag([
            'regionCode',
            'provinceCode',
            'cityCode',
            'barangayCode',
            'intl_country',
            'intl_state',
            'intl_city',
        ]);
    }

    // ===== Course change → clear board fields if non-board =====

    public function updatedCourseId()
    {
        if ($this->selectedCourse?->course_type !== 'board') {
            $this->board_taken = null;
            $this->board_rate  = '';
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
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

    #[Computed]
    public function selectedCourse()
    {
        return $this->course_id
            ? Course::find($this->course_id)
            : null;
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

    // ===== Rules =====

    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'gender'           => 'required|in:Male,Female,Other',
                'contact_number_1' => [
                    'required',
                    'string',
                    'max:20',
                    new Phone(),
                    function ($attribute, $value, $fail) {
                        $exists = UserProfile::where(function ($q) use ($value) {
                            $q->where('contact_number_1', $value)
                                ->orWhere('contact_number_2', $value);
                        })
                            ->where('user_id', '!=', $this->userProfileId())
                            ->exists();

                        if ($exists) {
                            $fail('This mobile number is already registered to another account.');
                        }
                    },
                ],

                'contact_number_2' => [
                    'nullable',
                    'string',
                    'max:20',
                    new Phone(),
                    function ($attribute, $value, $fail) {
                        if (! $value) return; // skip if empty

                        $exists = UserProfile::where(function ($q) use ($value) {
                            $q->where('contact_number_1', $value)
                                ->orWhere('contact_number_2', $value);
                        })
                            ->where('user_id', '!=', $this->userProfileId())
                            ->exists();

                        if ($exists) {
                            $fail('This alternate number is already registered to another account.');
                        }
                    },
                ],
                'street_address'   => 'required|string|max:500',
                'address_type'     => 'required|in:philippines,abroad',

                // PH-only (required when address_type = philippines)
                'regionCode'   => 'required_if:address_type,philippines|nullable|string',
                'provinceCode' => 'required_if:address_type,philippines|nullable|string',
                'cityCode'     => 'required_if:address_type,philippines|nullable|string',
                'barangayCode' => 'required_if:address_type,philippines|nullable|string',

                // Abroad-only (required when address_type = abroad)
                'intl_country' => 'required_if:address_type,abroad|nullable|string|max:255',
                'intl_state'   => 'nullable|string|max:255',
                'intl_city'    => 'required_if:address_type,abroad|nullable|string|max:255',

                'consentGiven' => 'accepted',
            ],
            2 => [
                'civil_status' => 'required|in:single,married,widowed,separated,single-parent',
                'course_id'    => 'required|exists:courses,id',
                'batch_id'     => 'required|exists:batches,id',

                // Board fields are now OPTIONAL — only validated if filled in
                'board_taken'  => 'nullable|date|before_or_equal:today',
                'board_rate'   => 'nullable|numeric|min:0|max:100',
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
                'months_to_first_job'        => 'required_if:employment_status,employed|nullable|in:1-3-months,4-6-months,more-than-6-months,more-than-1-year,not-yet-employed',
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
            'contact_number_1.phone'                 => 'Please enter a valid mobile number.',
            'contact_number_2.phone'                 => 'Please enter a valid alternate number.',
            'contact_number_1.required'              => 'Mobile number is required.',
            'street_address.required'                => 'Street address is required.',
            'address_type.required'                  => 'Please select whether you reside in the Philippines or abroad.',
            'regionCode.required'                    => 'Please select your region.',
            'provinceCode.required'                  => 'Please select your province.',
            'cityCode.required'                      => 'Please select your city/municipality.',
            'barangayCode.required'                  => 'Please select your barangay.',
            'intl_country.required_if'               => 'Please enter your country.',
            'intl_city.required_if'                  => 'Please enter your city.',
            'consentGiven.accepted'                  => 'You must agree to the Privacy Policy and Terms and Conditions before continuing.',
            'civil_status.required'                  => 'Please select your civil status.',
            'course_id.required'                     => 'Please select your program.',
            'batch_id.required'                      => 'Please select your year graduated.',
            'board_taken.date'                       => 'Board exam date must be a valid date.',
            'board_taken.before_or_equal'            => 'Board exam date cannot be in the future.',
            'board_rate.numeric'                     => 'Board rating must be a number.',
            'board_rate.min'                         => 'Board rating cannot be less than 0.',
            'board_rate.max'                         => 'Board rating cannot be more than 100.',
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

    protected function userProfileId(): ?int
    {
        return UserProfile::where('user_id', Auth::id())->value('id');
    }

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

        $region   = $this->address_type === 'philippines' ? $service->findByCode($this->regionCode) : null;
        $province = $this->address_type === 'philippines' ? $service->findByCode($this->provinceCode) : null;
        $city     = $this->address_type === 'philippines' ? $service->findByCode($this->cityCode) : null;
        $barangay = $this->address_type === 'philippines' ? $service->findByCode($this->barangayCode) : null;

        if ($this->address_type === 'philippines') {
            $fullAddress = collect([
                $this->street_address,
                $barangay->name ?? null,
                $city->name ?? null,
                $province->name ?? null,
                $region->name ?? null,
            ])->filter()->implode(', ');
        } else {
            $fullAddress = collect([
                $this->street_address,
                $this->intl_city,
                $this->intl_state,
                $this->intl_country,
            ])->filter()->implode(', ');
        }

        DB::transaction(function () use ($user, $region, $province, $city, $barangay, $fullAddress) {
            $existingProfile = UserProfile::where('user_id', $user->id)->first();

            $isBoardCourse = $this->selectedCourse?->course_type === 'board';

            $location = $this->address_type === 'philippines'
                ? array_merge($existingProfile->location ?? [], [
                    'address_type'   => 'philippines',
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

                    // Clear abroad keys
                    'intl_country'   => null,
                    'intl_state'     => null,
                    'intl_city'      => null,
                ])
                : array_merge($existingProfile->location ?? [], [
                    'address_type'   => 'abroad',
                    'street_address' => $this->street_address,
                    'intl_country'   => $this->intl_country,
                    'intl_state'     => $this->intl_state,
                    'intl_city'      => $this->intl_city,
                    'address'        => $fullAddress,

                    // Clear PH keys
                    'region_code'    => null,
                    'region_name'    => null,
                    'province_code'  => null,
                    'province_name'  => null,
                    'city_code'      => null,
                    'city_name'      => null,
                    'barangay_code'  => null,
                    'barangay_name'  => null,
                ]);

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar'           => $existingProfile->avatar ?? null,
                    'gender'           => strtolower($this->gender),
                    'contact_number_1' => $this->contact_number_1,
                    'contact_number_2' => $this->contact_number_2 ?: null,
                    'location'         => $location,
                    'batch_id'         => $this->batch_id,
                    'is_private'       => $existingProfile->is_private ?? false,
                    'is_verified'      => $existingProfile->is_verified ?? false,
                    'board_taken'      => $isBoardCourse ? ($this->board_taken ?: null) : null,
                    'board_rate'       => $isBoardCourse
                        ? ($this->board_rate !== '' ? round((float) $this->board_rate, 2) : null)
                        : null,
                ]
            );

            $profile->courses()->sync([$this->course_id]);

            $tracerStudy = TracerStudy::firstOrCreate(['user_id' => $user->id]);

            CivilStatusEmployment::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'civil_status'               => $this->civil_status,
                    'employment_status'          => $this->employment_status,
                    'current_job_position'       => $this->current_job_position ?: null,
                    'employed_related_to_degree' => $this->employed_related_to_degree ?: null,
                    'employment_type'            => $this->employment_type ?: null,
                    'organization_type'          => $this->organization_type ?: null,
                    'employment_area'            => $this->employment_area ?: null,
                    'abroad_country'             => $this->abroad_country ?: null,
                    'months_to_first_job'        => $this->months_to_first_job ?: null,
                ]
            );

            FurtherStudy::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'is_pursued_further_studies' => $this->is_pursued_further_studies,
                    'level_of_study'             => $this->is_pursued_further_studies ? $this->level_of_study : null,
                ]
            );

            if ($this->employment_status === 'employed' && $this->company_id && $this->date_hired) {
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
            }
        });

        session()->flash('status', 'Thank you! Please wait for the admin to approve your form.');

        return redirect()->route('alumni.dashboard');
    }
};