<?php

use App\Models\Company;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public string $company_name = '';
    public string $company_desc = '';
    public $company_logo = null;

    // ---- Address (Philippines) ----
    public string $address_type = 'philippines';
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $street_address = '';

    // ---- Address (Abroad) ----
    public string $intl_country = '';
    public string $intl_state = '';
    public string $intl_city = '';

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'min:2', 'max:255', 'unique:companies,company_name'],
            'company_desc' => ['nullable', 'string', 'max:2000'],
            'company_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
                'dimensions:min_width=32,min_height=32,max_width=2000,max_height=2000',
            ],

            'address_type' => ['required', 'in:philippines,abroad'],

            // Philippines
            'regionCode'     => ['required_if:address_type,philippines', 'nullable', 'string'],
            'provinceCode'   => ['required_if:address_type,philippines', 'nullable', 'string'],
            'cityCode'       => ['required_if:address_type,philippines', 'nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:500'],

            // Abroad
            'intl_country' => ['required_if:address_type,abroad', 'nullable', 'string', 'max:255'],
            'intl_state'   => ['nullable', 'string', 'max:255'],
            'intl_city'    => ['required_if:address_type,abroad', 'nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'company_name.required'   => 'Company name is required.',
            'company_name.min'        => 'Company name must be at least 2 characters.',
            'company_name.unique'     => 'A company with this name already exists.',
            'company_name.max'        => 'Company name cannot exceed 255 characters.',
            'company_desc.max'        => 'Description cannot exceed 2000 characters.',
            'company_logo.image'      => 'The logo must be a valid image file.',
            'company_logo.mimes'      => 'The logo must be a JPG, PNG, WebP, or SVG file.',
            'company_logo.max'        => 'The logo must not exceed 2MB.',
            'company_logo.dimensions' => 'The logo must be between 32×32 and 2000×2000 pixels.',

            'address_type.required'    => 'Please select the company location.',
            'regionCode.required_if'   => 'Please select a region.',
            'provinceCode.required_if' => 'Please select a province.',
            'cityCode.required_if'     => 'Please select a city / municipality.',
            'intl_country.required_if' => 'Please enter the country.',
            'intl_city.required_if'    => 'Please enter the city.',
        ];
    }

    public function updatedCompanyLogo(): void
    {
        $this->validateOnly('company_logo');
    }

    // =========================================================
    //  HOOKS
    // =========================================================

    public function updatedAddressType(): void
    {
        $this->resetErrorBag([
            'regionCode', 'provinceCode', 'cityCode', 'street_address',
            'intl_country', 'intl_state', 'intl_city',
        ]);
    }

    public function updatedRegionCode(): void
    {
        $this->provinceCode = '';
        $this->cityCode = '';
        $this->resetErrorBag(['provinceCode', 'cityCode']);
    }

    public function updatedProvinceCode(): void
    {
        $this->cityCode = '';
        $this->resetErrorBag('cityCode');
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

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

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {

        $validated = $this->validate();

        // ---- Build the composed address string ----
        $service = app(PhAddressService::class);

        if ($this->address_type === 'philippines') {
            $region   = $service->findByCode($this->regionCode);
            $province = $service->findByCode($this->provinceCode);
            $city     = $service->findByCode($this->cityCode);

            $companyAddress = collect([
                $this->street_address,
                $city->name ?? null,
                $province->name ?? null,
                $region->name ?? null,
            ])->filter(fn ($p) => filled($p))->implode(', ');
        } else {
            $companyAddress = collect([
                $this->intl_city,
                $this->intl_state,
                $this->intl_country,
            ])->filter(fn ($p) => filled($p))->implode(', ');
        }

        $logoPath = null;

        try {
            if ($this->company_logo) {
                $logoPath = $this->company_logo->store('company-logos', 'public');
            }

            DB::transaction(function () use ($validated, $logoPath, $companyAddress) {
                Company::create([
                    'company_name'    => trim(strip_tags($validated['company_name'])),
                    'company_address' => $companyAddress ?: null,
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