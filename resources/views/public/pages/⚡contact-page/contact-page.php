<?php

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:120'],
            'email'   => ['required', 'email:rfc,dns', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'    => 'Please enter your name.',
            'name.min'         => 'Name must be at least 2 characters.',
            'email.required'   => 'Please enter your email address.',
            'email.email'      => 'Please enter a valid email address.',
            'message.required' => 'Please write your message.',
            'message.min'      => 'Message must be at least 10 characters.',
            'message.max'      => 'Message cannot exceed 5000 characters.',
        ];
    }

    // =========================================================
    //  SUBMIT
    // =========================================================

    public function submit(): void
    {
        $validated = $this->validate();

        // Sanitize before passing along.
        $name    = trim(strip_tags($validated['name']));
        $email   = trim(strip_tags($validated['email']));
        $message = trim(strip_tags($validated['message']));

        try {
            // ✅ Replace this with your actual handling:
            // Mail::to('csav@example.com')->send(new ContactMessage($name, $email, $message));
            // or store in DB, or dispatch a job.
            Log::info('Contact form submitted', [
                'name'    => $name,
                'email'   => $email,
                'message' => $message,
            ]);

            session()->flash('contact_success', "Thanks, {$name}! We'll get back to you at {$email} within 1–2 business days.");
        } catch (\Throwable $e) {
            report($e);
            $this->addError('message', 'Could not send your message. Please try again later.');
            return;
        }

        $this->reset('name', 'email', 'message');
    }
};