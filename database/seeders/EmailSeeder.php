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
                'message' => "Hi {{name}}, it's been a while, please log in and update your profile."
                            ."\nDisregard this message if you already updated your profile.",
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

            [
                'slug' => 'alumni-verification-rejected',
                'subject' => 'Update on your CSAV alumni verification, {{name}}',
                'message' => "Hi {{name}},\n\n"
                    ."Thank you for submitting your alumni profile for verification.\n\n"
                    ."After reviewing your board exam details, we were unable to verify your account at this time.\n\n"
                    ."Reason:\n"
                    ."{{reason}}\n\n"
                    ."You may log back in, update your board exam details, and resubmit for verification:\n"
                    ."{{login_url}}\n\n"
                    ."If you believe this decision was made in error, please contact the CSAV Alumni Office.\n\n"
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
                'subject' => 'You\'re a board passer, {{name}} — your alumni profile is verified!',
                'message' => "Hi {{name}},\n\n"
                    ."After reviewing your board exam details, we've confirmed that you're a board passer — "
                    ."your alumni profile has been verified!\n\n"
                    ."Log in here: {{login_url}}\n\n"
                    ."Congratulations, and welcome to the alumni community!\n\n"
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

            // ---------- Password reset ----------
            [
                'slug' => 'password-reset',
                'subject' => 'Reset your CSAV Alumni password',
                'message' => "Hi {{name}},\n\n"
                    ."We received a request to reset your CSAV Alumni Network password.\n\n"
                    ."To choose a new password, open this link:\n"
                    ."{{reset_url}}\n\n"
                    ."This link expires in {{expires_in}} minutes. If you didn't request a password reset, you can safely ignore this email — your password won't change.\n\n"
                    ."If you need help, contact the CSAV Alumni Office.\n\n"
                    .'— CSAV Alumni Office',
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

            // ---------- Tracer study submitted (first time) ----------
            [
                'slug' => 'tracer-study-submitted',
                'subject' => 'Your CSAV Alumni Tracer Study Has Been Received',
                'message' => "Hi {{name}},\n\n"
                    ."We've successfully received your Alumni Tracer Study response for {{year}}.\n\n"
                    ."Your answers help Colegio de Sta. Ana de Victorias track alumni outcomes, "
                    ."improve our programs, and support institutional planning.\n\n"
                    ."Submitted on: {{submitted_at}}\n\n"
                    ."If you need to update your answers, you can do so in Settings → Update Form anytime.\n\n"
                    ."Warm regards,\n"
                    .'— CSAV Alumni Office',
            ],

            // ---------- Tracer study updated (yearly update) ----------
            [
                'slug' => 'tracer-study-updated',
                'subject' => 'Your CSAV Alumni Tracer Study Has Been Updated',
                'message' => "Hi {{name}},\n\n"
                    ."Your Alumni Tracer Study has been successfully updated for {{year}}.\n\n"
                    ."Updated on: {{updated_at}}\n\n"
                    ."If you did not make this change, please contact the CSAV Alumni Office immediately.\n\n"
                    ."Warm regards,\n"
                    .'— CSAV Alumni Office',
            ],

            // ---------- Alumni profile updated (self-service) ----------
            [
                'slug' => 'alumni-profile-self-updated',
                'subject' => 'Your CSAV Alumni Profile Has Been Updated',
                'message' => "Hi {{name}},\n\n"
                    ."Your CSAV Alumni profile has been updated successfully.\n\n"
                    ."When: {{updated_at}}\n\n"
                    ."If you made this change — great! No action needed.\n\n"
                    ."If you did not make this change, please contact the CSAV Alumni Office immediately "
                    ."so we can secure your account.\n\n"
                    ."Warm regards,\n"
                    .'— CSAV Alumni Office',
            ],

            [
                'slug' => 'password-reset-by-registrar',
                'subject' => 'Your CSAV Alumni password has been reset',
                'message' => "Hi {{name}},\n\n"
                    ."Your CSAV Alumni Network password has been reset by the registrar.\n\n"
                    ."Your new temporary password: "
                    ."{{temp_password}}\n\n"
                    ."Please log in at {{login_url}} and change it immediately.\n\n"
                    ."If you did not request this change, contact the CSAV Alumni Office right away.\n\n"
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
