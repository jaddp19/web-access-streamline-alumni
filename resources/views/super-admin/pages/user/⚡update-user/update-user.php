<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public User $user;

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';
    public bool $resetPassword = false;
    public ?string $selectedRole = null;

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    /**
     * Default password for the currently selected role.
     * Format: csav.{role-slug}  →  csav.alumni, csav.program-head, csav.registrar
     */
    #[Computed]
    public function generatedPassword(): ?string
    {
        if (blank($this->selectedRole)) {
            return null;
        }

        return 'csav.' . Str::of($this->selectedRole)
            ->lower()
            ->replace(' ', '-')
            ->toString();
    }

    /**
     * Editable roles (excludes super admin).
     * Returns an array of strings — plain Cache::get/put, no persist:true,
     * no Eloquent models crossing the cache boundary.
     */
    #[Computed]
    public function roles(): array
    {
        $cacheKey = 'roles:editable-list:v2';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = Role::query()
                ->where('name', '!=', 'super admin')
                ->orderBy('name')
                ->pluck('name')
                ->all();

            Cache::put($cacheKey, $cached, now()->addMinutes(10));
        }

        return $cached;
    }

    /**
     * True if this user is the currently authenticated user.
     */
    #[Computed]
    public function isSelf(): bool
    {
        return auth()->id() === $this->user->id;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],

            'school_id' => [
                'required',
                'string',
                'max:9',
                Rule::unique('users', 'school_id')->ignore($this->user->id),
            ],

            'selectedRole' => [
                'nullable',
                'string',
                Rule::in($this->roles),
            ],

            'resetPassword' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'   => 'The first name is required.',
            'first_name.min'        => 'The first name must be at least 2 characters.',
            'first_name.max'        => 'The first name may not be greater than 255 characters.',
            'last_name.required'    => 'The last name is required.',
            'last_name.min'         => 'The last name must be at least 2 characters.',
            'last_name.max'         => 'The last name may not be greater than 255 characters.',
            'school_id.required'    => 'Your school ID number is required.',
            'school_id.unique'      => 'This school ID is already registered to an account.',
            'school_id.max'         => 'Your school ID number must not exceed 9 characters.',
            'email.unique'          => 'The email address is already registered.',
            'email.required'        => 'The email address is required.',
            'email.email'           => 'The email address is invalid.',
            'selectedRole.in'       => 'The selected role is invalid.',
        ];
    }

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(User $user): void
    {
        $this->user = $user->load('roles:id,name');

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

        $this->email        = $user->email ?? '';
        $this->school_id    = $user->school_id ?? '';
        $this->selectedRole = $user->roles->pluck('name')->first();
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function update()
    {
        $validated = $this->validate();

        if ($this->isSelf && blank($this->selectedRole)) {
            $this->addError('selectedRole', 'You cannot remove your own role.');
            return;
        }

        // If the user asked to reset the password, we need a role to derive it from.
        if ($this->resetPassword && blank($this->selectedRole)) {
            $this->addError('selectedRole', 'Select a role first — the password is generated from it.');
            return;
        }

        $firstName  = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName   = $this->sanitize($validated['last_name']);
        $email      = $this->sanitize($validated['email']);
        $schoolId   = $this->sanitize($validated['school_id']);

        $newPassword = $this->resetPassword ? $this->generatedPassword : null;

        try {
            DB::transaction(function () use (
                $firstName,
                $middleName,
                $lastName,
                $email,
                $schoolId,
                $newPassword
            ) {
                $payload = [
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    'email'       => $email,
                    'school_id'   => $schoolId,
                ];

                if (filled($newPassword)) {
                    $payload['password'] = Hash::make($newPassword);
                }

                $this->user->update($payload);

                $currentRole = $this->user->roles->pluck('name')->first();

                if ($this->selectedRole !== $currentRole) {
                    $this->user->syncRoles($this->selectedRole ? [$this->selectedRole] : []);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id' : 'email';
                $this->addError($field, 'This value was just taken by another account. Please refresh and try again.');
                return;
            }

            throw $e;
        }

        $message = $this->resetPassword && $newPassword
            ? "User updated. New temporary password: {$newPassword}"
            : 'User updated successfully.';

        session()->flash('success', $message);

        if ($this->resetPassword && $newPassword) {
            session()->flash('generated_password', $newPassword);
        }

        return redirect()->route('super-admin.user.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data) ? trim(strip_tags($data)) : $data;
    }
};