<?php

use App\Models\Batch;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public ?int $batch_id = null;

    protected function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'batch_id' => 'required|exists:batches,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'batch_id.required' => 'Please select a batch.',
            'batch_id.exists' => 'The selected batch is invalid.',
        ];
    }

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name')->get();
    }

    public function saveAlumni()
    {
        $validated = $this->validate();

        $validated['name']  = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                // Users column is required and not nullable, so we generate a
                // temporary password. The alumni sets their own via "Forgot Password"
                // on first login — the admin never chooses it for them.
                'password' => Hash::make(Str::random(32)),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('alumni');
            }

            // The profile row is created now (batch_id is a required FK), but every
            // detail the alumni themselves should fill in (avatar, contact, location,
            // course) is left empty for them to complete via their own update-profile page.
            UserProfile::create([
                'user_id'     => $user->id,
                'avatar'      => '',   // avatar column is required/non-nullable; alumni uploads their own later
                'location'    => [],   // alumni fills in gender/phone/location later
                'batch_id'    => $validated['batch_id'],
                'is_private'  => false,
                'is_verified' => false,
            ]);

            session()->flash('success', 'Alumni account created. They can log in and complete the rest of their profile themselves.');
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
