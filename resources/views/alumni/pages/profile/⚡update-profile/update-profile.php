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

    // User fields — three-part name (matches users table)
    public string $first_name  = '';
    public string $middle_name = '';
    public string $last_name   = '';
    public string $email       = '';

    // Avatar
    public $avatarFile = null;
    public ?string $currentAvatar = null;

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
            'contact_number_1'  => ['required', 'string', 'max:20', new Phone('PH')],
            'contact_number_2'  => ['nullable', 'string', 'max:20', new Phone('PH')],
            'avatarFile'        => 'nullable|image|max:2048',

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
            'avatarFile.image'          => 'The avatar must be an image file.',
            'avatarFile.max'            => 'The avatar may not be larger than 2MB.',
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
            $this->region_code    = $location['region_code'] ?? '';
            $this->region_name    = $location['region_name'] ?? '';
            $this->province_code  = $location['province_code'] ?? '';
            $this->province_name  = $location['province_name'] ?? '';
            $this->city_code      = $location['city_code'] ?? '';
            $this->city_name      = $location['city_name'] ?? '';
            $this->barangay_code  = $location['barangay_code'] ?? '';
            $this->barangay_name  = $location['barangay_name'] ?? '';
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

            // Resolve names server-side (same as tracer study form)
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

            $location = [
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
            ];

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