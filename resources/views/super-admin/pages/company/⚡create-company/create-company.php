<?php

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public string $company_name = '';
    public string $company_address = '';
    public string $company_desc = '';
    public $company_logo = null;

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'company_name'    => ['required', 'string', 'min:2', 'max:255', 'unique:companies,company_name'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_desc'    => ['nullable', 'string', 'max:2000'],
            'company_logo'    => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
                'dimensions:min_width=32,min_height=32,max_width=2000,max_height=2000',
            ],
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

    public function updatedCompanyLogo(): void
    {
        $this->validateOnly('company_logo');
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {
        abort_unless(auth()->user()?->can('manage-companies'), 403);

        $validated = $this->validate();

        $logoPath = null;

        try {
            if ($this->company_logo) {
                $logoPath = $this->company_logo->store('company-logos', 'public');
            }

            DB::transaction(function () use ($validated, $logoPath) {
                Company::create([
                    'company_name'    => trim(strip_tags($validated['company_name'])),
                    'company_address' => filled($validated['company_address'])
                        ? trim(strip_tags($validated['company_address']))
                        : null,
                    'company_desc'    => filled($validated['company_desc'])
                        ? trim(strip_tags($validated['company_desc']))
                        : null,
                    'company_logo'    => $logoPath,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            if ($e->getCode() === '23000') {
                $this->addError('company_name', 'This company name was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create company. Please try again.');
            return;
        } catch (\Throwable $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            report($e);
            session()->flash('error', 'Could not create company. Please try again.');
            return;
        }

        Cache::forget('companies:count');

        session()->flash('success', 'Company created successfully.');

        return redirect()->route('super-admin.company.view');
    }
};