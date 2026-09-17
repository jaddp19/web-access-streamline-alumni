<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmailSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'subject' => 'Please update your alumni profile',
                'message' => "Hi {{name}}, it's been a while — please log in and update your profile.",
            ],
            [
                'subject' => 'We would love your feedback',
                'message' => 'Hi {{name}}, please take a few minutes to complete our alumni tracer survey.',
            ],
            [
                'subject' => 'Upcoming alumni event',
                'message' => 'Hi {{name}}, we are hosting an event you might be interested in — details inside.',
            ],
            [
            'subject' => 'Welcome to the CSAV Alumni Network, {{name}}!',
            'message' => "Hi {{name}},\n\n"
                . "Your CSAV Alumni Network account has been created by the {{department}} department.\n\n"
                . "Login details:\n"
                . "• Email: {{school_email}}\n"
                . "• Default password: csav.alumni\n\n"
                . "You are enrolled as a {{course}} graduate of Batch {{batch}}.\n\n"
                . "Please log in at {{login_url}} and change your password immediately.\n\n"
                . "Welcome to the community!\n\n"
                . "— Colegio de Sta. Ana de Victorias",
            ],
            [
                'subject' => 'Your CSAV Alumni Network staff account has been created',
                'message' => 'Hi {{name}}, an account has been created for you on the CSAV Alumni Network using this login email: {{school_email}}. Please contact the registrar office to receive your login password, then log in here: {{login_url}}.',
            ],
        ];

        foreach ($templates as $template) {
            $slug = Str::slug($template['subject']);

            EmailTemplate::updateOrCreate(
                ['template->slug' => $slug],
                ['template' => [
                    'slug'    => $slug,
                    'subject' => $template['subject'],
                    'message' => $template['message'],
                ]]
            );
        }
    }
}
