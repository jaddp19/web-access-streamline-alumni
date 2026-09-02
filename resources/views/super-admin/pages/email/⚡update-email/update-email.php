<?php

use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public int $emailId;
    public string $subject = '';
    public string $message = '';

    protected function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'The subject field is required.',
            'subject.string'   => 'The subject must be a valid string.',
            'subject.max'      => 'The subject must not exceed 255 characters.',
            'message.required' => 'The message field is required.',
            'message.string'   => 'The message must be a valid string.',
            'message.max'      => 'The message must not exceed 5000 characters.',
        ];
    }

    public function mount($email)
    {
        $template = EmailTemplate::findOrFail($email);
        $data = is_array($template->template) ? $template->template : [];

        $this->emailId = $template->id;
        $this->subject = $data['subject'] ?? '';
        $this->message = $data['message'] ?? '';
    }

    public function update()
    {
        $validated = $this->validate();

        // sanitize subject and message
        $subject = $this->sanitizeData($validated['subject']);
        $message = $this->sanitizeData($validated['message']);

        // auto-generate slug from subject
        $slug = Str::slug($subject);

        // enforce slug uniqueness against every OTHER template, since it
        // lives inside the `template` JSON column and can't use a normal
        // `unique` rule
        $duplicateExists = EmailTemplate::where('id', '!=', $this->emailId)
            ->whereJsonContains('template->slug', $slug)
            ->exists();

        if ($duplicateExists) {
            $this->addError('subject', 'A template with this subject already exists.');
            return;
        }

        $template = EmailTemplate::findOrFail($this->emailId);
        $template->update([
            'template' => [
                'slug'    => $slug,
                'subject' => $subject,
                'message' => $message,
            ],
        ]);

        session()->flash('success', 'Email template updated successfully.');
        return redirect()->route('super-admin.email.view');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};
