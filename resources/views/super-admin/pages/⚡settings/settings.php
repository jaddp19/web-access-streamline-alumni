<?php

use App\Models\Batch;
use App\Models\UserProfile;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Rules\Phone;

new #[Layout('layouts::app-settings')] class extends Component
{
    use WithFileUploads;

    public string $activeTab = 'appearance';

    // ===== User fields =====
    public string $first_name  = '';
    public string $middle_name = '';
    public string $last_name   = '';
    public string $email       = '';

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

    /**
     * Composed full name — for avatar fallback.
     */
    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function mount(): void
    {
        $user = Auth::user();

        // Hydrate three-part name. If empty (legacy user), split from `name`.
        $first  = $user->first_name;
        $middle = $user->middle_name;
        $last   = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split  = preg_split('/\s+/', trim($user->name));
            $first  = $split[0] ?? '';
            $last   = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2
                ? implode(' ', array_slice($split, 1, -1))
                : '';
        }

        $this->first_name  = $first ?? '';
        $this->middle_name = $middle ?? '';
        $this->last_name   = $last ?? '';

        $this->email = $user->email;

        $profile = UserProfile::where('user_id', $user->id)->first();

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
            'first_name'       => 'required|string|min:2|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|min:2|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'avatarFile'       => 'nullable|image|max:2048',
            'gender'           => 'required|in:male,female,other',
            'contact_number_1' => ['required', 'string', 'max:20', new Phone()],
            'contact_number_2' => ['nullable', 'string', 'max:20', new Phone()],
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
            'first_name.required'       => 'First name is required.',
            'first_name.min'            => 'First name must be at least 2 characters.',
            'first_name.max'            => 'First name may not be greater than 255 characters.',
            'last_name.required'        => 'Last name is required.',
            'last_name.min'             => 'Last name must be at least 2 characters.',
            'last_name.max'             => 'Last name may not be greater than 255 characters.',
            'email.required'            => 'Email is required.',
            'email.email'               => 'Please enter a valid email address.',
            'email.unique'              => 'This email is already registered.',
            'avatarFile.image'          => 'Avatar must be an image file.',
            'avatarFile.max'            => 'Avatar cannot exceed 2MB.',
            'gender.required'           => 'Please select your gender.',
            'contact_number_1.required' => 'Mobile number is required.',
            'contact_number_1.phone'    => 'Please enter a valid mobile number.',
            'contact_number_2.phone'    => 'Please enter a valid alternate number.',
            'batch_id.required'         => 'Please select your batch.',
            'regionCode.required'       => 'Please select your region.',
            'provinceCode.required'     => 'Please select your province.',
            'cityCode.required'         => 'Please select your city/municipality.',
            'barangayCode.required'     => 'Please select your barangay.',
            'street_address.required'   => 'Street address is required.',
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

        $user = Auth::user();

        // The User model's `saving` hook auto-syncs `name` from the three parts.
        $user->update([
            'first_name'  => Str::of($this->first_name)->stripTags()->trim()->toString(),
            'middle_name' => $this->middle_name ? Str::of($this->middle_name)->stripTags()->trim()->toString() : null,
            'last_name'   => Str::of($this->last_name)->stripTags()->trim()->toString(),
            'email'       => Str::of($this->email)->stripTags()->trim()->toString(),
        ]);

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
            ['user_id' => $user->id],
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

        unset($this->userProfile, $this->avatarUrl, $this->fullName);

        session()->flash('profile_success', 'Profile updated successfully.');
    }
};