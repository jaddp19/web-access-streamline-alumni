<?php

use App\Models\AlumniProfile;
use App\Models\Batch;
use App\Models\DegreeProgram;
use App\Models\EducationalBackground;
use App\Models\User;
use App\Services\AlumniVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.auth')] class extends Component
{
    use WithFileUploads;

    // Account fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $proof_document; // uploaded ID / diploma / yearbook photo

    // Alumni profile fields
    public string $gender = '';
    public string $phone_number_1 = '';
    public string $phone_number_2 = '';
    public string $permanent_address = '';
    public string $current_address = '';

    // Educational background fields
    public string $degree_program_id = '';
    public string $batch_id = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
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
                },
            ],
            'password' => 'required|string|min:6|confirmed',
            'proof_document' => 'required|image|max:5120', // 5MB max

            'gender' => 'required|in:male,female',
            'phone_number_1' => 'required|numeric|digits:10',
            'phone_number_2' => 'nullable|numeric|digits:10',
            'permanent_address' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',

            'degree_program_id' => 'required|exists:degree_programs,id',
            'batch_id' => 'required|exists:batches,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'Confirmation password does not match the password.',
            'email.unique' => 'The email address is already registered.',
            'email.required' => 'The email address is required.',
            'proof_document.required' => 'Please upload a photo of your ID, diploma, or yearbook page as proof of alumni status.',
            'proof_document.image' => 'The file must be an image (JPG, PNG).',
            'proof_document.max' => 'The image must not be larger than 5MB.',
            'gender.required' => 'Please select your gender.',
            'phone_number_1.required' => 'Primary phone number is required.',
            'phone_number_1.numeric' => 'Phone number must contain numbers only.',
            'phone_number_1.digits' => 'The contact number must be exactly 10 digits.',
            'phone_number_2.digits' => 'The contact number must be exactly 10 digits.',
            'permanent_address.required' => 'Permanent address is required.',
            'current_address.required' => 'Current address is required.',
            'degree_program_id.required' => 'Please select your degree program.',
            'degree_program_id.exists' => 'The selected degree program is invalid.',
            'batch_id.required' => 'Please select your graduation batch/year.',
            'batch_id.exists' => 'The selected batch is invalid.',
        ];
    }

    public function register(AlumniVerificationService $verifier)
    {
        $validated = $this->validate();

        $validated['name'] = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        // Store the uploaded proof document privately
        $path = $this->proof_document->store('alumni-verification', 'private');

        $degreeProgram = DegreeProgram::find($this->degree_program_id);
        $batch = Batch::find($this->batch_id);

        // Create the user + alumni profile + educational background together
        $user = DB::transaction(function () use ($validated, $path, $degreeProgram, $batch) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'verification_status' => 'pending',
                'verification_document' => $path,
            ]);

            $profile = AlumniProfile::create([
                'user_id' => $user->id,
                'gender' => $this->gender,
                'phone_number_1' => $this->phone_number_1,
                'phone_number_2' => $this->phone_number_2 ?: null,
                'permanent_address' => $this->permanent_address,
                'current_address' => $this->current_address,
            ]);

            EducationalBackground::create([
                'alumni_profile_id' => $profile->id,
                'degree_program_id' => $degreeProgram->id,
                'batch_id' => $batch->id,
            ]);

            return $user;
        });

        // Run AI extraction against the uploaded document.
        // AI only assists the registrar's review — it never auto-verifies
        // or auto-assigns a role on its own. A human (registrar / super-admin)
        // always makes the final call.
        $absolutePath = Storage::disk('private')->path($path);
        $result = $verifier->verify(
            $absolutePath,
            $validated['name'],
            $degreeProgram->program_name,
            (string) $batch->batch_year
        );

        $user->update([
            'ai_extracted_data' => $result['extracted'],
            'ai_confidence' => $result['confidence'],
            'verification_status' => 'pending',
        ]);

        $user->assignRole('pending-verification');

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('verification.pending')
            ->with('status', 'Thanks for registering! Your documents are under review — we\'ll notify you once approved.');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    public function with(): array
    {
        return [
            'degreePrograms' => DegreeProgram::orderBy('program_name')->get(),
            'batches' => Batch::orderBy('batch_year', 'desc')->get(),
        ];
    }
};
