<?php

use App\Models\Batch;
use App\Models\UserProfile;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Propaganistas\LaravelPhone\Rules\Phone;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    // Column-backed fields
    public string $gender = 'male';
    public string $contact_number_1 = '';
    public ?string $contact_number_2 = null;

    // User fields — three-part name
    public string $first_name  = '';
    public string $middle_name = '';
    public string $last_name   = '';
    public string $email       = '';

    // Avatar
    public $avatarFile = null;
    public ?string $currentAvatar = null;

    // Address type toggle
    public string $address_type = 'philippines'; // 'philippines' | 'abroad'

    // Philippine address cascade
    public string $region_code = '';
    public string $province_code = '';
    public string $city_code = '';
    public string $barangay_code = '';

    public string $region_name = '';
    public string $province_name = '';
    public string $city_name = '';
    public string $barangay_name = '';

    public string $street_address = '';

    // International address (abroad)
    public string $intl_country = '';
    public string $intl_state   = '';
    public string $intl_city    = '';

    public bool $hasProfile = false;

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

    protected function rules(): array
    {
        return [
            'first_name'        => 'required|string|min:2|max:255',
            'middle_name'       => 'nullable|string|max:255',
            'last_name'         => 'required|string|min:2|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'gender'            => 'required|in:male,female,other',
            'contact_number_1' => [
                'required',
                'string',
                'max:20',
                new Phone(),
                function ($attribute, $value, $fail) {
                    $exists = UserProfile::where(function ($q) use ($value) {
                        $q->where('contact_number_1', $value)
                        ->orWhere('contact_number_2', $value);
                    })
                    ->where('id', '!=', $this->userProfileId())
                    ->exists();

                    if ($exists) {
                        $fail('This mobile number is already registered to another account.');
                    }
                },
            ],

            'contact_number_2' => [
                'nullable',
                'string',
                'max:20',
                new Phone(),
                function ($attribute, $value, $fail) {
                    if (! $value) return; // skip if empty

                    $exists = UserProfile::where(function ($q) use ($value) {
                        $q->where('contact_number_1', $value)
                        ->orWhere('contact_number_2', $value);
                    })
                    ->where('id', '!=', $this->userProfileId())
                    ->exists();

                    if ($exists) {
                        $fail('This alternate number is already registered to another account.');
                    }
                },
            ],
            'avatarFile'        => 'nullable|image|max:2048',

            'address_type'      => 'required|in:philippines,abroad',

            // PH-only
            'region_code'   => 'required_if:address_type,philippines|nullable|string',
            'province_code' => 'required_if:address_type,philippines|nullable|string',
            'city_code'     => 'required_if:address_type,philippines|nullable|string',
            'barangay_code' => 'required_if:address_type,philippines|nullable|string',

            // Abroad-only
            'intl_country'  => 'required_if:address_type,abroad|nullable|string|max:255',
            'intl_state'    => 'nullable|string|max:255',
            'intl_city'     => 'required_if:address_type,abroad|nullable|string|max:255',

            'street_address' => 'required|string|max:255',
        ];
    }

    protected function userProfileId(): ?int
    {
        return UserProfile::where('user_id', Auth::id())->value('id');
    }

    public function messages(): array
    {
        return [
            'first_name.required'       => 'The first name is required.',
            'first_name.min'            => 'The first name must be at least 2 characters.',
            'first_name.max'            => 'The first name may not be greater than 255 characters.',
            'last_name.required'        => 'The last name is required.',
            'last_name.min'             => 'The last name must be at least 2 characters.',
            'last_name.max'             => 'The last name may not be greater than 255 characters.',
            'email.required'            => 'The email field is required.',
            'email.email'               => 'The email must be a valid email address.',
            'email.unique'              => 'The email has already been taken.',
            'gender.required'           => 'Please select your gender.',
            'gender.in'                 => 'Please select a valid gender.',
            'contact_number_1.required' => 'The mobile number is required.',
            'contact_number_1.phone'    => 'Please enter a valid mobile number.',
            'contact_number_2.phone'    => 'Please enter a valid alternate number.',
            'avatarFile.image'          => 'The avatar must be an image file.',
            'avatarFile.max'            => 'The avatar may not be larger than 2MB.',
            'address_type.required'     => 'Please select whether you reside in the Philippines or abroad.',
            'region_code.required'      => 'Please select a region.',
            'province_code.required'    => 'Please select a province.',
            'city_code.required'        => 'Please select a city or municipality.',
            'barangay_code.required'    => 'Please select a barangay.',
            'intl_country.required_if'  => 'Please enter your country.',
            'intl_city.required_if'     => 'Please enter your city.',
            'street_address.required'   => 'Please enter your street address.',
        ];
    }

    protected function decodeLocation($profile): array
    {
        if (! $profile) {
            return [];
        }

        if (is_array($profile->location)) {
            return $profile->location;
        }

        if (is_string($profile->location)) {
            $decoded = json_decode($profile->location, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected function resolveBatchId(): int
    {
        $batchId = Batch::query()->orderBy('id')->value('id');

        if ($batchId) {
            return $batchId;
        }

        $numericTypes = ['integer', 'bigint', 'smallint', 'tinyint', 'decimal', 'float', 'double'];

        try {
            $columnType = Schema::getColumnType('batches', 'batch_name');
        } catch (\Throwable $e) {
            $columnType = 'string';
        }

        $batchName = in_array($columnType, $numericTypes, true) ? 1 : 'Unassigned';

        $batch = Batch::create(['batch_name' => $batchName]);

        return $batch->id;
    }

    public function mount()
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

        if ($profile) {
            $this->hasProfile = true;

            $this->gender           = $profile->gender ?? 'male';
            $this->contact_number_1 = $profile->contact_number_1 ?? '';
            $this->contact_number_2 = $profile->contact_number_2 ?? null;
            $this->currentAvatar    = $profile->avatar;

            $location = $this->decodeLocation($profile);

            $this->street_address = $location['street_address'] ?? '';

            // Detect address type from saved data (with PH fallback for legacy rows)
            $this->address_type = $location['address_type']
                ?? (!empty($location['region_code']) ? 'philippines' : (empty($location['intl_country']) ? 'philippines' : 'abroad'));

            if ($this->address_type === 'philippines') {
                $this->region_code   = $location['region_code'] ?? '';
                $this->region_name   = $location['region_name'] ?? '';
                $this->province_code = $location['province_code'] ?? '';
                $this->province_name = $location['province_name'] ?? '';
                $this->city_code     = $location['city_code'] ?? '';
                $this->city_name     = $location['city_name'] ?? '';
                $this->barangay_code = $location['barangay_code'] ?? '';
                $this->barangay_name = $location['barangay_name'] ?? '';
            } else {
                $this->intl_country = $location['intl_country'] ?? '';
                $this->intl_state   = $location['intl_state'] ?? '';
                $this->intl_city    = $location['intl_city'] ?? '';
            }
        }
    }

    // ===== Computed =====

    #[Computed]
    public function regions()
    {
        return app(PhAddressService::class)->regions();
    }

    #[Computed]
    public function provinces()
    {
        return $this->region_code
            ? app(PhAddressService::class)->provinces($this->region_code)
            : collect();
    }

    #[Computed]
    public function cities()
    {
        return $this->province_code
            ? app(PhAddressService::class)->cities($this->province_code)
            : collect();
    }

    #[Computed]
    public function barangays()
    {
        return $this->city_code
            ? app(PhAddressService::class)->barangays($this->city_code)
            : collect();
    }

    // ===== Cascade =====

    public function updatedRegionCode($value)
    {
        $this->province_code = '';
        $this->city_code     = '';
        $this->barangay_code = '';
        $this->province_name = '';
        $this->city_name     = '';
        $this->barangay_name = '';

        $this->region_name = optional(app(PhAddressService::class)->findByCode($value))->name ?? '';

        unset($this->provinces, $this->cities, $this->barangays);
    }

    public function updatedProvinceCode($value)
    {
        $this->city_code     = '';
        $this->barangay_code = '';
        $this->city_name     = '';
        $this->barangay_name = '';

        $this->province_name = optional(app(PhAddressService::class)->findByCode($value))->name ?? '';

        unset($this->cities, $this->barangays);
    }

    public function updatedCityCode($value)
    {
        $this->barangay_code = '';
        $this->barangay_name = '';

        $this->city_name = optional(app(PhAddressService::class)->findByCode($value))->name ?? '';

        unset($this->barangays);
    }

    public function updatedBarangayCode($value)
    {
        $this->barangay_name = optional(app(PhAddressService::class)->findByCode($value))->name ?? '';
    }

    public function updatedAddressType($value)
    {
        $this->resetErrorBag([
            'region_code', 'province_code', 'city_code', 'barangay_code',
            'intl_country', 'intl_state', 'intl_city',
        ]);
    }

    // ===== Save =====

    public function saveProfile()
    {
        $this->contact_number_2 = $this->contact_number_2 === '' ? null : $this->contact_number_2;

        $validated = $this->validate();

        $validated['first_name']     = $this->sanitizeData($validated['first_name']);
        $validated['middle_name']    = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $validated['last_name']      = $this->sanitizeData($validated['last_name']);
        $validated['email']          = $this->sanitizeData($validated['email']);
        $validated['street_address'] = $this->sanitizeData($validated['street_address']);

        $user = Auth::user();
        $service = app(PhAddressService::class);

        try {
            // The User model's `saving` hook auto-syncs `name` from the three parts.
            $user->update([
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
            ]);

            $existing = UserProfile::where('user_id', $user->id)->first();

            if ($this->address_type === 'philippines') {
                $region   = $service->findByCode($validated['region_code']);
                $province = $service->findByCode($validated['province_code']);
                $city     = $service->findByCode($validated['city_code']);
                $barangay = $service->findByCode($validated['barangay_code']);

                $fullAddress = collect([
                    $validated['street_address'],
                    $barangay->name ?? null,
                    $city->name ?? null,
                    $province->name ?? null,
                    $region->name ?? null,
                ])->filter()->implode(', ');

                $location = array_merge($existing?->location ?? [], [
                    'address_type'   => 'philippines',
                    'street_address' => $validated['street_address'],
                    'region_code'    => $validated['region_code'],
                    'region_name'    => $region->name ?? null,
                    'province_code'  => $validated['province_code'],
                    'province_name'  => $province->name ?? null,
                    'city_code'      => $validated['city_code'],
                    'city_name'      => $city->name ?? null,
                    'barangay_code'  => $validated['barangay_code'],
                    'barangay_name'  => $barangay->name ?? null,
                    'address'        => $fullAddress,

                    // Clear abroad keys
                    'intl_country'   => null,
                    'intl_state'     => null,
                    'intl_city'      => null,
                ]);
            } else {
                $fullAddress = collect([
                    $validated['street_address'],
                    $this->intl_city,
                    $this->intl_state,
                    $this->intl_country,
                ])->filter()->implode(', ');

                $location = array_merge($existing?->location ?? [], [
                    'address_type'   => 'abroad',
                    'street_address' => $validated['street_address'],
                    'intl_country'   => $this->intl_country,
                    'intl_state'     => $this->intl_state,
                    'intl_city'      => $this->intl_city,
                    'address'        => $fullAddress,

                    // Clear PH keys
                    'region_code'    => null,
                    'region_name'    => null,
                    'province_code'  => null,
                    'province_name'  => null,
                    'city_code'      => null,
                    'city_name'      => null,
                    'barangay_code'  => null,
                    'barangay_name'  => null,
                ]);
            }

            $avatarPath = $existing?->avatar;

            if ($this->avatarFile) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
                $avatarPath = $this->avatarFile->store('avatars', 'public');
            }

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar'           => $avatarPath,
                    'gender'           => $validated['gender'],
                    'contact_number_1' => $validated['contact_number_1'],
                    'contact_number_2' => $validated['contact_number_2'],
                    'location'         => $location,
                    'batch_id'         => $existing?->batch_id ?? $this->resolveBatchId(),
                    'is_private'       => $existing?->is_private ?? false,
                    'is_verified'      => $existing?->is_verified ?? false,
                ]
            );

            $profile->refresh();

            $this->avatarFile    = null;
            $this->currentAvatar = $avatarPath;
            $this->hasProfile    = true;

            session()->flash('success', 'Profile updated successfully.');
            return redirect()->route('alumni.profile');

        } catch (\Throwable $e) {
            logger()->error('Profile save failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while saving your profile: ' . $e->getMessage());
            return;
        }
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};