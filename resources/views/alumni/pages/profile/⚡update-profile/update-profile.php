<?php

use App\Models\Batch;
use App\Models\UserProfile;
use App\Services\PhAddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Rules\Phone;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    // Column-backed fields
    public string $gender = 'male';
    public string $contact_number_1 = '';
    public ?string $contact_number_2 = null;

    // User fields
    public string $name = '';
    public string $email = '';

    // Avatar
    public $avatarFile = null;
    public ?string $currentAvatar = null;

    // Coordinates
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?string $address = null;

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

    public bool $hasProfile = false;

    protected function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'gender'            => 'required|in:male,female,other',
            'contact_number_1'  => ['required', 'string', 'max:20', new Phone('PH')],
            'contact_number_2'  => ['nullable', 'string', 'max:20', new Phone('PH')],
            'avatarFile'        => 'nullable|image|max:2048',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',

            'region_code'       => 'required|string',
            'province_code'     => 'required|string',
            'city_code'         => 'required|string',
            'barangay_code'     => 'required|string',
            'street_address'    => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'The name field is required.',
            'email.required'            => 'The email field is required.',
            'email.email'               => 'The email must be a valid email address.',
            'email.unique'              => 'The email has already been taken.',
            'gender.required'           => 'Please select your gender.',
            'gender.in'                 => 'Please select a valid gender.',
            'contact_number_1.required' => 'The mobile number is required.',
            'avatarFile.image'          => 'The avatar must be an image file.',
            'avatarFile.max'            => 'The avatar may not be larger than 2MB.',
            'latitude.required'         => 'Please pin your location on the map.',
            'longitude.required'        => 'Please pin your location on the map.',
            'region_code.required'      => 'Please select a region.',
            'province_code.required'    => 'Please select a province.',
            'city_code.required'        => 'Please select a city or municipality.',
            'barangay_code.required'    => 'Please select a barangay.',
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

        $this->name  = $user->name;
        $this->email = $user->email;

        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($profile) {
            $this->hasProfile = true;

            // ----- Column-backed fields -----
            $this->gender           = $profile->gender ?? 'male';
            $this->contact_number_1 = $profile->contact_number_1 ?? '';
            $this->contact_number_2 = $profile->contact_number_2 ?? null;
            $this->currentAvatar    = $profile->avatar;

            // ----- Location JSON fields -----
            $location = $this->decodeLocation($profile);

            $this->latitude       = isset($location['latitude']) ? (float) $location['latitude'] : null;
            $this->longitude      = isset($location['longitude']) ? (float) $location['longitude'] : null;
            $this->address        = $location['address'] ?? null;

            $this->region_code    = $location['region_code'] ?? '';
            $this->province_code  = $location['province_code'] ?? '';
            $this->city_code      = $location['city_code'] ?? '';
            $this->barangay_code  = $location['barangay_code'] ?? '';
            $this->region_name    = $location['region_name'] ?? '';
            $this->province_name  = $location['province_name'] ?? '';
            $this->city_name      = $location['city_name'] ?? '';
            $this->barangay_name  = $location['barangay_name'] ?? '';
            $this->street_address = $location['street_address'] ?? '';
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

    public function setLocation($lat, $lng, $address = null)
    {
        $this->latitude  = round((float) $lat, 6);
        $this->longitude = round((float) $lng, 6);
        $this->address   = $address ?: $this->address;

        $this->resetErrorBag(['latitude', 'longitude']);
    }

    // ===== Save =====

    public function saveProfile()
    {
        // Normalize empty strings to null so "nullable" rules apply.
        $this->contact_number_2 = $this->contact_number_2 === '' ? null : $this->contact_number_2;

        $validated = $this->validate();

        $validated['name']           = $this->sanitizeData($validated['name']);
        $validated['email']          = $this->sanitizeData($validated['email']);
        $validated['street_address'] = $this->sanitizeData($validated['street_address']);

        $user = Auth::user();

        try {
            $user->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);

            $profile = UserProfile::where('user_id', $user->id)->first();

            $composedPhAddress = collect([
                $validated['street_address'],
                $this->barangay_name,
                $this->city_name,
                $this->province_name,
                $this->region_name,
            ])->filter()->implode(', ');

            // Location JSON — no longer contains gender/phone (they have columns now)
            $location = $this->decodeLocation($profile);
            $location = array_merge($location, [
                'latitude'       => $validated['latitude'],
                'longitude'      => $validated['longitude'],
                'address'        => $this->address,

                'region_code'    => $validated['region_code'],
                'province_code'  => $validated['province_code'],
                'city_code'      => $validated['city_code'],
                'barangay_code'  => $validated['barangay_code'],
                'region_name'    => $this->region_name,
                'province_name'  => $this->province_name,
                'city_name'      => $this->city_name,
                'barangay_name'  => $this->barangay_name,
                'street_address' => $validated['street_address'],
                'ph_address'     => $composedPhAddress,
            ]);

            // Strip legacy keys from location JSON if they exist
            unset(
                $location['gender'],
                $location['phone_number_1'],
                $location['phone_number_2']
            );

            $avatarPath = $profile->avatar ?? '';

            if ($this->avatarFile) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }

                $avatarPath = $this->avatarFile->store('avatars', 'public');
            }

            if ($profile) {
                $profile->update([
                    'avatar'           => $avatarPath,
                    'gender'           => $validated['gender'],
                    'contact_number_1' => $validated['contact_number_1'],
                    'contact_number_2' => $validated['contact_number_2'],
                    'location'         => $location,
                ]);
            } else {
                $profile = UserProfile::create([
                    'user_id'          => $user->id,
                    'avatar'           => $avatarPath,
                    'gender'           => $validated['gender'],
                    'contact_number_1' => $validated['contact_number_1'],
                    'contact_number_2' => $validated['contact_number_2'],
                    'location'         => $location,
                    'batch_id'         => $this->resolveBatchId(),
                    'is_private'       => false,
                    'is_verified'      => false,
                ]);
            }

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