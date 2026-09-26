<?php

use App\Models\Batch;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
    public string $school_year = '';
    public string $school_id_suffix = '';
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

    #[Computed]
    public function generatedPassword(): ?string
    {
        if (blank($this->selectedRole)) {
            return null;
        }

        return 'csav.'.Str::of($this->selectedRole)
            ->lower()
            ->replace(' ', '-')
            ->toString();
    }

    #[Computed]
    public function schoolId(): string
    {
        if (blank($this->school_year) || blank($this->school_id_suffix)) {
            return '';
        }

        return $this->school_year.'-'.$this->school_id_suffix;
    }

    #[Computed]
    public function roles(): array
    {
        $cacheKey = 'roles:editable-list:v2';
        $cached = Cache::get($cacheKey);

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

    #[Computed(persist: true)]
    public function batchYears()
    {
        return Cache::remember('batches:years-list', now()->addMinutes(10), function () {
            return Batch::query()
                ->orderByDesc('batch_name')
                ->pluck('batch_name')
                ->map(fn ($y) => (string) $y)
                ->all();
        });
    }

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
            'first_name' => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|min:2|max:255',

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],

            'school_year' => [
                'required',
                Rule::in($this->batchYears),
                function ($attribute, $value, $fail) {
                    if (strlen($this->school_id_suffix) !== 4) {
                        return;
                    }
                    $combined = $value.'-'.$this->school_id_suffix;
                    if (User::where('school_id', $combined)
                        ->where('id', '!=', $this->user->id)
                        ->exists()) {
                        $fail('This school ID is already registered to an account.');
                    }
                },
            ],

            'school_id_suffix' => [
                'required',
                'string',
                'digits:4',
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
            'first_name.required' => 'The first name is required.',
            'first_name.min' => 'The first name must be at least 2 characters.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.min' => 'The last name must be at least 2 characters.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'school_year.required' => 'Please select a batch year.',
            'school_year.in' => 'The selected batch year is invalid.',
            'school_id_suffix.required' => 'Please enter the last 4 digits of the school ID.',
            'school_id_suffix.digits' => 'The last 4 digits must be numeric.',
            'email.unique' => 'The email address is already registered.',
            'email.required' => 'The email address is required.',
            'email.email' => 'The email address is invalid.',
            'selectedRole.in' => 'The selected role is invalid.',
        ];
    }

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(User $user): void
    {
        $this->user = $user->load('roles:id,name');

        $first = $user->first_name;
        $middle = $user->middle_name;
        $last = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split = preg_split('/\s+/', trim($user->name));
            $first = $split[0] ?? '';
            $last = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2
                ? implode(' ', array_slice($split, 1, -1))
                : '';
        }

        $this->first_name = $first ?? '';
        $this->middle_name = $middle ?? '';
        $this->last_name = $last ?? '';

        $this->email = $user->email ?? '';

        $this->school_year = '';
        $this->school_id_suffix = '';

        if ($user->school_id && str_contains($user->school_id, '-')) {
            [$year, $suffix] = explode('-', $user->school_id, 2);
            $this->school_year = $year;
            $this->school_id_suffix = $suffix;
        }

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

        if ($this->resetPassword && blank($this->selectedRole)) {
            $this->addError('selectedRole', 'Select a role first — the password is generated from it.');

            return;
        }

        $firstName = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName = $this->sanitize($validated['last_name']);
        $email = $this->sanitize($validated['email']);
        $schoolId = $this->schoolId;

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
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'school_id' => $schoolId,
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
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id_suffix' : 'email';
                $this->addError($field, 'This value was just taken by another account. Please refresh and try again.');

                return;
            }

            throw $e;
        }

        // ← THE MISSING PIECE: send the reset email when the flag was on.
        if ($this->resetPassword && $newPassword) {
            $fullName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));
            $this->sendPasswordResetEmail($this->user, $fullName, $email, $newPassword);
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

    protected function sendPasswordResetEmail(
        User $user,
        string $fullName,
        string $email,
        string $plainPassword
    ): void {
        try {
            EmailTemplateService::send('password-reset-by-registrar', $email, [
                'name' => $fullName,
                'temp_password' => $plainPassword,
                'login_url' => route('login'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Password-reset email failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            session()->flash(
                'warning',
                'Password was reset, but the notification email could not be sent. Please tell them manually.'
            );
        }
    }

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data) ? trim(strip_tags($data)) : $data;
    }
};