<?php

use App\Models\AlumniProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public string $gender = 'male';
    public int $phone_number_1 = 0;
    public ?int $phone_number_2 = null;
    public string $permanent_address = '';
    public string $current_address = '';
    public string $name = '';
    public string $email = '';

    protected function rules()
    {
        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'gender'            => 'required|in:male,female',
            'phone_number_1'    => 'required|numeric|digits:10',
            'phone_number_2'    => 'nullable|numeric|digits:10',
            'permanent_address' => 'required|string|max:255',
            'current_address'   => 'required|string|max:255',
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
            'phone_number_1.numeric' => 'The contact number 1 must contain only numbers.',
            'phone_number_2.numeric' => 'The contact number 2 must contain only numbers.',
            'phone_number_1.digits' => 'The contact number 1 must be a number with at most 10 digits',
            'phone_number_2.digits' => 'The contact number 2 must be a number with at most 10 digits',
            'permanent_address.required' => 'The permanent address field is required',
            'current_address.required' => 'The current address field is required',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $profile = AlumniProfile::where('user_id', $user->id)->first();

        $this->name  = $user->name;
        $this->email = $user->email;

        if ($profile) {
            $this->gender            = $profile->gender;
            $this->phone_number_1    = $profile->phone_number_1;
            $this->phone_number_2    = $profile->phone_number_2;
            $this->permanent_address = $profile->permanent_address;
            $this->current_address   = $profile->current_address;
        }
    }

    public function saveProfile()
    {
        $validated = $this->validate();

        // sanitize string fields
        $validated['name']              = $this->sanitizeData($validated['name']);
        $validated['email']             = $this->sanitizeData($validated['email']);
        $validated['gender']            = $this->sanitizeData($validated['gender']);
        $validated['permanent_address'] = $this->sanitizeData($validated['permanent_address']);
        $validated['current_address']   = $this->sanitizeData($validated['current_address']);

        // sanitize phone numbers: strip non-digits
        $validated['phone_number_1'] = $validated['phone_number_1']
            ? (int) preg_replace('/\D/', '', $validated['phone_number_1'])
            : null;

        $validated['phone_number_2'] = $validated['phone_number_2']
            ? (int) preg_replace('/\D/', '', $validated['phone_number_2'])
            : null;

        $user = Auth::user();
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'gender'            => $validated['gender'],
                'phone_number_1'    => $validated['phone_number_1'],
                'phone_number_2'    => $validated['phone_number_2'],
                'permanent_address' => $validated['permanent_address'],
                'current_address'   => $validated['current_address'],
            ]
        );

        session()->flash('success', 'Profile updated successfully.');
        return redirect()->route('alumni.profile');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

};
