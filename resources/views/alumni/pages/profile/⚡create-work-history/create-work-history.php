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

    // Inline "new company" mode
    public bool $showNewCompanyForm = false;
    public $new_company_logo = null;
    public string $new_company_name = '';
    public string $new_company_address = '';
    public string $new_company_desc = '';

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

    protected function companyRules(): array
    {
        return [
            'new_company_name' => 'required|string|max:255|unique:companies,company_name',
            'new_company_logo' => 'nullable|image|max:2048',
            'new_company_address' => 'nullable|string|max:500',
            'new_company_desc'    => 'nullable|string|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'work_name.required'              => 'Please enter your job title or position.',
            'company_id.required'             => 'Please select a company.',
            'company_id.exists'               => 'The selected company no longer exists.',
            'date_hired.required'             => 'Please enter the date you were hired.',
            'date_hired.before_or_equal'      => 'The hire date cannot be in the future.',
            'new_company_name.required'       => 'Please enter the company name.',
            'new_company_name.unique'         => 'A company with this name already exists.',
            'new_company_logo.image'          => 'The logo must be an image file.',
            'new_company_logo.max'            => 'The logo cannot exceed 2MB.',
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

    /**
     * Create the company as its own step, then auto-select it
     * for the work history dropdown.
     */
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

            // Auto-select the freshly created company
            $this->company_id = $company->id;

            // Close the form and clear the fields
            $this->showNewCompanyForm = false;
            $this->reset([
                'new_company_logo',
                'new_company_name',
                'new_company_address',
                'new_company_desc',
            ]);

            // Bust the cached companies list so the new one shows up
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
        $this->validate($this->rules(), $this->messages());

        try {
            DB::transaction(function () {
                // If marked current, unset all others
                if ($this->is_current_job) {
                    WorkHistory::where('user_id', Auth::id())
                        ->where('is_current_job', true)
                        ->update(['is_current_job' => false]);
                }

                WorkHistory::create([
                    'user_id'             => Auth::id(),
                    'work_name'           => trim($this->work_name),
                    'company_id'          => $this->company_id,
                    'date_hired'          => $this->date_hired,
                    'is_current_job'      => $this->is_current_job,
                    'is_current_employed' => $this->is_current_job,
                ]);

                // ===== Sync tracer study when this is the current job =====
                if ($this->is_current_job) {
                    $tracerStudy = TracerStudy::where('user_id', Auth::id())->first();

                    if ($tracerStudy) {
                        $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();

                        if ($employment) {
                            $employment->update([
                                'employment_status'    => 'employed',
                                'current_job_position' => trim($this->work_name),
                            ]);
                        }
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
