<?php

use App\Models\Batch;
use App\Models\CivilStatusEmployment;
use App\Models\Company;
use App\Models\Course;
use App\Models\FurtherStudy;
use App\Models\TracerStudy;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use App\Services\EmailTemplateService;
use App\Services\PhAddressService;
use App\Support\TracerStudyRules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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

    // Step 2 — snapshots for change detection
    public ?int $original_course_id = null;

    public ?string $original_board_taken = null;

    public string $original_board_rate = '';

    // Step 3
    public string $employment_status = '';

    public string $employment_status_other = '';

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
        Gate::authorize('can_update');
        $this->courses = Course::orderBy('course_title')->pluck('course_title', 'id');
        $this->batches = Batch::orderBy('batch_name', 'desc')->pluck('batch_name', 'id');

        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($profile) {
            $this->gender = $profile->gender ? ucfirst($profile->gender) : '';
            $this->contact_number_1 = $profile->contact_number_1 ?? '';
            $this->contact_number_2 = $profile->contact_number_2 ?? '';
            $this->consentGiven = (bool) $profile->consent_given_at;
            $this->batch_id = $profile->batch_id ?? '';

            $this->course_id = $profile->courses()->value('courses.id') ?? '';

            $this->original_course_id = $this->course_id ? (int) $this->course_id : null;

            // Load the LATEST board exam attempt (verified OR pending) so the form
            // always reflects the most recent submission — not the "featured" verified
            // one from syncBoardMirrors().
            $latestAttempt = $profile->boardExams()
                ->reorder()
                ->latest('id')
                ->first();

            if ($latestAttempt) {
                $this->board_taken = $latestAttempt->date_taken?->format('Y-m-d');
                $this->board_rate  = $latestAttempt->rate !== null
                    ? (string) $latestAttempt->rate
                    : '';
            } else {
                // Fallback for legacy rows that only have the profile mirror columns.
                $this->board_taken = $profile->board_taken
                    ? Carbon::parse($profile->board_taken)->format('Y-m-d')
                    : null;
                $this->board_rate = $profile->board_rate !== null
                    ? (string) $profile->board_rate
                    : '';
            }

            $this->original_board_taken = $this->board_taken;
            $this->original_board_rate = $this->board_rate;

            $location = $profile->location ?? [];

            $this->street_address = $location['street_address'] ?? '';

            $this->address_type = $location['address_type']
                ?? (! empty($location['region_code']) ? 'philippines' : (empty($location['intl_country']) ? 'philippines' : 'abroad'));

            if ($this->address_type === 'philippines') {
                $this->regionCode = $location['region_code'] ?? '';
                $this->provinceCode = $location['province_code'] ?? '';
                $this->cityCode = $location['city_code'] ?? '';
                $this->barangayCode = $location['barangay_code'] ?? '';
            } else {
                $this->intl_country = $location['intl_country'] ?? '';
                $this->intl_state = $location['intl_state'] ?? '';
                $this->intl_city = $location['intl_city'] ?? '';
            }
        }

        $tracerStudy = TracerStudy::where('user_id', $user->id)->first();

        if ($tracerStudy) {
            $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();
            if ($employment) {
                $this->civil_status = $employment->civil_status ?? '';
                $this->employment_status = $employment->employment_status ?? '';
                $this->employment_status_other = $employment->employment_status_other ?? '';
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

        $currentWork = WorkHistory::where('user_id', $user->id)
            ->where('is_current_job', true)
            ->first();

        if ($currentWork) {
            $this->company_id = $currentWork->company_id;
            $this->date_hired = $currentWork->date_hired?->format('Y-m-d') ?? '';
        }
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
            $this->board_rate = '';
            $this->resetErrorBag(['board_taken', 'board_rate']);
        }
    }

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

    public function updatedNewCompanyLogo(): void
    {
        if (! $this->new_company_logo) {
            return;
        }

        try {
            $mime = (string) $this->new_company_logo->getMimeType();
        } catch (Throwable $e) {
            $this->reset('new_company_logo');
            $this->addError('new_company_logo', 'Could not read the uploaded file. Please try another image.');

            return;
        }

        if (! str_starts_with($mime, 'image/')) {
            $this->reset('new_company_logo');
            $this->addError('new_company_logo', 'The logo must be an image file (JPG, PNG, or WebP).');

            return;
        }

        try {
            $this->validateOnly('new_company_logo', [
                'new_company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'new_company_logo.image' => 'The logo must be an image file.',
                'new_company_logo.mimes' => 'Logo must be JPG, PNG, or WebP.',
                'new_company_logo.max' => 'The logo cannot exceed 2MB.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('new_company_logo');
            throw $e;
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

    public function updatedEmploymentStatus(): void
    {
        if ($this->employment_status !== 'other') {
            $this->employment_status_other = '';
            $this->resetErrorBag('employment_status_other');
        }

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
                $this->reset([
                    'new_company_logo', 'new_company_name', 'new_company_desc',
                    'new_company_region_code', 'new_company_province_code', 'new_company_city_code',
                    'new_company_street_address',
                    'new_company_intl_country', 'new_company_intl_state', 'new_company_intl_city',
                ]);
                $this->new_company_address_type = 'philippines';
            }
        }
    }

    public function createCompany(): void
    {
        $this->validate([
            'new_company_name' => 'required|string|max:255|unique:companies,company_name',
            'new_company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'new_company_desc' => 'nullable|string|max:2000',

            'new_company_address_type' => 'required|in:philippines,abroad',

            'new_company_region_code' => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_province_code' => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_city_code' => 'required_if:new_company_address_type,philippines|nullable|string',
            'new_company_street_address' => 'nullable|string|max:500',

            'new_company_intl_country' => 'required_if:new_company_address_type,abroad|nullable|string|max:255',
            'new_company_intl_state' => 'nullable|string|max:255',
            'new_company_intl_city' => 'required_if:new_company_address_type,abroad|nullable|string|max:255',
        ], [
            'new_company_name.required' => 'Company name is required.',
            'new_company_name.unique' => 'A company with this name already exists.',
            'new_company_logo.image' => 'The logo must be an image file.',
            'new_company_logo.mimes' => 'Logo must be JPG, PNG, or WebP.',
            'new_company_logo.max' => 'The logo cannot exceed 2MB.',
            'new_company_region_code.required_if' => 'Please select a region.',
            'new_company_province_code.required_if' => 'Please select a province.',
            'new_company_city_code.required_if' => 'Please select a city / municipality.',
            'new_company_intl_country.required_if' => 'Please enter the country.',
            'new_company_intl_city.required_if' => 'Please enter the city.',
        ]);

        if ($this->new_company_logo) {
            try {
                $mime = (string) $this->new_company_logo->getMimeType();
            } catch (Throwable $e) {
                $this->addError('new_company_logo', 'Could not read the uploaded file. Please try another image.');

                return;
            }

            if (! str_starts_with($mime, 'image/')) {
                $this->addError('new_company_logo', 'The logo must be an image file (JPG, PNG, or WebP).');

                return;
            }
        }

        $service = app(PhAddressService::class);

        if ($this->new_company_address_type === 'philippines') {
            $region = $service->findByCode($this->new_company_region_code);
            $province = $service->findByCode($this->new_company_province_code);
            $city = $service->findByCode($this->new_company_city_code);

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
                'company_name' => trim(strip_tags($this->new_company_name)),
                'company_address' => $companyAddress ?: null,
                'company_logo' => $logoPath,
                'company_desc' => trim(strip_tags($this->new_company_desc)) ?: null,
            ]);

            $this->company_id = $company->id;
            $this->showNewCompanyForm = false;

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

            unset($this->companies);

            session()->flash('company_created', 'Company "'.$company->company_name.'" created and selected.');
        } catch (Throwable $e) {
            if ($logoPath ?? null) {
                Storage::disk('public')->delete($logoPath);
            }

            logger()->error('Company creation failed: '.$e->getMessage());
            session()->flash('error', 'Failed to create company. Please try again.');
        }
    }

    // ===== Navigation =====

    public function nextStep(): void
    {
        $this->validate(
            TracerStudyRules::step($this->step, [
                'taken' => $this->board_taken,
                'rate' => $this->board_rate,
                'batch_id' => $this->batch_id,
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
        Gate::authorize('can_update');
        $this->validate(
            TracerStudyRules::all([
                'taken' => $this->board_taken,
                'rate' => $this->board_rate,
                'batch_id' => $this->batch_id,
            ]),
            TracerStudyRules::messages()
        );

        $user = Auth::user();
        $service = app(PhAddressService::class);

        $region = $this->address_type === 'philippines' ? $service->findByCode($this->regionCode) : null;
        $province = $this->address_type === 'philippines' ? $service->findByCode($this->provinceCode) : null;
        $city = $this->address_type === 'philippines' ? $service->findByCode($this->cityCode) : null;
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

        // ===== Determine verification outcome BEFORE the transaction =====
        $isBoardCourse = $this->selectedCourse?->course_type === 'board';
        $examName = $this->selectedCourse?->course_title;

        $wasBoardCourse = $this->original_course_id
            ? (Course::whereKey($this->original_course_id)->value('course_type') === 'board')
            : false;

        $boardChanged = $this->board_taken !== $this->original_board_taken
            || round((float) $this->board_rate, 2) !== round((float) $this->original_board_rate, 2);

        $courseChanged = (int) $this->course_id !== (int) $this->original_course_id;

        DB::transaction(function () use (
            $user, $region, $province, $city, $barangay, $fullAddress,
            $isBoardCourse, $wasBoardCourse, $boardChanged, $courseChanged, $examName
        ) {
            $existingProfile = UserProfile::where('user_id', $user->id)->first();

            $location = $this->address_type === 'philippines'
                ? array_merge($existingProfile->location ?? [], [
                    'address_type' => 'philippines',
                    'street_address' => $this->street_address,
                    'region_code' => $this->regionCode,
                    'region_name' => $region->name ?? null,
                    'province_code' => $this->provinceCode,
                    'province_name' => $province->name ?? null,
                    'city_code' => $this->cityCode,
                    'city_name' => $city->name ?? null,
                    'barangay_code' => $this->barangayCode,
                    'barangay_name' => $barangay->name ?? null,
                    'address' => $fullAddress,
                    'intl_country' => null,
                    'intl_state' => null,
                    'intl_city' => null,
                ])
                : array_merge($existingProfile->location ?? [], [
                    'address_type' => 'abroad',
                    'street_address' => $this->street_address,
                    'intl_country' => $this->intl_country,
                    'intl_state' => $this->intl_state,
                    'intl_city' => $this->intl_city,
                    'address' => $fullAddress,
                    'region_code' => null,
                    'region_name' => null,
                    'province_code' => null,
                    'province_name' => null,
                    'city_code' => null,
                    'city_name' => null,
                    'barangay_code' => null,
                    'barangay_name' => null,
                ]);

            $boardRate = trim((string) $this->board_rate);

            // Temporary value; syncBoardMirrors() finalizes it from board_exams.
            $isVerified = ! $isBoardCourse;

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar' => $existingProfile->avatar ?? null,
                    'gender' => strtolower($this->gender),
                    'contact_number_1' => $this->contact_number_1,
                    'contact_number_2' => $this->contact_number_2 ?: null,
                    'location' => $location,
                    'batch_id' => $this->batch_id,
                    'is_private' => $existingProfile->is_private ?? false,
                    'is_verified' => $isVerified,
                    'board_taken' => $isBoardCourse ? ($this->board_taken ?: null) : null,
                    'board_rate' => $isBoardCourse && $boardRate !== ''
                        ? round((float) $boardRate, 2)
                        : null,
                ]
            );

            // ─── Re-enter the review queue if anything meaningful changed ─────
            // Any edit to alumni-editable fields, a course switch, or a change
            // to the board data sends the profile back to "Awaiting Review" so
            // the registrar can re-verify it.
            $profileFieldsChanged = $profile->wasChanged([
                'gender',
                'contact_number_1',
                'contact_number_2',
                'location',
                'batch_id',
                'board_taken',
                'board_rate',
            ]);

            if ($profileFieldsChanged || $courseChanged || $boardChanged) {
                $profile->forceFill([
                    'is_approved' => false,
                    'last_rejection_reason' => null,   // clears "Rejected" → shows "Awaiting Review"
                ])->saveQuietly();

                // Optional but recommended: drop the cached "verified board passer"
                // badge until the registrar re-approves. Comment out if you want the
                // old verified attempt to keep counting.
                // $profile->forceFill(['is_verified' => false])->saveQuietly();
            }
            // ──────────────────────────────────────────────────────────────────

            // ── Board exam history handling ──────────────────────────────
            $wasVerified = (bool) $existingProfile?->is_verified;

            if (! $isBoardCourse) {
                // Non-board target → wipe every attempt; nothing to verify.
                $profile->boardExams()->reorder()->delete();
                $profile->syncBoardMirrors();
                $profile->forceFill([
                    'board_taken' => null,
                    'board_rate' => null,
                    'is_verified' => true,
                ])->saveQuietly();
            } else {
                if ($courseChanged && $wasBoardCourse) {
                    // board → DIFFERENT board: reset every existing attempt.
                    $profile->boardExams()->reorder()->update([
                        'is_verified' => false,
                        'verified_at' => null,
                        'is_top_notcher' => false,
                        'top_notcher_rank' => null,
                    ]);
                } elseif ($courseChanged && ! $wasBoardCourse) {
                    // non-board → board: fresh start.
                    $profile->boardExams()->reorder()->delete();
                }

                if ($wasVerified) {
                    // Verified alumni → new history row, reset verification.
                    $profile->recordBoardAttempt(
                        $this->board_taken ?: null,
                        $this->board_rate ?: null,
                        $examName
                    );

                    if ($courseChanged || $boardChanged) {
                        $profile->forceFill(['is_verified' => false])->saveQuietly();
                    }
                } else {
                    // Unverified alumni → edit the pending row IN PLACE.
                    $pendingExam = $profile->boardExams()
                        ->where('is_verified', false)
                        ->reorder()
                        ->latest('id')
                        ->first();

                    $payload = [
                        'exam_name' => $examName,
                        'date_taken' => $this->board_taken ?: null,
                        'rate' => filled($this->board_rate)
                            ? round((float) $this->board_rate, 2)
                            : null,
                        'passed' => filled($this->board_rate)
                            ? ((float) $this->board_rate >= UserProfile::BOARD_PASSING_RATE)
                            : false,
                    ];

                    if ($pendingExam) {
                        $pendingExam->update($payload);
                    } else {
                        $profile->recordBoardAttempt(
                            $this->board_taken ?: null,
                            $this->board_rate ?: null,
                            $examName
                        );
                    }

                    $profile->syncBoardMirrors();
                }
            }
            // ────────────────────────────────────────────────────────────

            $profile->courses()->sync([$this->course_id]);

            $tracerStudy = TracerStudy::firstOrCreate(['user_id' => $user->id]);
            $isEmployed = $this->employment_status === 'employed';

            CivilStatusEmployment::updateOrCreate(
                ['tracer_study_id' => $tracerStudy->id],
                [
                    'civil_status' => $this->civil_status,
                    'employment_status' => $this->employment_status,
                    'employment_status_other' => $this->employment_status === 'other'
                        ? ($this->employment_status_other ?: null)
                        : null,

                    'current_job_position' => $isEmployed ? ($this->current_job_position ?: null) : null,
                    'employed_related_to_degree' => $isEmployed ? ($this->employed_related_to_degree ?: null) : null,
                    'employment_type' => $isEmployed ? ($this->employment_type ?: null) : null,
                    'organization_type' => $isEmployed ? ($this->organization_type ?: null) : null,
                    'employment_area' => $isEmployed ? ($this->employment_area ?: null) : null,
                    'abroad_country' => $isEmployed && $this->employment_area === 'abroad'
                        ? ($this->abroad_country ?: null)
                        : null,
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

            if ($this->employment_status === 'employed' && $this->company_id && $this->date_hired) {
                $existingCurrent = WorkHistory::where('user_id', $user->id)
                    ->where('is_current_job', true)
                    ->first();

                if ($existingCurrent) {
                    $existingCurrent->update([
                        'work_name' => $this->current_job_position ?: 'Position not specified',
                        'company_id' => $this->company_id,
                        'date_hired' => $this->date_hired,
                    ]);
                } else {
                    WorkHistory::create([
                        'user_id' => $user->id,
                        'work_name' => $this->current_job_position ?: 'Position not specified',
                        'company_id' => $this->company_id,
                        'date_hired' => $this->date_hired,
                        'is_current_job' => true,
                    ]);
                }
            } else {
                WorkHistory::where('user_id', $user->id)
                    ->where('is_current_job', true)
                    ->update(['is_current_job' => false]);
            }
        });

        // ── Refresh snapshots so a second save has correct baseline ──
        $this->original_course_id = $this->course_id ? (int) $this->course_id : null;
        $this->original_board_taken = $this->board_taken;
        $this->original_board_rate = $this->board_rate;

        // ─── Send update confirmation email ──────────────────────────
        $updater = Auth::user();
        $isSelfUpdate = $updater->id === $user->id;
        $isRegistrar = ! $isSelfUpdate && $updater->hasRole('registrar');

        EmailTemplateService::send('tracer-study-updated', $user->email, [
            'name' => $user->name,
            'year' => now()->year,
            'updated_at' => now()->format('F j, Y · g:i A'),

            'is_self_update' => $isSelfUpdate,
            'updater_label' => match (true) {
                $isSelfUpdate => $user->name,
                $isRegistrar => 'the registrar',
                default => 'an administrator',
            },
        ]);
        // ─────────────────────────────────────────────────────────────

        session()->flash('status', 'Your tracer study has been updated successfully.');
        $this->dispatch('close-tab');
    }
};
