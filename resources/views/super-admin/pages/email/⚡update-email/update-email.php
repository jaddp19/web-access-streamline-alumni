<?php

use App\Models\Email;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public int $email;
    public string $subject = '';
    public string $message = '';

    protected function rules()
    {
        return [
            'subject' => 'required|string|max:255|unique:emails,subject,' . $this->email,
            'message' => 'required|string|max:5000',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'The subject field is required.',
            'subject.string'   => 'The subject must be a valid string.',
            'subject.max'      => 'The subject must not exceed 255 characters.',
            'subject.unique'   => 'This subject already exists.',
            'message.required' => 'The message field is required.',
            'message.string'   => 'The message must be a valid string.',
            'message.max'      => 'The message must not exceed 5000 characters.',
        ];
    }

    public function mount($email)
    {
        $email = Email::find($email);

        $this->email = $email->id;
        $this->subject = $email->subject;
        $this->message = $email->message;
    }

    public function update()
    {
        $validated = $this->validate();

        // sanitize subject and message
        $validated['subject'] = $this->sanitizeData($validated['subject']);
        $validated['message'] = $this->sanitizeData($validated['message']);

        // auto-generate slug from subject
        $slug = Str::slug($validated['subject']);

        $email = Email::find($this->email);
        $email->update([
            'slug'    => $slug,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
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
