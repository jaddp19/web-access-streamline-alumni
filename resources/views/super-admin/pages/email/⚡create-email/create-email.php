<?php

use App\Models\EmailTemplate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public string $subject = '';
    public string $message = '';
    public bool $showPreview = false;

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

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    #[Computed]
    public function previewHtml(): string
    {
        try {
            return view('emails.template', [
                'emailSubject' => $this->subject !== '' ? $this->subject : 'Your subject here',
                'bodyHtml'     => $this->message !== '' ? $this->message : 'Your message will appear here.',
            ])->render();
        } catch (\Throwable $e) {
            return '<div style="padding:24px;font-family:system-ui,sans-serif;color:#b91c1c;">'
                . '<strong>Preview unavailable.</strong><br>'
                . e($e->getMessage())
                . '</div>';
        }
    }

    public function create()
    {
        $validated = $this->validate();

        $subject = $this->sanitizeData($validated['subject']);
        $message = $this->sanitizeData($validated['message']);

        $slug = Str::slug($subject);

        if (EmailTemplate::whereJsonContains('template->slug', $slug)->exists()) {
            $this->addError('subject', 'A template with this subject already exists.');
            return;
        }

        EmailTemplate::create([
            'template' => [
                'slug'    => $slug,
                'subject' => $subject,
                'message' => $message,
            ],
        ]);

        session()->flash('success', 'Email template created successfully.');
        return redirect()->route('super-admin.email.view');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};