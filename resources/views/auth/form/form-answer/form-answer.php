<?php

use App\Models\Batch;
use App\Models\Company;
use App\Models\Course;
use App\Models\TracerStudy;
use App\Models\CivilStatusEmployment;
use App\Models\FurtherStudy;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use App\Services\EmailTemplateService;
use App\Services\PhAddressService;
use App\Support\TracerStudyRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-form')] class extends Component
{
    use WithFileUploads;

    public bool $alreadyFilled = false;

    public int $step = 1;
    #[Locked]
    public int $totalSteps = 4;

    // Step 1 — Personal
    public string $gender = '';
    public string $contact_number_1 = '';
    public string $contact_number_2 = '';
    public bool $consentGiven = false;

    // Step 1 — Address (alumni's own)
    public string $address_type = 'philippines';
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $barangayCode = '';
    public string $street_address = '';

    // Step 1 — International address (alumni's own)
    public string $intl_country = '';
    public string $intl_state = '';
    public string $intl_city = '';

    // Step 2
    public string $civil_status = '';
    public $course_id = '';
    public $batch_id = '';
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
    public ?int $company_id = null;
    public string $date_hired = '';

    // Step 3 — Inline new company (basic)
    public bool $showNewCompanyForm = false;
    public $new_company_logo = null;
    public string $new_company_name = '';
    public string $new_company_desc = '';

    // Step 3 — Inline new company (address cascade)
    public string $new_company_address_type = 'philippines';
    public string $new_company_region_code = '';
    public string $new_company_province_code = '';
    public string $new_company_city_code = '';
    public string $new_company_street_address = '';
    public string $new_company_intl_country = '';
    public string $new_company_intl_state = '';
    public string $new_company_intl_city = '';

    // Step 4
    public bool $is_pursued_further_studies = false;
    public string $level_of_study = '';

    public $courses = [];
    public $batches = [];

    public function mount()
    {
        $user = Auth::user();

        if (UserProfile::where('user_id', $user->id)->exists()) {
            $this->alreadyFilled = true;
            return;
        }

        $this->courses = Course::orderBy('course_title')->pluck('course_title', 'id');
        $this->batches = Batch::orderBy('batch_name', 'desc')->pluck('batch_name', 'id');
    }

    // ===== Cascading address (alumni's own) =====

    public function updatedRegionCode(): void
    {
        $this->provinceCode = '';
        $this->cityCode = '';
        $this->barangayCode = '';
    }

    public function updatedProvinceCode(): void
    {
        $this->cityCode = '';
        $this->barangayCode = '';
    }

    public function updatedCityCode(): void
    {
        $this->barangayCode = '';
    }

    public function updatedAddressType(): void
    {
        $this->resetErrorBag([
            'regionCode', 'provinceCode', 'cityCode', 'barangayCode',
            'intl_country', 'intl_state', 'intl_city',
        ]);
    }

    public function updatedCourseId(): void
    {
        if ($this->selectedCourse?->course_type !== 'board') {
            $this->board_taken = null;
            $this->board_rate  = '';
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

    // ===== Cascading address (new company inline) =====

    public function updatedNewCompanyAddressType(): void
    {
        $this->resetErrorBag([
            'new_company_region_code', 'new_company_province_code', 'new_company_city_code',
            'new_company_street_address',
            'new_company_intl_country', 'new_company_intl_state', 'new_company_intl_city',
        ]);
    }

    public function updatedNewCompanyRegionCode(): void
    {
        $this->new_company_province_code = '';
        $this->new_company_city_code = '';
        $this->resetErrorBag(['new_company_province_code', 'new_company_city_code']);
    }

    public function updatedNewCompanyProvinceCode(): void
    {
        $this->new_company_city_code = '';
        $this->resetErrorBag('new_company_city_code');
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
        return $this->course_id ? Course::find($this->course_id) : null;
    }

    // ---- New-company address cascades ----

    #[Computed]
    public function newCompanyProvinces()
    {
        return $this->new_company_region_code
            ? app(PhAddressService::class)->provinces($this->new_company_region_code)
            : collect();
    }

    #[Computed]
    public function newCompanyCities()
    {
        return $this->new_company_province_code
            ? app(PhAddressService::class)->cities($this->new_company_province_code)
            : collect();
    }

    // ===== Inline company creation =====

    public function toggleNewCompanyForm(): void
    {
        $this->showNewCompanyForm = ! $this->showNewCompanyForm;

        if (! $this->showNewCompanyForm) {
            $this->reset([
                'new_company_logo', 'new_company_name', 'new_company_desc',
                'new_company_region_code', 'new_company_province_code', 'new_company_city_code',
                'new_company_street_address',
                'new_company_intl_country', 'new_company_intl_state', 'new_company_intl_city',
            ]);
            $this->new_company_address_type = 'philippines';

            $this->resetErrorBag([
                'new_company_logo', 'new_company_name', 'new_company_desc',
                'new_company_region_code', 'new_company_province_code', 'new_company_city_code',
                'new_company_street_address',
                'new_company_intl_country', 'new_company_intl_state', 'new_company_intl_city',
            ]);
        } else {
            $this->company_id = null;
        }
    }

    public function createCompany(): void
    {
        $this->validate([
            'new_company_name'    => 'required|string|max:255|unique:companies,company_name',
            'new_company_logo'    => 'nullable|image|max:2048',
            'new_company_desc'    => 'nullable|string|max:2000',

            'new_company_address_type' => 'required|in:philippines,abroad',

            'new_company_region_code'     => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_province_code'   => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_city_code'       => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_street_address'  => 'nullable|string|max:500',

            'new_company_intl_country' => 'required_if:new_company_address_type,abroad|nullable|string|max:255',
            'new_company_intl_state'   => 'nullable|string|max:255',
            'new_company_intl_city'    => 'required_if:new_company_address_type,abroad|nullable|string|max:255',
        ], [
            'new_company_name.required'    => 'Company name is required.',
            'new_company_name.unique'      => 'A company with this name already exists.',
            'new_company_region_code.required_if'   => 'Please select a region.',
            'new_company_province_code.required_if' => 'Please select a province.',
            'new_company_city_code.required_if'     => 'Please select a city / municipality.',
            'new_company_intl_country.required_if'  => 'Please enter the country.',
            'new_company_intl_city.required_if'     => 'Please enter the city.',
        ]);

        $service = app(PhAddressService::class);

        // ---- Compose address string ----
        if ($this->new_company_address_type === 'philippines') {
            $region   = $service->findByCode($this->new_company_region_code);
            $province = $service->findByCode($this->new_company_province_code);
            $city     = $service->findByCode($this->new_company_city_code);

            $companyAddress = collect([
                $this->new_company_street_address,
                $city->name ?? null,
                $province->name ?? null,
                $region->name ?? null,
            ])->filter(fn ($p) => filled($p))->implode(', ');
        } else {
            $companyAddress = collect([
                $this->new_company_intl_city,
                $this->new_company_intl_state,
                $this->new_company_intl_country,
            ])->filter(fn ($p) => filled($p))->implode(', ');
        }

        try {
            $logoPath = $this->new_company_logo
                ? $this->new_company_logo->store('companies', 'public')
                : null;

            $company = Company::create([
                'company_name'    => trim($this->new_company_name),
                'company_address' => $companyAddress ?: null,
                'company_logo'    => $logoPath,
                'company_desc'    => trim($this->new_company_desc) ?: null,
            ]);

            $this->company_id         = $company->id;
            $this->showNewCompanyForm = false;

            $this->reset([
                'new_company_logo', 'new_company_name', 'new_company_desc',
                'new_company_region_code', 'new_company_province_code', 'new_company_city_code',
                'new_company_street_address',
                'new_company_intl_country', 'new_company_intl_state', 'new_company_intl_city',
            ]);
            $this->new_company_address_type = 'philippines';

            unset($this->companies);

            session()->flash('company_created', 'Company "' . $company->company_name . '" created and selected.');
        } catch (\Throwable $e) {
            logger()->error('Company creation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    // ===== Navigation =====

    public function nextStep(): void
    {
        $this->validate(
            TracerStudyRules::step($this->step, [
                'taken' => $this->board_taken,
                'rate'  => $this->board_rate,
            ]),
            TracerStudyRules::messages()
        );

        if ($this->step < $this->totalSteps) {
            $this->step++;
            $this->dispatch('step-changed');
        }
    }

    public function previousStep(): void
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
        $this->validate(
            TracerStudyRules::all([
                'taken' => $this->board_taken,
                'rate'  => $this->board_rate,
            ]),
            TracerStudyRules::messages()
        );

        $user    = Auth::user();
        $service = app(PhAddressService::class);

        $region   = $this->address_type === 'philippines' ? $service->findByCode($this->regionCode) : null;
        $province = $this->address_type === 'philippines' ? $service->findByCode($this->provinceCode) : null;
        $city     = $this->address_type === 'philippines' ? $service->findByCode($this->cityCode) : null;
        $barangay = $this->address_type === 'philippines' ? $service->findByCode($this->barangayCode) : null;

        $fullAddress = $this->address_type === 'philippines'
            ? collect([
                $this->street_address,
                $barangay->name ?? null,
                $city->name ?? null,
                $province->name ?? null,
                $region->name ?? null,
            ])->filter()->implode(', ')
            : collect([
                $this->street_address,
                $this->intl_city,
                $this->intl_state,
                $this->intl_country,
            ])->filter()->implode(', ');

        DB::transaction(function () use ($user, $region, $province, $city, $barangay, $fullAddress) {
            $existingProfile = UserProfile::where('user_id', $user->id)->first();
            $isBoardCourse   = $this->selectedCourse?->course_type === 'board';

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
                    'region_code'    => null,
                    'region_name'    => null,
                    'province_code'  => null,
                    'province_name'  => null,
                    'city_code'      => null,
                    'city_name'      => null,
                    'barangay_code'  => null,
                    'barangay_name'  => null,
                ]);

            $boardRate = trim((string) $this->board_rate);

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
                    'is_verified'      => ! $isBoardCourse,
                    'board_taken'      => $isBoardCourse ? ($this->board_taken ?: null) : null,
                    'board_rate'       => $isBoardCourse && $boardRate !== ''
                        ? round((float) $boardRate, 2)
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

        EmailTemplateService::send('tracer-study-submitted', $user->email, [
            'name'         => $user->name,
            'year'         => now()->year,
            'submitted_at' => now()->format('F j, Y · g:i A'),
        ]);

        return redirect()->route('alumni.dashboard');
    }
};