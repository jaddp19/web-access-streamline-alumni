<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ---------- Generic outreach (unchanged) ----------
            [
                'slug' => 'please-update-your-alumni-profile',
                'subject' => 'Please update your alumni profile',
                'message' => "Hi {{name}}, it's been a while — please log in and update your profile.",
            ],
            [
                'slug' => 'we-would-love-your-feedback',
                'subject' => 'We would love your feedback',
                'message' => 'Hi {{name}}, please take a few minutes to complete our alumni tracer survey.',
            ],
            [
                'slug' => 'upcoming-alumni-event',
                'subject' => 'Upcoming alumni event',
                'message' => 'Hi {{name}}, we are hosting an event you might be interested in — details inside.',
            ],

            // ---------- Alumni welcome ----------
            [
                'slug' => 'welcome-to-the-csav-alumni-network-name',
                'subject' => 'Welcome to the CSAV Alumni Network, {{name}}!',
                'message' => "Hi {{name}},\n\n"
                    ."Your CSAV Alumni Network account has been created.\n\n"
                    ."Login details:\n"
                    ."• Email: {{school_email}}\n"
                    ."• Temporary password: {{temp_password}}\n\n"
                    ."Please log in at {{login_url}} and change your password immediately.\n\n"
                    ."Welcome to the community!\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Program Head welcome ----------
            [
                'slug' => 'welcome-program-head',
                'subject' => 'Your CSAV Program Head account is ready, {{name}}',
                'message' => "Hi {{name}},\n\n"
                    ."A Program Head account has been created for you on the CSAV Alumni Network.\n\n"
                    ."Login details:\n"
                    ."• Email: {{school_email}}\n"
                    ."• Temporary password: {{temp_password}}\n\n"
                    .'As a Program Head, you have access to alumni records for your assigned department — '
                    ."including employment trends, tracer submissions, and engagement analytics.\n\n"
                    ."Please log in at {{login_url}} and change your password immediately.\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Registrar welcome ----------
            [
                'slug' => 'welcome-registrar',
                'subject' => 'Your CSAV Registrar account is ready, {{name}}',
                'message' => "Hi {{name}},\n\n"
                    ."A Registrar account has been created for you on the CSAV Alumni Network.\n\n"
                    ."Login details:\n"
                    ."• Email: {{school_email}}\n"
                    ."• Temporary password: {{temp_password}}\n\n"
                    .'As a Registrar, you have institution-wide access to all alumni records, '
                    ."tracer study submissions, and reporting tools.\n\n"
                    ."Please log in at {{login_url}} and change your password immediately.\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Fallback / staff (kept for legacy) ----------
            [
                'slug' => 'your-csav-alumni-network-staff-account-has-been-created',
                'subject' => 'Your CSAV Alumni Network staff account has been created',
                'message' => "Hi {{name}},\n\n"
                    ."An account has been created for you on the CSAV Alumni Network using this login email: {{school_email}}.\n\n"
                    .'Please contact the registrar office to receive your login password, '
                    ."then log in here: {{login_url}}.\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],
            // ---------- Profile verification ----------
            [
                'slug' => 'your-alumni-profile-has-been-verified',
                'subject' => 'Your alumni profile has been verified, {{name}}!',
                'message' => "Hi {{name}},\n\n"
                    ."Great news — your alumni profile has been verified.\n\n"
                    ."You now have full access to the alumni network:\n"
                    ."• Announcements and events\n"
                    ."• Tracer study submissions\n"
                    ."• Job board and networking\n\n"
                    ."Log in here: {{login_url}}\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],
            [
                'slug' => 'your-alumni-verification-status-was-updated',
                'subject' => 'Your alumni verification status was updated',
                'message' => "Hi {{name}},\n\n"
                    .'Your alumni verification status has changed to unverified. '
                    ."This usually means some details need to be reviewed.\n\n"
                    ."Please log in and check your profile, or contact the alumni office.\n\n"
                    ."Log in here: {{login_url}}\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Profile updated ----------
            [
                'slug' => 'your-alumni-profile-was-updated',
                'subject' => 'Your alumni profile was updated',
                'message' => "Hi {{name}},\n\n"
                    ."Your alumni profile was updated by an administrator.\n\n"
                    ."Fields changed: {{changes}}\n\n"
                    ."If you did not expect this change, please contact the alumni office immediately.\n\n"
                    ."Log in to review: {{login_url}}\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Institutional announcement (post publish) ----------
            [
                'slug' => 'institutional-announcement',
                'subject' => 'New announcement: {{post_title}}',
                'message' => "Hi {{name}},\n\n"
                    ."A new announcement has been posted in the alumni portal:\n\n"
                    ."{{post_title}}\n"
                    ."Category: {{category}}\n\n"
                    ."{{post_excerpt}}\n\n"
                    ."Read the full post: {{post_url}}\n\n"
                    ."Or log in here: {{login_url}}\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],
            // ---------- Event invitation ----------
            [
                'slug' => 'youre-invited-event-invitation',
                'subject' => "You're invited: {{event_title}}",
                'message' => "Hi {{name}},\n\n"
                    ."You're invited to an upcoming alumni event:\n\n"
                    ."{{event_title}}\n"
                    ."When: {{event_date}}\n"
                    ."Where: {{event_location}}\n\n"
                    ."{{event_excerpt}}\n\n"
                    ."RSVP now: {{rsvp_url}}\n\n"
                    ."We hope to see you there!\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Event reminder (1 day before) ----------
            [
                'slug' => 'reminder-event-is-tomorrow',
                'subject' => 'Reminder: {{event_title}} is tomorrow',
                'message' => "Hi {{name}},\n\n"
                    ."Just a friendly reminder that the event you RSVP'd for is happening tomorrow.\n\n"
                    ."{{event_title}}\n"
                    ."When: {{event_date}}\n"
                    ."Where: {{event_location}}\n\n"
                    ."See you there!\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],

            // ---------- Event cancelled ----------
            [
                'slug' => 'event-has-been-cancelled',
                'subject' => 'Event cancelled: {{event_title}}',
                'message' => "Hi {{name}},\n\n"
                    ."Unfortunately, the following event has been cancelled:\n\n"
                    ."{{event_title}}\n"
                    ."Originally scheduled for: {{event_date}}\n\n"
                    ."We apologize for any inconvenience. Any future events will be announced through the alumni portal.\n\n"
                    .'— Colegio de Sta. Ana de Victorias',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['template->slug' => $template['slug']],
                ['template' => [
                    'slug' => $template['slug'],
                    'subject' => $template['subject'],
                    'message' => $template['message'],
                ]]
            );
        }
    }
}
