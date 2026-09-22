<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    private const DEFAULT_PASSWORD = 'csav.alumni';

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';

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

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'min:2', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'min:2', 'max:255'],
            'email'       => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'school_id'   => ['required', 'string', 'max:20', 'unique:users,school_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name is required.',
            'first_name.min'      => 'The first name must be at least 2 characters.',
            'first_name.max'      => 'The first name may not be greater than 255 characters.',
            'last_name.required'  => 'The last name is required.',
            'last_name.min'       => 'The last name must be at least 2 characters.',
            'last_name.max'       => 'The last name may not be greater than 255 characters.',
            'email.required'      => 'The email field is required.',
            'email.email'         => 'The email must be a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'school_id.required'  => 'The school ID field is required.',
            'school_id.unique'    => 'This school ID is already registered.',
            'school_id.max'       => 'The school ID may not be greater than 20 characters.',
        ];
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function saveAlumni()
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar', 'program head']), 403);

        $validated = $this->validate();

        $firstName  = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName   = $this->sanitize($validated['last_name']);
        $email      = $this->sanitize($validated['email']);
        $schoolId   = $this->sanitize($validated['school_id']);
        $fullName   = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

        try {
            DB::transaction(function () use ($firstName, $middleName, $lastName, $email, $schoolId) {
                $user = User::create([
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    'email'       => $email,
                    'school_id'   => $schoolId,
                    'password'    => Hash::make(self::DEFAULT_PASSWORD),
                ]);

                $user->syncRoles(['alumni']);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id' : 'email';
                $this->addError($field, 'This value was just registered. Please refresh and try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create the alumni account. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not create the alumni account. Please try again.');
            return;
        }

        // Send welcome email — after DB commit so mail failure won't roll back.
        try {
            EmailTemplateService::send(
                'welcome-to-the-csav-alumni-network-name',
                $email,
                [
                    'name'          => $fullName,
                    'school_email'  => $email,
                    'school_id'     => $schoolId,
                    'temp_password' => self::DEFAULT_PASSWORD,
                    'login_url'     => route('login'),
                ]
            );

            session()->flash(
                'success',
                "Alumni account created. A welcome email was sent to {$email}."
            );
        } catch (\Throwable $e) {
            Log::warning('Alumni welcome email failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            session()->flash(
                'warning',
                "Alumni account created, but the welcome email could not be sent. Notify {$email} manually with the temp password: " . self::DEFAULT_PASSWORD
            );
        }

        return redirect()->route('admin.alumni.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data) ? trim(strip_tags($data)) : $data;
    }
};