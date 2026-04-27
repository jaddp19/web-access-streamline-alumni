<?php

namespace App\Mail;

use App\Models\Email;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $user;

    public function __construct(Email $template, User $user)
    {
        $this->template = $template;
        $this->user = $user;
    }

    public function build()
    {
        // Replace placeholders like {{name}} with actual values
        $messageBody = str_replace('{{name}}', $this->user->name, $this->template->message);

        return $this->subject($this->template->subject)
                    ->view('emails.dynamic')
                    ->with([
                        'messageBody' => $messageBody,
                        'user' => $this->user,
                    ]);
    }
}
