<?php

use App\Models\Company;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public Company $company;

    public string $company_name = '';
    public string $company_desc = '';
    public $company_logo = null;
    public bool $remove_logo = false;

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

    public function mount(Company $company): void
    {
        $this->company      = $company;
        $this->company_name = $company->company_name;
        $this->company_desc = $company->company_desc ?? '';

        $this->hydrateAddress($company->company_address);
    }

    // =========================================================
    //  ADDRESS HYDRATION (reverse-parse from the saved string)
    // =========================================================

    /**
     * Best-effort parse of the composed `company_address` string back into
     * the structured fields.
     *
     * If a segment matches a PH region name, we treat it as a PH address and
     * try to match province + city by name. Otherwise we assume abroad and
     * split from the end: last = country, second-last = state, rest = city.
     */
    protected function hydrateAddress(?string $address): void
    {
        if (blank($address)) {
            return;
        }

        $segments = array_values(array_filter(
            array_map('trim', explode(',', $address)),
            fn ($s) => $s !== ''
        ));

        if (empty($segments)) {
            return;
        }

        $service     = app(PhAddressService::class);
        $usedIndexes = [];

        // ---- Try to find a region name in the segments ----
        $matchedRegion = null;
        foreach ($service->regions() as $region) {
            $idx = array_search($region->name, $segments, true);
            if ($idx !== false) {
                $matchedRegion  = $region;
                $usedIndexes[]  = $idx;
                break;
            }
        }

        if (! $matchedRegion) {
            // Not obviously PH — treat as abroad and split from the end.
            $this->address_type = 'abroad';

            $count = count($segments);
            if ($count >= 3) {
                $this->intl_country = array_pop($segments);
                $this->intl_state   = array_pop($segments);
                $this->intl_city    = implode(', ', $segments);
            } elseif ($count === 2) {
                $this->intl_country = $segments[1];
                $this->intl_city    = $segments[0];
            } else {
                $this->intl_city = $segments[0];
            }

            return;
        }

        $this->address_type = 'philippines';
        $this->regionCode   = $matchedRegion->code;

        // ---- Province ----
        $matchedProvince = null;
        foreach ($service->provinces($matchedRegion->code) as $province) {
            $idx = array_search($province->name, $segments, true);
            if ($idx !== false) {
                $matchedProvince = $province;
                $usedIndexes[]   = $idx;
                break;
            }
        }

        if ($matchedProvince) {
            $this->provinceCode = $matchedProvince->code;

            // ---- City ----
            $matchedCity = null;
            foreach ($service->cities($matchedProvince->code) as $city) {
                $idx = array_search($city->name, $segments, true);
                if ($idx !== false) {
                    $matchedCity  = $city;
                    $usedIndexes[] = $idx;
                    break;
                }
            }

            if ($matchedCity) {
                $this->cityCode = $matchedCity->code;
            }
        }

        // ---- Whatever's left becomes the street address ----
        $streetParts = [];
        foreach ($segments as $i => $segment) {
            if (! in_array($i, $usedIndexes, true)) {
                $streetParts[] = $segment;
            }
        }
        $this->street_address = implode(', ', $streetParts);
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'company_name' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_desc' => ['nullable', 'string', 'max:2000'],
            'company_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
                'dimensions:min_width=32,min_height=32,max_width=2000,max_height=2000',
            ],
            'remove_logo' => ['boolean'],

            'address_type' => ['required', 'in:philippines,abroad'],

            'regionCode'     => ['required_if:address_type,philippines', 'nullable', 'string'],
            'provinceCode'   => ['required_if:address_type,philippines', 'nullable', 'string'],
            'cityCode'       => ['required_if:address_type,philippines', 'nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:500'],

            'intl_country' => ['required_if:address_type,abroad', 'nullable', 'string', 'max:255'],
            'intl_state'   => ['nullable', 'string', 'max:255'],
            'intl_city'    => ['required_if:address_type,abroad', 'nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.min'      => 'Company name must be at least 2 characters.',
            'company_name.unique'   => 'A company with this name already exists.',
            'company_name.max'      => 'Company name cannot exceed 255 characters.',
            'company_desc.max'      => 'Description cannot exceed 2000 characters.',
            'company_logo.image'    => 'The logo must be a valid image file.',
            'company_logo.mimes'    => 'The logo must be a JPG, PNG, WebP, or SVG file.',
            'company_logo.max'      => 'The logo must not exceed 2MB.',
            'company_logo.dimensions' => 'The logo must be between 32×32 and 2000×2000 pixels.',

            'address_type.required'    => 'Please select the company location.',
            'regionCode.required_if'   => 'Please select a region.',
            'provinceCode.required_if' => 'Please select a province.',
            'cityCode.required_if'     => 'Please select a city / municipality.',
            'intl_country.required_if' => 'Please enter the country.',
            'intl_city.required_if'    => 'Please enter the city.',
        ];
    }

    // =========================================================
    //  HOOKS
    // =========================================================

    public function updatedCompanyLogo(): void
    {
        $this->validateOnly('company_logo');
    }

    public function updatedRemoveLogo(): void
    {
        if ($this->remove_logo) {
            $this->company_logo = null;
            $this->resetErrorBag('company_logo');
        }
    }

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
        $this->cityCode     = '';
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

        // ---- Compose address string ----
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

        $oldLogoPath = $this->company->company_logo;
        $newLogoPath = null;
        $clearLogo   = false;

        try {
            // ---- 1. Decide logo action ----
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
            DB::transaction(function () use ($validated, $finalLogo, $companyAddress) {
                $this->company->update([
                    'company_name'    => trim(strip_tags($validated['company_name'])),
                    'company_address' => $companyAddress ?: null,
                    'company_desc'    => filled($validated['company_desc'])
                        ? trim(strip_tags($validated['company_desc']))
                        : null,
                    'company_logo'    => $finalLogo,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
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

        // ---- 3. Cleanup OLD logo AFTER commit ----
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