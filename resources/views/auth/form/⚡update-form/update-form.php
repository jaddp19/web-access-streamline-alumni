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

    public int $step = 1;
    #[Locked]
    public int $totalSteps = 4;

    // Step 1 — Personal
    public string $gender = '';
    public string $contact_number_1 = '';
    public string $contact_number_2 = '';
    public bool $consentGiven = false;

    // Step 1 — Address
    public string $address_type = 'philippines';
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $barangayCode = '';
    public string $street_address = '';

    // Step 1 — International address
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

        $user    = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($profile) {
            $this->gender           = $profile->gender ? ucfirst($profile->gender) : '';
            $this->contact_number_1 = $profile->contact_number_1 ?? '';
            $this->contact_number_2 = $profile->contact_number_2 ?? '';
            $this->consentGiven     = (bool) $profile->consent_given_at;
            $this->batch_id         = $profile->batch_id ?? '';

            $this->course_id = $profile->courses()->value('courses.id') ?? '';

            // Hydrate board fields
            $this->board_taken = $profile->board_taken
                ? \Carbon\Carbon::parse($profile->board_taken)->format('Y-m-d')
                : null;
            $this->board_rate = $profile->board_rate !== null
                ? (string) $profile->board_rate
                : '';

            $location = $profile->location ?? [];

            $this->street_address = $location['street_address'] ?? '';

            // Detect address type from saved data (with PH fallback for legacy rows)
            $this->address_type = $location['address_type']
                ?? (!empty($location['region_code']) ? 'philippines' : (empty($location['intl_country']) ? 'philippines' : 'abroad'));

            if ($this->address_type === 'philippines') {
                $this->regionCode   = $location['region_code'] ?? '';
                $this->provinceCode = $location['province_code'] ?? '';
                $this->cityCode     = $location['city_code'] ?? '';
                $this->barangayCode = $location['barangay_code'] ?? '';
            } else {
                $this->intl_country = $location['intl_country'] ?? '';
                $this->intl_state   = $location['intl_state'] ?? '';
                $this->intl_city    = $location['intl_city'] ?? '';
            }
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

    /** Clear the "other field is required" error as soon as the user fills its pair. */
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
            $logoPath = $this->new_company_logo
                ? $this->new_company_logo->store('companies', 'public')
                : null;

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
                    'is_verified'      => $existingProfile->is_verified ?? false,
                    'board_taken'      => $isBoardCourse ? ($this->board_taken ?: null) : null,
                    'board_rate'       => $isBoardCourse && $boardRate !== ''
                        ? round((float) $boardRate, 2)
                        : null,
                ]
            );

            $profile->courses()->sync([$this->course_id]);

            $tracerStudy = TracerStudy::firstOrCreate(['user_id' => $user->id]);
            $isEmployed  = $this->employment_status === 'employed';

            CivilStatusEmployment::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'civil_status'      => $this->civil_status,
                    'employment_status' => $this->employment_status,

                    'current_job_position'       => $isEmployed ? ($this->current_job_position ?: null) : null,
                    'employed_related_to_degree' => $isEmployed ? ($this->employed_related_to_degree ?: null) : null,
                    'employment_type'            => $isEmployed ? ($this->employment_type ?: null) : null,
                    'organization_type'          => $isEmployed ? ($this->organization_type ?: null) : null,
                    'employment_area'            => $isEmployed ? ($this->employment_area ?: null) : null,
                    'abroad_country'             => $isEmployed && $this->employment_area === 'abroad'
                        ? ($this->abroad_country ?: null)
                        : null,
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

            // ===== Work history =====
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
            } else {
                WorkHistory::where('user_id', $user->id)
                    ->where('is_current_job', true)
                    ->update(['is_current_job' => false]);
            }
        });

        // ─── Send update confirmation email ──────────────────────────
        $updater      = Auth::user();
        $isSelfUpdate = $updater->id === $user->id;
        $isRegistrar  = ! $isSelfUpdate && $updater->hasRole('registrar');

        EmailTemplateService::send('tracer-study-updated', $user->email, [
            'name'           => $user->name,
            'year'           => now()->year,
            'updated_at'     => now()->format('F j, Y · g:i A'),

            // ── New: lets the template word itself correctly ──
            'is_self_update' => $isSelfUpdate,
            'updater_label'  => match (true) {
                $isSelfUpdate => $user->name,       // themselves
                $isRegistrar  => 'the registrar',
                default       => 'an administrator',
            },
        ]);
        // ─────────────────────────────────────────────────────────────

        session()->flash('status', 'Your tracer study has been updated successfully.');
        $this->dispatch('close-tab');
    }
};