<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    #[Locked]
    public User $user;

    // ---- Read-only display fields ----
    public string $first_name = '';

    public string $middle_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $school_year = '';

    public string $school_id_suffix = '';

    // ---- Editable ----
    public bool $is_active = true;

    public bool $resetPassword = false;

    /** Default password for the alumni role (never taken from the client). */
    protected const ALUMNI_DEFAULT_PASSWORD = 'csav.alumni';

    // =========================================================
    //  MOUNT
    // =========================================================

    public function mount(User $user): void
    {
        Gate::authorize('update', $user);

        // This page only manages alumni accounts — never other roles.
        abort_unless($user->hasRole('alumni'), 403);

        $this->user = $user;

        // Hydrate name parts. If empty (legacy user), split from `name`.
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

        // Split the stored "YYYY-NNNN" back into year + suffix.
        if ($user->school_id && str_contains($user->school_id, '-')) {
            [$year, $suffix] = explode('-', $user->school_id, 2);
            $this->school_year = $year;
            $this->school_id_suffix = $suffix;
        }

        $this->is_active = (bool) $user->is_active;
    }

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
    public function generatedPassword(): string
    {
        return self::ALUMNI_DEFAULT_PASSWORD;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'is_active' => ['boolean'],
            'resetPassword' => ['boolean'],
        ];
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function updateAlumni()
    {
        // Re-check on every request: the model is locked, but permissions
        // and role may have changed since the page was loaded.
        Gate::authorize('update', $this->user);
        abort_unless($this->user->hasRole('alumni'), 403);

        $this->validate();

        $newPassword = $this->resetPassword ? $this->generatedPassword : null;

        try {
            $payload = ['is_active' => $this->is_active];

            if ($newPassword) {
                // The model's `hashed` cast hashes this automatically.
                $payload['password'] = $newPassword;
            }

            $this->user->update($payload);
        } catch (QueryException $e) {
            report($e);
            session()->flash('error', 'Could not update the alumni. Please try again.');

            return;
        }

        Log::info('Alumni account updated by program head', [
            'actor_id' => auth()->id(),
            'user_id' => $this->user->id,
            'is_active' => $this->is_active,
            'password_reset' => (bool) $newPassword,
        ]);

        if ($newPassword) {
            $this->sendPasswordResetEmail($newPassword);
        }

        session()->flash(
            'success',
            $newPassword
                ? "Alumni updated. New temporary password: {$newPassword}"
                : 'Alumni updated successfully.'
        );

        return redirect()->route('admin.alumni.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function sendPasswordResetEmail(string $plainPassword): void
    {
        try {
            EmailTemplateService::send('password-reset-by-program-head', $this->user->email, [
                'name' => $this->fullName,
                'temp_password' => $plainPassword,
                'login_url' => route('login'),
            ]);
        } catch (Throwable $e) {
            Log::error('Password-reset email failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);

            session()->flash(
                'warning',
                'Password was reset, but the notification email could not be sent. Please tell them manually.'
            );
        }
    }
};