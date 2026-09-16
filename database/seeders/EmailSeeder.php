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
                'subject' => 'Your CSAV Alumni account has been created',
                'message' => 'Hi {{name}}, an account has been created for you on the CSAV Alumni Network. Please log in here and complete your profile form: {{login_url}}. If this is your first time logging in, use "Forgot Password" to set your password.',
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
