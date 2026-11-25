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

    public bool $is_active = true;

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
            'resetPassword' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(User $user): void
    {
        Gate::authorize('can_update');
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
        $this->is_active = (bool) $user->is_active;
    }

    // =========================================================
    //  UPDATE
    // =========================================================

        public function update()
    {
        Gate::authorize('can_update');
        $this->validate();

        if ($this->isSelf && blank($this->selectedRole)) {
            $this->addError('selectedRole', 'You cannot remove your own role.');
            return;
        }

        if ($this->resetPassword && blank($this->selectedRole)) {
            $this->addError('selectedRole', 'Select a role first — the password is generated from it.');
            return;
        }

        $newPassword = $this->resetPassword ? $this->generatedPassword : null;

        try {
            DB::transaction(function () use ($newPassword) {
                // Only update is_active and password (if requested)
                $payload = [
                    'is_active' => $this->is_active,
                ];

                if (filled($newPassword)) {
                    $payload['password'] = Hash::make($newPassword);
                }

                $this->user->update($payload);
            });
        } catch (QueryException $e) {
            report($e);
            session()->flash('error', 'Could not update the user. Please try again.');
            return;
        }

        // Send the reset email when the flag was on.
        if ($this->resetPassword && $newPassword) {
            $fullName = trim(implode(' ', array_filter([
                $this->user->first_name,
                $this->user->middle_name,
                $this->user->last_name,
            ])));
            
            $this->sendPasswordResetEmail($this->user, $fullName, $this->user->email, $newPassword);
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
            EmailTemplateService::send('password-reset-by-alumni-coordinator', $email, [
                'name' => $fullName,
                'temp_password' => $plainPassword,
                'login_url' => route('login'),
            ]);
        } catch (Throwable $e) {
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
