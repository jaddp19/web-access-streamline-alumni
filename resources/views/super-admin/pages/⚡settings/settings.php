<?php

use App\Models\Batch;
use App\Models\UserProfile;
use App\Services\PhAddressService;
use App\Support\SettingsRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

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

    #[Locked]
    public array $allowedTabs = ['appearance', 'profile'];

    // ===== Lifecycle =====

    public function mount(): void
    {
        $user = Auth::user();

        [$first, $middle, $last] = $this->resolveNameParts($user);

        $this->first_name  = $first;
        $this->middle_name = $middle;
        $this->last_name   = $last;
        $this->email       = $user->email;

        if (! $profile = $this->userProfile) {
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
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    #[Computed]
    public function avatarUrl(): ?string
    {
        $avatar = $this->userProfile?->avatar;

        if (! $avatar) {
            return null;
        }

        return filter_var($avatar, FILTER_VALIDATE_URL)
            ? $avatar
            : Storage::url($avatar);
    }

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name', 'desc')->get(['id', 'batch_name']);
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

    // ===== Tabs =====

    public function setTab(string $tab): void
    {
        if (! in_array($tab, $this->allowedTabs, true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // ===== Save =====

    public function saveProfile(): void
    {
        $this->validate(
            SettingsRules::profileDetails(),
            SettingsRules::messages()
        );

        $user = Auth::user();

        // Sanitize name parts + email once
        $first  = $this->sanitize($this->first_name);
        $middle = $this->middle_name ? $this->sanitize($this->middle_name) : null;
        $last   = $this->sanitize($this->last_name);
        $email  = Str::lower($this->sanitize($this->email));

        // The User model's `saving` hook auto-syncs `name` from the three parts.
        $user->update([
            'first_name'  => $first,
            'middle_name' => $middle,
            'last_name'   => $last,
            'email'       => $email,
        ]);

        // Refresh locals with sanitized values
        $this->first_name  = $first;
        $this->middle_name = $middle ?? '';
        $this->last_name   = $last;
        $this->email       = $email;

        // ===== Build full address =====
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

        // ===== Avatar handling =====
        $avatarPath = $existing?->avatar;

        if ($this->avatarFile) {
            // Delete old avatar to avoid orphaned files
            if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

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

    // ===== Helpers =====

    /**
     * Resolve name parts, splitting from `name` if the parts are legacy-empty.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    protected function resolveNameParts($user): array
    {
        $first  = $user->first_name;
        $middle = $user->middle_name;
        $last   = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split  = preg_split('/\s+/', trim($user->name));
            $first  = $split[0] ?? '';
            $last   = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2 ? implode(' ', array_slice($split, 1, -1)) : '';
        }

        return [$first ?? '', $middle ?? '', $last ?? ''];
    }

    protected function sanitize(string $value): string
    {
        return Str::of($value)->stripTags()->trim()->toString();
    }
};