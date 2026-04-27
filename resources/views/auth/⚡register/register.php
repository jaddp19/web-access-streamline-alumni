<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $selectedRole = '';


    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:users,name',
            'email' => [
            'required',
            'email',
            'unique:users,email,' . Auth::id(),
            function ($attribute, $value, $fail) 
            {
                // basic format check
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('The email address is invalid.');
                    return;
                }

                // extract domain
                $domain = substr(strrchr($value, "@"), 1);

                // local MX record check
                if (!checkdnsrr($domain, "MX")) {
                    $fail('The email domain is not valid.');
                    return;
                }

                // $response = Http::get('https://emailreputation.abstractapi.com/v1', [
                //     'api_key' => env('ABSTRACT_API_KEY'),
                //     'email'   => $value,
                // ]);

                // if (!$response->ok()) {
                //     $fail('Unable to validate the email address right now.');
                //     return;
                // }

                // $data = $response->json();

                // // check deliverability using the correct field
                // if (!isset($data['email_deliverability']['status']) 
                //     || $data['email_deliverability']['status'] !== 'deliverable') {
                //     $fail('The email address is not deliverable.');
                // }
            },
        ],
            'password' => 'required|string|min:6|confirmed',
            'selectedRole' => 'exists:roles,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'The name is already taken.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'Confirmation password does not match the password.',
            'email.unique' => 'The email address is already registered.',
            'email.required' => 'The email address is required.',
        ];
    }


    public function register()
    {
        $validated = $this->validate();

        $validated['name'] = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('pop-up');
        
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};
