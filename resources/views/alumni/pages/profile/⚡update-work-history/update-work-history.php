<?php

use App\Models\CivilStatusEmployment;
use App\Models\Company;
use App\Models\TracerStudy;
use App\Models\WorkHistory;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    public WorkHistory $history;

    // Work history
    public string $work_name = '';
    public ?int $company_id = null;
    public string $date_hired = '';
    public bool $is_current_job = false;

    // Employment details
    public string $civil_status = 'single';
    public string $employment_area = '';
    public string $abroad_country = '';
    public string $employed_related_to_degree = '';
    public string $employment_type = '';
    public string $organization_type = '';
    public string $months_to_first_job = '';

    // Inline new-company — basic
    public bool $showNewCompanyForm = false;
    public $new_company_logo = null;
    public string $new_company_name = '';
    public string $new_company_desc = '';

    // Inline new-company — address cascade
    public string $new_company_address_type = 'philippines';
    public string $new_company_region_code = '';
    public string $new_company_province_code = '';
    public string $new_company_city_code = '';
    public string $new_company_street_address = '';
    public string $new_company_intl_country = '';
    public string $new_company_intl_state = '';
    public string $new_company_intl_city = '';

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(WorkHistory $history): void
    {
        abort_unless($history->user_id === Auth::id(), 403);

        $this->history = $history->load('company');

        $this->work_name      = $history->work_name ?? '';
        $this->company_id     = $history->company_id;
        $this->date_hired     = $history->date_hired?->format('Y-m-d') ?? '';
        $this->is_current_job = (bool) $history->is_current_job;

        $tracerStudy = TracerStudy::query()
            ->where('user_id', Auth::id())
            ->first();

        if (! $tracerStudy) {
            return;
        }

        $employment = CivilStatusEmployment::query()
            ->where('tracer_study_id', $tracerStudy->id)
            ->first();

        if (! $employment) {
            return;
        }

        $this->civil_status               = $employment->civil_status ?? 'single';
        $this->employment_area            = $employment->employment_area ?? '';
        $this->abroad_country             = $employment->abroad_country ?? '';
        $this->employed_related_to_degree = $employment->employed_related_to_degree ?? '';
        $this->employment_type            = $employment->employment_type ?? '';
        $this->organization_type          = $employment->organization_type ?? '';
        $this->months_to_first_job        = $employment->months_to_first_job ?? '';
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'work_name'      => ['required', 'string', 'min:2', 'max:255'],
            'company_id'     => ['required', 'integer', 'exists:companies,id'],
            'date_hired'     => ['required', 'date', 'before_or_equal:today'],
            'is_current_job' => ['boolean'],
        ];
    }

    protected function employmentRules(): array
    {
        if (! $this->is_current_job) {
            return [];
        }

        return [
            'civil_status'               => ['required', 'in:single,married,widowed,separated,single-parent'],
            'employed_related_to_degree' => ['required', 'in:yes,no,partially-related'],
            'employment_type'            => ['required', 'in:full-time,part-time,contractual-project-based,freelance,other'],
            'organization_type'          => ['required', 'in:private-company,government-agency,non-government-organization,educational-institution,self-employed-business,other'],
            'employment_area'            => ['required', 'in:philippines,abroad'],
            'abroad_country'             => ['required_if:employment_area,abroad', 'nullable', 'string', 'max:255'],
            'months_to_first_job'        => ['required', 'in:1-3-months,4-6-months,more-than-6-months,more-than-1-year'],
        ];
    }

    protected function companyRules(): array
    {
        return [
            'new_company_name' => ['required', 'string', 'min:2', 'max:255', 'unique:companies,company_name'],
            'new_company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'new_company_desc' => ['nullable', 'string', 'max:2000'],

            'new_company_address_type' => ['required', 'in:philippines,abroad'],

            'new_company_region_code'    => ['required_if:new_company_address_type,philippines', 'nullable', 'string'],
            'new_company_province_code'  => ['required_if:new_company_address_type,philippines', 'nullable', 'string'],
            'new_company_city_code'      => ['required_if:new_company_address_type,philippines', 'nullable', 'string'],
            'new_company_street_address' => ['nullable', 'string', 'max:500'],

            'new_company_intl_country' => ['required_if:new_company_address_type,abroad', 'nullable', 'string', 'max:255'],
            'new_company_intl_state'   => ['nullable', 'string', 'max:255'],
            'new_company_intl_city'    => ['required_if:new_company_address_type,abroad', 'nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'work_name.required'                  => 'Please enter your job title or position.',
            'work_name.min'                       => 'Job title must be at least 2 characters.',
            'company_id.required'                 => 'Please select a company.',
            'company_id.exists'                   => 'The selected company no longer exists.',
            'date_hired.required'                 => 'Please enter the date you were hired.',
            'date_hired.before_or_equal'          => 'The hire date cannot be in the future.',
            'civil_status.required'               => 'Please select your civil status.',
            'employed_related_to_degree.required' => 'Please answer if your job is related to your degree.',
            'employment_type.required'            => 'Please select type of employment.',
            'organization_type.required'          => 'Please select type of organization.',
            'employment_area.required'            => 'Please select employment area.',
            'abroad_country.required_if'          => 'Please specify the country.',
            'months_to_first_job.required'        => 'Please select how long it took to get your first job.',

            'new_company_name.required'   => 'Please enter the company name.',
            'new_company_name.unique'     => 'A company with this name already exists.',
            'new_company_logo.image'      => 'The logo must be an image file.',
            'new_company_logo.mimes'      => 'Logo must be JPG, PNG, or WebP.',
            'new_company_logo.max'        => 'The logo cannot exceed 2MB.',

            'new_company_region_code.required_if'   => 'Please select a region.',
            'new_company_province_code.required_if' => 'Please select a province.',
            'new_company_city_code.required_if'     => 'Please select a city / municipality.',
            'new_company_intl_country.required_if'  => 'Please enter the country.',
            'new_company_intl_city.required_if'     => 'Please enter the city.',
        ];
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function companies()
    {
        return Company::query()
            ->select('id', 'company_name', 'company_logo', 'company_address')
            ->orderBy('company_name')
            ->get();
    }

    #[Computed]
    public function regions()
    {
        return app(PhAddressService::class)->regions();
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

    // =========================================================
    //  NEW COMPANY FORM
    // =========================================================

    public function toggleNewCompanyForm(): void
    {
        $this->showNewCompanyForm = ! $this->showNewCompanyForm;

        if (! $this->showNewCompanyForm) {
            $this->resetNewCompanyFields();
        }
    }

    protected function resetNewCompanyFields(): void
    {
        $this->reset([
            'new_company_logo',
            'new_company_name',
            'new_company_desc',
            'new_company_region_code',
            'new_company_province_code',
            'new_company_city_code',
            'new_company_street_address',
            'new_company_intl_country',
            'new_company_intl_state',
            'new_company_intl_city',
        ]);

        $this->new_company_address_type = 'philippines';

        $this->resetErrorBag([
            'new_company_logo',
            'new_company_name',
            'new_company_desc',
            'new_company_region_code',
            'new_company_province_code',
            'new_company_city_code',
            'new_company_street_address',
            'new_company_intl_country',
            'new_company_intl_state',
            'new_company_intl_city',
        ]);
    }

    public function updatedNewCompanyLogo(): void
    {
        $this->validateOnly('new_company_logo');
    }

    // ---- New-company address cascade hooks ----

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
        $this->new_company_city_code     = '';
        $this->resetErrorBag(['new_company_province_code', 'new_company_city_code']);
    }

    public function updatedNewCompanyProvinceCode(): void
    {
        $this->new_company_city_code = '';
        $this->resetErrorBag('new_company_city_code');
    }

    // =========================================================
    //  CREATE COMPANY
    // =========================================================

    public function createCompany(): void
    {
        abort_unless(Auth::check(), 403);

        $validated = $this->validate($this->companyRules(), $this->messages());

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

        $logoPath = null;

        try {
            $company = DB::transaction(function () use ($validated, $companyAddress, &$logoPath) {
                if ($this->new_company_logo) {
                    $logoPath = $this->new_company_logo->store('companies', 'public');
                }

                return Company::create([
                    'company_name'    => trim(strip_tags($validated['new_company_name'])),
                    'company_address' => $companyAddress ?: null,
                    'company_logo'    => $logoPath,
                    'company_desc'    => filled($validated['new_company_desc'])
                        ? trim(strip_tags($validated['new_company_desc']))
                        : null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            if ($e->getCode() === '23000') {
                $this->addError('new_company_name', 'This company name was just taken. Please refresh and try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create the company. Please try again.');
            return;
        } catch (\Throwable $e) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            report($e);
            session()->flash('error', 'Could not create the company. Please try again.');
            return;
        }

        $this->company_id         = $company->id;
        $this->showNewCompanyForm = false;

        $this->resetNewCompanyFields();

        unset($this->companies);

        session()->flash('company_created', 'Company "' . $company->company_name . '" created and selected.');
    }

    // =========================================================
    //  TRACER SYNC
    // =========================================================

    protected function syncTracerEmployment(): void
    {
        $tracerStudy = TracerStudy::firstOrCreate(['user_id' => Auth::id()]);

        $employment = CivilStatusEmployment::firstOrCreate(
            ['tracer_study_id' => $tracerStudy->id],
            [
                'civil_status'      => $this->civil_status ?: 'single',
                'employment_status' => 'unemployed',
            ]
        );

        $current = WorkHistory::query()
            ->where('user_id', Auth::id())
            ->where('is_current_job', true)
            ->latest('date_hired')
            ->first();

        if ($current) {
            $employment->update([
                'civil_status'               => $this->civil_status ?: 'single',
                'employment_status'          => 'employed',
                'current_job_position'       => $current->work_name ?: 'Position not specified',
                'employed_related_to_degree' => $this->employed_related_to_degree ?: null,
                'employment_type'            => $this->employment_type ?: null,
                'organization_type'          => $this->organization_type ?: null,
                'employment_area'            => $this->employment_area ?: null,
                'abroad_country'             => $this->employment_area === 'abroad'
                    ? ($this->abroad_country ?: null)
                    : null,
                'months_to_first_job'        => $this->months_to_first_job ?: null,
            ]);
        } else {
            $employment->update([
                'employment_status'          => 'unemployed',
                'current_job_position'       => null,
                'employed_related_to_degree' => null,
                'employment_type'            => null,
                'organization_type'          => null,
                'employment_area'            => null,
                'abroad_country'             => null,
            ]);
        }
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function updateWorkHistory()
    {
        abort_unless($this->history->user_id === Auth::id(), 403);

        $rules = array_merge($this->rules(), $this->employmentRules());

        $this->validate($rules, $this->messages());

        $workName      = trim(strip_tags($this->work_name));
        $civilStatus   = trim(strip_tags($this->civil_status));
        $abroadCountry = trim(strip_tags($this->abroad_country));

        try {
            DB::transaction(function () use ($workName, $civilStatus, $abroadCountry) {
                $this->history->update([
                    'work_name'      => $workName,
                    'company_id'     => $this->company_id,
                    'date_hired'     => $this->date_hired,
                    'is_current_job' => $this->is_current_job,
                ]);

                $this->syncTracerEmployment();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not save changes. Please try again.');
            return;
        }

        session()->flash('success', 'Work experience updated successfully.');

        return redirect()->route('alumni.profile');
    }

    // =========================================================
    //  DELETE
    // =========================================================

    public function deleteWorkHistory()
    {
        abort_unless($this->history->user_id === Auth::id(), 403);

        try {
            DB::transaction(function () {
                $this->history->delete();
                $this->syncTracerEmployment();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not delete this work experience. Please try again.');
            return;
        }

        session()->flash('success', 'Work experience deleted.');

        return redirect()->route('alumni.profile');
    }
};