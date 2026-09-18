<?php

use App\Models\CivilStatusEmployment;
use App\Models\Company;
use App\Models\TracerStudy;
use App\Models\WorkHistory;
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

    public function mount(WorkHistory $history): void
    {
        abort_unless($history->user_id === Auth::id(), 403);

        $this->history = $history->load('company');

        $this->work_name      = $history->work_name ?? '';
        $this->company_id     = $history->company_id;
        $this->date_hired     = $history->date_hired?->format('Y-m-d') ?? '';
        $this->is_current_job = (bool) $history->is_current_job;
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
            'work_name.required'         => 'Please enter your job title or position.',
            'company_id.required'        => 'Please select a company.',
            'company_id.exists'          => 'The selected company no longer exists.',
            'date_hired.required'        => 'Please enter the date you were hired.',
            'date_hired.before_or_equal' => 'The hire date cannot be in the future.',
            'new_company_name.required'  => 'Please enter the company name.',
            'new_company_name.unique'    => 'A company with this name already exists.',
            'new_company_logo.image'     => 'The logo must be an image file.',
            'new_company_logo.max'       => 'The logo cannot exceed 2MB.',
        ];
    }

    // ---- Computed ----

    #[Computed]
    public function companies()
    {
        return Company::orderBy('company_name')
            ->get(['id', 'company_name', 'company_logo', 'company_address']);
    }

    // ---- Tracer sync helper ----

    /**
     * Sync CivilStatusEmployment with the user's current job state.
     * If a current job exists → employment_status = employed.
     * Otherwise → leave the tracer as-is (don't force unemployment).
     */
    /**
     * Sync CivilStatusEmployment with the user's current job state.
     *
     *   Current job exists  → employment_status = employed + position
     *   No current job      → employment_status = unemployed + clear employment fields
     *
     * Matches the same save pattern used by the tracer study form.
     */
    protected function syncTracerEmployment(): void
    {
        $tracerStudy = TracerStudy::where('user_id', Auth::id())->first();
        if (! $tracerStudy) return;

        $employment = CivilStatusEmployment::where('tracer_study_id', $tracerStudy->id)->first();
        if (! $employment) return;

        $current = WorkHistory::where('user_id', Auth::id())
            ->where('is_current_job', true)
            ->latest('date_hired')
            ->first();

        if ($current) {
            // Has a current job → mark as employed
            $employment->update([
                'employment_status'    => 'employed',
                'current_job_position' => $current->work_name ?: 'Position not specified',
            ]);
        } else {
            // No current job → mark as unemployed + clear employment-only fields
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

    public function updateWorkHistory(): void
    {
        $this->validate($this->rules(), $this->messages());

        try {
            DB::transaction(function () {
                // If this one is marked current, unset all OTHERS (not this one)
                if ($this->is_current_job) {
                    WorkHistory::where('user_id', Auth::id())
                        ->where('id', '!=', $this->history->id)
                        ->where('is_current_job', true)
                        ->update(['is_current_job' => false]);
                }

                $this->history->update([
                    'work_name'           => trim($this->work_name),
                    'company_id'          => $this->company_id,
                    'date_hired'          => $this->date_hired,
                    'is_current_job'      => $this->is_current_job,
                    'is_current_employed' => $this->is_current_job,
                ]);

                // Sync tracer study after the history record changes
                $this->syncTracerEmployment();
            });

            session()->flash('success', 'Work experience updated successfully.');
            $this->redirect(route('alumni.profile'), navigate: true);
        } catch (\Throwable $e) {
            logger()->error('Work history update failed: ' . $e->getMessage(), [
                'user_id'    => Auth::id(),
                'history_id' => $this->history->id,
                'trace'      => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteWorkHistory(): void
    {
        try {
            DB::transaction(function () {
                $this->history->delete();

                // If we just deleted the current job, re-sync the tracer
                $this->syncTracerEmployment();
            });

            session()->flash('success', 'Work experience deleted.');
            $this->redirect(route('alumni.profile'), navigate: true);
        } catch (\Throwable $e) {
            logger()->error('Work history delete failed: ' . $e->getMessage(), [
                'user_id'    => Auth::id(),
                'history_id' => $this->history->id,
            ]);

            session()->flash('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
};
