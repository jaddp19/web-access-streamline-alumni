<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $school_id = '';

    protected function rules()
    {
        return [
            'name'      => 'required|string|min:3|max:255|unique:users,name',
            'email'     => 'required|email|max:255|unique:users,email',
            'school_id' => 'required|string|max:255|unique:users,school_id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => 'The name field is required.',
            'name.min'           => 'The name must be at least 3 characters.',
            'name.max'           => 'The name may not be greater than 255 characters.',
            'name.unique'        => 'This name is already registered.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'The email must be a valid email address.',
            'email.unique'       => 'This email is already registered.',
            'school_id.required' => 'The school ID field is required.',
            'school_id.unique'   => 'This school ID is already registered.',
        ];
    }

    public function saveAlumni()
    {
        $validated = $this->validate();

        $validated['name']      = $this->sanitizeData($validated['name']);
        $validated['email']     = $this->sanitizeData($validated['email']);
        $validated['school_id'] = $this->sanitizeData($validated['school_id']);

        try {
            DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'school_id' => $validated['school_id'],
                    'password'  => Hash::make('csav.alumni'),
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
                        'name'             => $validated['name'],
                        'school_email'     => $validated['email'],
                        'school_id'        => $validated['school_id'],
                        'temp_password' => 'csav.alumni',
                        'login_url'        => route('login'),
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