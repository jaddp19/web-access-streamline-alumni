<?php

use App\Models\Company;
use App\Models\WorkHistory;
use App\Models\CivilStatusEmployment;
use App\Models\TracerStudy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    // Work history fields
    public string $work_name = '';
    public ?int $company_id = null;
    public string $date_hired = '';
    public bool $is_current_job = false;

    // ===== Employment details (CivilStatusEmployment) =====
    public string $civil_status = '';
    public string $employment_area = '';
    public string $abroad_country = '';
    public string $employed_related_to_degree = '';
    public string $employment_type = '';
    public string $organization_type = '';
    public string $months_to_first_job = '';

    // Inline "new company" mode
    public bool $showNewCompanyForm = false;
    public $new_company_logo = null;
    public string $new_company_name = '';
    public string $new_company_address = '';
    public string $new_company_desc = '';

    public function mount(): void
    {
        // Prefill civil_status from existing tracer (if any) so the user
        // doesn't have to re-enter it when they first add a job.
        $tracerStudy = TracerStudy::where('user_id', Auth::id())->first();

        if ($tracerStudy) {
            $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();

            if ($employment) {
                $this->civil_status = $employment->civil_status ?? '';
            }
        }
    }

    // ---- Validation ----

    protected function rules(): array
    {
        return [
            'work_name'      => 'required|string|max:255',
            'company_id'     => 'required|exists:companies,id',
            'date_hired'     => 'required|date|before_or_equal:today',
            'is_current_job' => 'boolean',
        ];
    }

    protected function employmentRules(): array
    {
        // Only validate employment details when this is the current job
        if (! $this->is_current_job) {
            return [];
        }

        return [
            'civil_status'               => 'required|in:single,married,widowed,separated,single-parent',
            'employed_related_to_degree' => 'required|in:yes,no,partially-related',
            'employment_type'            => 'required|in:full-time,part-time,contractual-project-based,freelance,other',
            'organization_type'          => 'required|in:private-company,government-agency,non-government-organization,educational-institution,self-employed-business,other',
            'employment_area'            => 'required|in:philippines,abroad',
            'abroad_country'             => 'required_if:employment_area,abroad|nullable|string|max:255',
            'months_to_first_job'        => 'required|in:1-3-months,4-6-months,more-than-6-months,more-than-1-year',
        ];
    }

    protected function companyRules(): array
    {
        return [
            'new_company_name'    => 'required|string|max:255|unique:companies,company_name',
            'new_company_logo'    => 'nullable|image|max:2048',
            'new_company_address' => 'nullable|string|max:500',
            'new_company_desc'    => 'nullable|string|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'work_name.required'                    => 'Please enter your job title or position.',
            'company_id.required'                   => 'Please select a company.',
            'company_id.exists'                     => 'The selected company no longer exists.',
            'date_hired.required'                   => 'Please enter the date you were hired.',
            'date_hired.before_or_equal'            => 'The hire date cannot be in the future.',
            'civil_status.required'                 => 'Please select your civil status.',
            'employed_related_to_degree.required'   => 'Please answer if your job is related to your degree.',
            'employment_type.required'              => 'Please select type of employment.',
            'organization_type.required'            => 'Please select type of organization.',
            'employment_area.required'              => 'Please select employment area.',
            'abroad_country.required_if'            => 'Please specify the country.',
            'months_to_first_job.required'          => 'Please select how long it took to get your first job.',
            'new_company_name.required'             => 'Please enter the company name.',
            'new_company_name.unique'               => 'A company with this name already exists.',
            'new_company_logo.image'                => 'The logo must be an image file.',
            'new_company_logo.max'                  => 'The logo cannot exceed 2MB.',
        ];
    }

    // ---- Computed ----

    #[Computed]
    public function companies()
    {
        return Company::orderBy('company_name')
            ->get(['id', 'company_name', 'company_logo', 'company_address']);
    }

    // ---- Actions ----

    public function toggleNewCompanyForm(): void
    {
        $this->showNewCompanyForm = ! $this->showNewCompanyForm;

        if (! $this->showNewCompanyForm) {
            $this->reset([
                'new_company_logo',
                'new_company_name',
                'new_company_address',
                'new_company_desc',
            ]);
            $this->resetErrorBag([
                'new_company_logo',
                'new_company_name',
                'new_company_address',
                'new_company_desc',
            ]);
        }
    }

    public function createCompany(): void
    {
        $validated = $this->validate($this->companyRules(), $this->messages());

        try {
            $logoPath = null;
            if ($this->new_company_logo) {
                $logoPath = $this->new_company_logo->store('companies', 'public');
            }

            $company = Company::create([
                'company_name'    => trim($validated['new_company_name']),
                'company_address' => trim($this->new_company_address) ?: null,
                'company_logo'    => $logoPath,
                'company_desc'    => trim($this->new_company_desc) ?: null,
            ]);

            $this->company_id         = $company->id;
            $this->showNewCompanyForm = false;

            $this->reset([
                'new_company_logo',
                'new_company_name',
                'new_company_address',
                'new_company_desc',
            ]);

            unset($this->companies);

            session()->flash('company_created', 'Company "' . $company->company_name . '" created and selected.');
        } catch (\Throwable $e) {
            logger()->error('Company creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    public function saveWorkHistory()
    {
        $rules = array_merge($this->rules(), $this->employmentRules());

        $this->validate($rules, $this->messages());

        try {
            DB::transaction(function () {

                WorkHistory::create([
                    'user_id'        => Auth::id(),
                    'work_name'      => trim($this->work_name),
                    'company_id'     => $this->company_id,
                    'date_hired'     => $this->date_hired,
                    'is_current_job' => $this->is_current_job,
                ]);

                // ===== Sync tracer study =====
                $tracerStudy = TracerStudy::firstOrCreate(['user_id' => Auth::id()]);

                $employment = CivilStatusEmployment::firstOrCreate(
                    ['tracer_study_id' => $tracerStudy->id],
                    [
                        'civil_status'      => $this->civil_status ?: 'single',
                        'employment_status' => 'unemployed',
                    ]
                );

                if ($this->is_current_job) {
                    // Employed → write everything from the form
                    $employment->update([
                        'civil_status'               => $this->civil_status,
                        'employment_status'          => 'employed',
                        'current_job_position'       => trim($this->work_name),
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
                    // Not current → only set civil_status if this is the first-ever row
                    if (! $employment->civil_status) {
                        $employment->update(['civil_status' => $this->civil_status ?: 'single']);
                    }
                }
            });

            session()->flash('success', 'Work experience added successfully.');
            return redirect()->route('alumni.profile');
        } catch (\Throwable $e) {
            logger()->error('Work history creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
};