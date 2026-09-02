<?php

use App\Models\Batch;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;

    public string $gender = 'male';
    public string $phone_number_1 = '';
    public ?string $phone_number_2 = null;
    public string $name = '';
    public string $email = '';

    public $avatarFile = null;            // newly picked file (temporary)
    public ?string $currentAvatar = null; // existing stored avatar path

    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?string $address = null;       // human-readable reverse-geocoded address

    public bool $hasProfile = false;      // tracks whether we're creating or updating

    protected function rules()
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'gender'            => 'required|in:male,female',
            'phone_number_1'    => 'required|digits:10',
            'phone_number_2'    => 'nullable|digits:10',
            'avatarFile'        => 'nullable|image|max:2048',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'gender.required' => 'The gender field is required',
            'phone_number_1.required' => 'The contact number 1 field is required',
            'phone_number_1.digits' => 'The contact number 1 must be exactly 10 digits',
            'phone_number_2.digits' => 'The contact number 2 must be exactly 10 digits',
            'avatarFile.image' => 'The avatar must be an image file.',
            'avatarFile.max' => 'The avatar may not be larger than 2MB.',
            'latitude.required' => 'Please pin your location on the map.',
            'longitude.required' => 'Please pin your location on the map.',
        ];
    }

    /**
     * Safely turn the profile's `location` column into an array,
     * regardless of whether the model casts it to array or not.
     */
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

    /**
     * Get an existing batch id to satisfy the required batch_id foreign key,
     * or create a fallback batch if none exist yet.
     *
     * NOTE: The live database's `batches.batch_name` column does not match
     * the string type shown in the migration file — it is actually numeric
     * (confirmed by the "Incorrect integer value" SQL error). We inspect the
     * real column type at runtime instead of assuming, so this keeps working
     * no matter which type is actually in the database.
     */
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
            // If introspection itself fails for any reason, fall back to string.
            $columnType = 'string';
        }

        $batchName = in_array($columnType, $numericTypes, true) ? 1 : 'Unassigned';

        $batch = Batch::create([
            'batch_name' => $batchName,
        ]);

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

            $location = $this->decodeLocation($profile);

            $this->gender         = $location['gender'] ?? 'male';
            $this->phone_number_1 = $location['phone_number_1'] ?? '';
            $this->phone_number_2 = $location['phone_number_2'] ?? null;
            $this->latitude       = isset($location['latitude']) ? (float) $location['latitude'] : null;
            $this->longitude      = isset($location['longitude']) ? (float) $location['longitude'] : null;
            $this->address        = $location['address'] ?? null;
            $this->currentAvatar  = $profile->avatar;
        }
    }

    // Called from the map/geolocation JS: this.$wire.setLocation(lat, lng, address)
    public function setLocation($lat, $lng, $address = null)
    {
        $this->latitude  = round((float) $lat, 6);
        $this->longitude = round((float) $lng, 6);
        $this->address   = $address ?: $this->address;

        $this->resetErrorBag(['latitude', 'longitude']);
    }

    public function saveProfile()
    {
        // Normalize empty strings to null so "nullable" rules actually apply.
        $this->phone_number_2 = $this->phone_number_2 === '' ? null : $this->phone_number_2;

        $validated = $this->validate();

        $validated['name']  = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        $user = Auth::user();

        try {
            $user->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);

            $profile = UserProfile::where('user_id', $user->id)->first();

            $location = $this->decodeLocation($profile);
            $location = array_merge($location, [
                'gender'         => $validated['gender'],
                'phone_number_1' => $validated['phone_number_1'],
                'phone_number_2' => $validated['phone_number_2'],
                'latitude'       => $validated['latitude'],
                'longitude'      => $validated['longitude'],
                'address'        => $this->address,
            ]);

            $avatarPath = $profile->avatar ?? '';

            if ($this->avatarFile) {
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }

                $avatarPath = $this->avatarFile->store('avatars', 'public');
            }

            if ($profile) {
                // ----- UPDATE existing profile -----
                $profile->update([
                    'avatar'   => $avatarPath,
                    'location' => $location,
                ]);
            } else {
                // ----- CREATE profile for the first time -----
                $profile = UserProfile::create([
                    'user_id'     => $user->id,
                    'avatar'      => $avatarPath, // may be '' if no photo was uploaded yet
                    'location'    => $location,
                    'batch_id'    => $this->resolveBatchId(),
                    'is_private'  => false,
                    'is_verified' => false,
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
