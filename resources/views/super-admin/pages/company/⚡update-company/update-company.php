<?php

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public Company $company;

    public string $company_name = '';
    public string $company_address = '';
    public string $company_desc = '';
    public $company_logo = null;
    public bool $remove_logo = false;

    public function mount(Company $company): void
    {
        $this->company         = $company;
        $this->company_name    = $company->company_name;
        $this->company_address = $company->company_address ?? '';
        $this->company_desc    = $company->company_desc ?? '';
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'company_name'    => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_desc'    => ['nullable', 'string', 'max:2000'],
            'company_logo'    => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
                'dimensions:min_width=32,min_height=32,max_width=2000,max_height=2000',
            ],
            'remove_logo'     => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.min'      => 'Company name must be at least 2 characters.',
            'company_name.unique'   => 'A company with this name already exists.',
            'company_name.max'      => 'Company name cannot exceed 255 characters.',
            'company_address.max'   => 'Address cannot exceed 500 characters.',
            'company_desc.max'      => 'Description cannot exceed 2000 characters.',
            'company_logo.image'    => 'The logo must be a valid image file.',
            'company_logo.mimes'    => 'The logo must be a JPG, PNG, WebP, or SVG file.',
            'company_logo.max'      => 'The logo must not exceed 2MB.',
            'company_logo.dimensions' => 'The logo must be between 32×32 and 2000×2000 pixels.',
        ];
    }

    /** Live-validate as soon as a new file is picked. */
    public function updatedCompanyLogo(): void
    {
        $this->validateOnly('company_logo');
    }

    /** Picking a new file cancels any pending "remove" intent. */
    public function updatedRemoveLogo(): void
    {
        if ($this->remove_logo) {
            $this->company_logo = null;
            $this->resetErrorBag('company_logo');
        }
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {
        abort_unless(auth()->user()?->can('manage-companies'), 403);

        $validated = $this->validate();

        $oldLogoPath = $this->company->company_logo;
        $newLogoPath = null;
        $clearLogo   = false;

        try {
            // ---- 1. Decide logo action (store file, don't delete old yet) ----
            if ($this->company_logo) {
                $newLogoPath = $this->company_logo->store('company-logos', 'public');
                $finalLogo   = $newLogoPath;
            } elseif ($this->remove_logo) {
                $finalLogo = null;
                $clearLogo = true;
            } else {
                $finalLogo = $oldLogoPath;
            }

            // ---- 2. Update DB in transaction ----
            DB::transaction(function () use ($validated, $finalLogo) {
                $this->company->update([
                    'company_name'    => trim(strip_tags($validated['company_name'])),
                    'company_address' => filled($validated['company_address'])
                        ? trim(strip_tags($validated['company_address']))
                        : null,
                    'company_desc'    => filled($validated['company_desc'])
                        ? trim(strip_tags($validated['company_desc']))
                        : null,
                    'company_logo'    => $finalLogo,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Roll back new file on failure.
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            if ($e->getCode() === '23000') {
                $this->addError('company_name', 'This company name was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update company. Please try again.');
            return;
        } catch (\Throwable $e) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            report($e);
            session()->flash('error', 'Could not update company. Please try again.');
            return;
        }

        // ---- 3. Cleanup OLD logo AFTER commit (only if we replaced/removed it) ----
        if ($oldLogoPath && ($newLogoPath || $clearLogo)) {
            $this->deleteLogoFile($oldLogoPath, $this->company->id);
        }

        Cache::forget('companies:count');

        session()->flash('success', 'Company updated successfully.');

        return redirect()->route('super-admin.company.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Only delete a logo file if:
     * - it's not an external URL,
     * - it's not a public-folder asset (/imgs/, /storage/),
     * - no OTHER company still references the same path.
     */
    protected function deleteLogoFile(?string $path, ?int $exceptId = null): void
    {
        if (blank($path)) return;
        if (filter_var($path, FILTER_VALIDATE_URL)) return;
        if (str_starts_with($path, '/imgs/') || str_starts_with($path, '/storage/')) return;

        $stillUsed = Company::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('company_logo', $path)
            ->exists();

        if ($stillUsed) return;

        $relative = ltrim($path, '/');
        Storage::disk('public')->delete($relative);
    }
};