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
            // ---------- Generic outreach (unchanged) ----------
            [
                'slug'    => 'please-update-your-alumni-profile',
                'subject' => 'Please update your alumni profile',
                'message' => "Hi {{name}}, it's been a while — please log in and update your profile.",
            ],
            [
                'slug'    => 'we-would-love-your-feedback',
                'subject' => 'We would love your feedback',
                'message' => 'Hi {{name}}, please take a few minutes to complete our alumni tracer survey.',
            ],
            [
                'slug'    => 'upcoming-alumni-event',
                'subject' => 'Upcoming alumni event',
                'message' => 'Hi {{name}}, we are hosting an event you might be interested in — details inside.',
            ],

            // ---------- Alumni welcome ----------
            [
                'slug'    => 'welcome-to-the-csav-alumni-network-name',
                'subject' => 'Welcome to the CSAV Alumni Network, {{name}}!',
                'message' =>
                    "Hi {{name}},\n\n"
                    . "Your CSAV Alumni Network account has been created.\n\n"
                    . "Login details:\n"
                    . "• Email: {{school_email}}\n"
                    . "• Temporary password: {{temp_password}}\n\n"
                    . "Please log in at {{login_url}} and change your password immediately.\n\n"
                    . "Welcome to the community!\n\n"
                    . "— Colegio de Sta. Ana de Victorias",
            ],

            // ---------- Program Head welcome ----------
            [
                'slug'    => 'welcome-program-head',
                'subject' => 'Your CSAV Program Head account is ready, {{name}}',
                'message' =>
                    "Hi {{name}},\n\n"
                    . "A Program Head account has been created for you on the CSAV Alumni Network.\n\n"
                    . "Login details:\n"
                    . "• Email: {{school_email}}\n"
                    . "• Temporary password: {{temp_password}}\n\n"
                    . "As a Program Head, you have access to alumni records for your assigned department — "
                    . "including employment trends, tracer submissions, and engagement analytics.\n\n"
                    . "Please log in at {{login_url}} and change your password immediately.\n\n"
                    . "— Colegio de Sta. Ana de Victorias",
            ],

            // ---------- Registrar welcome ----------
            [
                'slug'    => 'welcome-registrar',
                'subject' => 'Your CSAV Registrar account is ready, {{name}}',
                'message' =>
                    "Hi {{name}},\n\n"
                    . "A Registrar account has been created for you on the CSAV Alumni Network.\n\n"
                    . "Login details:\n"
                    . "• Email: {{school_email}}\n"
                    . "• Temporary password: {{temp_password}}\n\n"
                    . "As a Registrar, you have institution-wide access to all alumni records, "
                    . "tracer study submissions, and reporting tools.\n\n"
                    . "Please log in at {{login_url}} and change your password immediately.\n\n"
                    . "— Colegio de Sta. Ana de Victorias",
            ],

            // ---------- Fallback / staff (kept for legacy) ----------
            [
                'slug'    => 'your-csav-alumni-network-staff-account-has-been-created',
                'subject' => 'Your CSAV Alumni Network staff account has been created',
                'message' =>
                    "Hi {{name}},\n\n"
                    . "An account has been created for you on the CSAV Alumni Network using this login email: {{school_email}}.\n\n"
                    . "Please contact the registrar office to receive your login password, "
                    . "then log in here: {{login_url}}.\n\n"
                    . "— Colegio de Sta. Ana de Victorias",
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['template->slug' => $template['slug']],
                ['template' => [
                    'slug'    => $template['slug'],
                    'subject' => $template['subject'],
                    'message' => $template['message'],
                ]]
            );
        }
    }
}