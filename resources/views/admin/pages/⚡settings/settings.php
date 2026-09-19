<?php

use App\Models\Batch;
use App\Models\UserProfile;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app-admin')] class extends Component
{
    use WithFileUploads;

    public string $activeTab = 'appearance';

    // ===== Profile fields =====
    public ?int $batch_id = null;
    public string $gender = '';
    public string $contact_number_1 = '';
    public string $contact_number_2 = '';
    public string $street_address = '';

    // Location cascade
    public string $regionCode = '';
    public string $provinceCode = '';
    public string $cityCode = '';
    public string $barangayCode = '';

    // Avatar upload
    public $avatarFile = null;

    public function mount(): void
    {
        $profile = UserProfile::where('user_id', Auth::id())->first();

        if (! $profile) {
            return;
        }

        $this->batch_id         = $profile->batch_id;
        $this->gender           = $profile->gender ?? '';
        $this->contact_number_1 = $profile->contact_number_1 ?? '';
        $this->contact_number_2 = $profile->contact_number_2 ?? '';

        $location = $profile->location ?? [];

        $this->street_address = $location['street_address'] ?? '';
        $this->regionCode     = $location['region_code'] ?? '';
        $this->provinceCode   = $location['province_code'] ?? '';
        $this->cityCode       = $location['city_code'] ?? '';
        $this->barangayCode   = $location['barangay_code'] ?? '';
    }

    // ===== Computed =====

    #[Computed]
    public function userProfile(): ?UserProfile
    {
        return UserProfile::where('user_id', Auth::id())->first();
    }

    #[Computed]
    public function avatarUrl(): ?string
    {
        $avatar = $this->userProfile?->avatar;
        if (! $avatar) return null;

        return filter_var($avatar, FILTER_VALIDATE_URL)
            ? $avatar
            : \Illuminate\Support\Facades\Storage::url($avatar);
    }

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name', 'desc')->get();
    }

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

    // ===== Validation =====

    protected function profileRules(): array
    {
        return [
            'avatarFile'       => 'nullable|image|max:2048',
            'gender'           => 'required|in:male,female,other',
            'contact_number_1' => 'required|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'batch_id'         => 'required|exists:batches,id',
            'regionCode'       => 'required|string',
            'provinceCode'     => 'required|string',
            'cityCode'         => 'required|string',
            'barangayCode'     => 'required|string',
            'street_address'   => 'required|string|max:500',
        ];
    }

    protected function profileMessages(): array
    {
        return [
            'avatarFile.image'         => 'Avatar must be an image file.',
            'avatarFile.max'           => 'Avatar cannot exceed 2MB.',
            'gender.required'          => 'Please select your gender.',
            'contact_number_1.required'=> 'Mobile number is required.',
            'batch_id.required'        => 'Please select your batch.',
            'regionCode.required'      => 'Please select your region.',
            'provinceCode.required'    => 'Please select your province.',
            'cityCode.required'        => 'Please select your city/municipality.',
            'barangayCode.required'    => 'Please select your barangay.',
            'street_address.required'  => 'Street address is required.',
        ];
    }

    // ===== Actions =====

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
    }

    public function saveProfile(): void
    {
        $this->validate($this->profileRules(), $this->profileMessages());

        $service  = app(PhAddressService::class);
        $region   = $service->findByCode($this->regionCode);
        $province = $service->findByCode($this->provinceCode);
        $city     = $service->findByCode($this->cityCode);
        $barangay = $service->findByCode($this->barangayCode);

        $fullAddress = collect([
            $this->street_address,
            $barangay->name ?? null,
            $city->name ?? null,
            $province->name ?? null,
            $region->name ?? null,
        ])->filter()->implode(', ');

        $existing = $this->userProfile;

        $avatarPath = $existing?->avatar;
        if ($this->avatarFile) {
            $avatarPath = $this->avatarFile->store('avatars', 'public');
        }

        UserProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'avatar'           => $avatarPath,
                'gender'           => $this->gender,
                'contact_number_1' => $this->contact_number_1,
                'contact_number_2' => $this->contact_number_2 ?: null,
                'batch_id'         => $this->batch_id,
                'location' => array_merge(
                    $existing?->location ?? [],
                    [
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
                    ]
                ),
                'is_private'  => $existing?->is_private ?? false,
                'is_verified' => $existing?->is_verified ?? false,
            ]
        );

        $this->avatarFile = null;

        unset($this->userProfile, $this->avatarUrl);

        session()->flash('profile_success', 'Profile updated successfully.');
    }
};