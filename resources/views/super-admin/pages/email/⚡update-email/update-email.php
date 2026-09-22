<?php

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public int $emailId;
    public string $subject = '';
    public string $message = '';
    public bool $showPreview = false;

    /** Cached from mount so we don't hit the DB again on update. */
    protected ?EmailTemplate $templateModel = null;

    public function mount(int $email): void
    {
        $template = EmailTemplate::findOrFail($email);

        $data = is_array($template->template) ? $template->template : [];

        $this->templateModel = $template;
        $this->emailId       = (int) $template->id;
        $this->subject       = (string) ($data['subject'] ?? '');
        $this->message       = (string) ($data['message'] ?? '');
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function slugPreview(): string
    {
        return Str::slug($this->subject) ?: 'your-template-slug';
    }

    #[Computed]
    public function subjectLength(): int
    {
        return mb_strlen($this->subject);
    }

    #[Computed]
    public function messageLength(): int
    {
        return mb_strlen($this->message);
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
            report($e);

            return '<div style="padding:24px;font-family:system-ui,sans-serif;color:#b91c1c;">'
                . '<strong>Preview unavailable.</strong><br>'
                . e($e->getMessage())
                . '</div>';
        }
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'message' => ['required', 'string', 'min:3', 'max:20000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'subject.required' => 'The subject field is required.',
            'subject.min'      => 'The subject must be at least 3 characters.',
            'subject.max'      => 'The subject must not exceed 255 characters.',
            'message.required' => 'The message field is required.',
            'message.min'      => 'The message must be at least 3 characters.',
            'message.max'      => 'The message must not exceed 20,000 characters.',
        ];
    }

    // =========================================================
    //  PREVIEW
    // =========================================================

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function update()
    {
        abort_unless(auth()->user()?->can('manage-emails'), 403);

        $validated = $this->validate();

        $subject = $this->sanitizeSubject($validated['subject']);
        $message = $this->sanitizeMessage($validated['message']);
        $slug    = Str::slug($subject);

        // Duplicate slug check (JSON column).
        $duplicate = EmailTemplate::query()
            ->where('id', '!=', $this->emailId)
            ->whereJsonContains('template->slug', $slug)
            ->exists();

        if ($duplicate) {
            $this->addError('subject', 'A template with this subject already exists.');
            return;
        }

        try {
            DB::transaction(function () use ($slug, $subject, $message) {
                $template = $this->templateModel
                    ?? EmailTemplate::findOrFail($this->emailId);

                $template->update([
                    'template' => [
                        'slug'    => $slug,
                        'subject' => $subject,
                        'message' => $message,
                    ],
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $this->addError('subject', 'This template was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update email template. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not update email template. Please try again.');
            return;
        }

        Cache::forget('email-templates:count');

        session()->flash('success', 'Email template updated successfully.');

        return redirect()->route('super-admin.email.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function sanitizeSubject(string $subject): string
    {
        return trim(strip_tags($subject));
    }

    /**
     * Allow a safe whitelist of HTML tags in the email body.
     * Strips <script>, on* handlers, and javascript: URLs.
     */
    protected function sanitizeMessage(string $message): string
    {
        $allowed = '<p><br><br/><a><b><strong><i><em><u><s><ul><ol><li>'
            . '<h1><h2><h3><h4><h5><h6><img><table><thead><tbody><tr><td><th>'
            . '<div><span><hr><blockquote><pre><code>';

        $clean = strip_tags($message, $allowed);

        // Strip inline event handlers (onclick, onerror, etc.).
        $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);

        // Strip javascript: pseudo-URLs.
        $clean = preg_replace('/javascript\s*:/i', '', $clean);

        return trim($clean);
    }
};