<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';

    /**
     * Composed full name — matches what will be saved in `users.name`.
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

    protected function rules()
    {
        return [
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'school_id'   => 'required|string|max:255|unique:users,school_id',
        ];
    }

    public function messages()
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
        ];
    }

    public function saveAlumni()
    {
        $validated = $this->validate();

        $validated['first_name']  = $this->sanitizeData($validated['first_name']);
        $validated['middle_name'] = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $validated['last_name']   = $this->sanitizeData($validated['last_name']);
        $validated['email']       = $this->sanitizeData($validated['email']);
        $validated['school_id']   = $this->sanitizeData($validated['school_id']);

        $fullName = $this->fullName;

        try {
            DB::transaction(function () use ($validated) {
                $user = User::create([
                    'first_name'  => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name'   => $validated['last_name'],
                    // 'name' auto-fills via the User model's saving hook
                    'email'       => $validated['email'],
                    'school_id'   => $validated['school_id'],
                    'password'    => Hash::make('csav.alumni'),
                ]);

                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('alumni');
                }
            });

            // Send welcome email — outside the transaction so a mail failure
            // doesn't roll back the created account.
            try {
                EmailTemplateService::send(
                    'welcome-to-the-csav-alumni-network-name',
                    $validated['email'],
                    [
                        'name'          => $fullName,
                        'school_email'  => $validated['email'],
                        'school_id'     => $validated['school_id'],
                        'temp_password' => 'csav.alumni',
                        'login_url'     => route('login'),
                    ]
                );
            } catch (\Throwable $e) {
                logger()->warning('Alumni welcome email failed: ' . $e->getMessage(), [
                    'email' => $validated['email'],
                ]);
            }

            session()->flash('success', 'Alumni account created. A welcome email was sent to ' . $validated['email'] . '.');
            return redirect()->route('admin.alumni.view');

        } catch (\Throwable $e) {
            logger()->error('Alumni creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while creating this alumni: ' . $e->getMessage());
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