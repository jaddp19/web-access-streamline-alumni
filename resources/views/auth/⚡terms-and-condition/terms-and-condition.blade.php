<div class="max-w-[900px] mx-auto px-4 py-12 select-none">
    {{-- Header --}}
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#0a1a10] rounded-3xl p-8 mb-8">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Legal</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Terms and Conditions
                </h1>
                <p class="text-white/40 text-xs mt-1">Effective Date: {{ now()->format('F d, Y') }} · Version 1.0</p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div
        class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 rounded-3xl p-8 space-y-8 text-black dark:text-white">

        {{-- 1. Acceptance of Terms --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">1. Acceptance of Terms</h2>
            <p class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                By accessing or using the <strong>Web-Access Streamline Alumni Information Tracking System with
                    Comparative Descriptive Analytics</strong> ("the System") of Colegio de Sta. Ana de Victorias, Inc.
                ("the Institution", "we", "us"), you ("the User", "Alumni") agree to be bound by these Terms and
                Conditions.
            </p>
            <p class="text-sm leading-relaxed text-black/80 dark:text-white/80 mt-3">
                If you do not agree to these Terms, you must discontinue use of the System immediately. Continued use of
                the System constitutes ongoing acceptance of these Terms.
            </p>
        </section>

        {{-- 2. Eligibility and Account Provisioning --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">2. Eligibility and Account Provisioning</h2>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li>The System is available only to verified alumni of the Institution. Accounts are provisioned by
                    authorized personnel (Super Admin, Registrar, or Program Head).</li>
                <li>Upon account creation, you will receive an email containing your login credentials and a default
                    password. <strong>You are required to change your password immediately after first login.</strong>
                </li>
                <li>You are responsible for maintaining the confidentiality of your account credentials. Do not share
                    your password with anyone.</li>
                <li>You agree to notify the Institution immediately at <strong>dpo@csav.edu.ph</strong> if you suspect
                    unauthorized access to your account.</li>
                <li>The Institution reserves the right to suspend or terminate accounts that violate these Terms.</li>
            </ul>
        </section>

        {{-- 3. Acceptable Use --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">3. Acceptable Use</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">The System includes features that allow you to
                publish content (posts, updates, comments) visible to other alumni. When using these features, you agree
                <strong>NOT</strong> to:</p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li>Post illegal, defamatory, libelous, obscene, or harassing content</li>
                <li>Share another person's private information without their consent (doxxing)</li>
                <li>Impersonate any individual, institution, or entity</li>
                <li>Distribute spam, malware, phishing links, or unsolicited commercial content</li>
                <li>Post content that infringes on any third party's intellectual property rights</li>
                <li>Use the System for any commercial purpose not authorized by the Institution</li>
                <li>Attempt to access data belonging to other alumni beyond what is publicly shared</li>
                <li>Manipulate, scrape, or automate data extraction from the System</li>
            </ul>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">
                The Institution reserves the right to remove any content that violates these rules and to suspend
                accounts that repeatedly violate them.
            </p>
        </section>

        {{-- 4. Automated Email Communications --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">4. Automated Email Communications</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">
                The System sends automated emails for the following purposes:
            </p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li>Account notifications (welcome, password reset, verification)</li>
                <li>Requests to update your alumni profile</li>
                <li>Alumni tracer study surveys</li>
                <li>Event invitations and institutional announcements</li>
            </ul>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">
                By using the System, you consent to receive these communications at the email address registered to your
                account. <strong>Transactional emails</strong> (account security, password resets) cannot be opted out
                of. <strong>Informational and survey emails</strong> may be unsubscribed from at any time by contacting
                the Registrar's Office or the DPO.
            </p>
        </section>

        {{-- 5. Data Accuracy and Profile Updates --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">5. Data Accuracy and Profile Updates</h2>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li>You are responsible for the accuracy of the information you submit through the System.</li>
                <li>You agree to promptly update your profile when your employment, contact details, or educational
                    status changes.</li>
                <li>False or misleading information may result in suspension of your account.</li>
                <li>The Institution may contact you periodically to verify the accuracy of your records.</li>
            </ul>
        </section>

        {{-- 6. Comparative Descriptive Analytics and Reporting --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">6. Comparative Descriptive Analytics and Reporting</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">
                The System generates <strong>aggregated, anonymized</strong> analytics and reports for institutional
                purposes, including:
            </p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li>Yearly alumni employment statistics</li>
                <li>Trends in employment, engagement, and contributions across batches and programs</li>
                <li>Top industries of employment among graduates</li>
                <li>Engagement participation rates</li>
            </ul>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">
                <strong>Individual-level data is never publicly disclosed.</strong> Reports are presented in aggregate
                form to protect the identity of individual alumni. These analytics support institutional planning,
                accreditation requirements, and regulatory reporting to CHED and PRC.
            </p>
        </section>

        {{-- 7. Data Export and Research Use --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">7. Data Export and Research Use</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">
                Authorized users (Registrar, Program Heads) may export selected datasets for research, reporting, or
                institutional planning purposes. All exported data is subject to the following restrictions:
            </p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li><strong>No commercial use.</strong> Exported data may not be sold, licensed, or used for commercial
                    gain.</li>
                <li><strong>No re-identification.</strong> Attempts to re-identify individuals from anonymized or
                    aggregated data are strictly prohibited.</li>
                <li><strong>No redistribution.</strong> Exported data may not be shared with third parties without
                    written authorization from the Institution.</li>
                <li><strong>Secure storage.</strong> Exported datasets must be stored securely and deleted when the
                    research or reporting purpose is complete.</li>
                <li><strong>Attribution.</strong> Any publication using exported data must acknowledge the Institution
                    as the source.</li>
            </ul>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">
                All export actions are logged by the System and subject to audit. Misuse of exported data may result in
                disciplinary action and legal liability under the DPA.
            </p>
        </section>

        {{-- 8. Audit Logs and Data Integrity --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">8. Audit Logs and Data Integrity</h2>
            <p class="text-sm text-black/80 dark:text-white/80">
                The System maintains audit logs of all profile updates, record modifications, and administrative
                actions. By using the System, you acknowledge and consent that:
            </p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5 mt-2">
                <li>Your actions within the System may be recorded for security, compliance, and integrity purposes.
                </li>
                <li>Audit logs are retained for a minimum of 3 years.</li>
                <li>The System performs automated duplicate record detection to ensure data consistency.</li>
                <li>Logs may be reviewed by authorized personnel in the event of a security incident or data integrity
                    concern.</li>
            </ul>
        </section>

        {{-- 9. Intellectual Property --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">9. Intellectual Property</h2>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li><strong>Your content:</strong> You retain ownership of any posts, updates, or content you submit to
                    the System. By posting, you grant the Institution a non-exclusive, royalty-free license to display
                    and distribute your content within the alumni network.</li>
                <li><strong>System and Institution content:</strong> The System's design, code, database structure,
                    logos, branding, and analytics outputs are the exclusive property of Colegio de Sta. Ana de
                    Victorias, Inc.</li>
                <li><strong>Alumni data:</strong> Aggregate alumni data is owned by the Institution. Individual records
                    remain the property of the respective alumni, subject to the Institution's legitimate processing
                    rights.</li>
            </ul>
        </section>

        {{-- 10. Limitation of Liability --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">10. Limitation of Liability</h2>
            <p class="text-sm text-black/80 dark:text-white/80">
                The System is provided on an "as is" and "as available" basis. While the Institution implements
                reasonable security measures, it does not warrant uninterrupted or error-free operation. To the maximum
                extent permitted by Philippine law, the Institution shall not be liable for:
            </p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5 mt-2">
                <li>Content posted by other alumni</li>
                <li>Indirect, incidental, or consequential damages arising from use of the System</li>
                <li>Loss of data due to events beyond the Institution's reasonable control</li>
                <li>Actions taken by third parties who gain unauthorized access through user negligence</li>
            </ul>
        </section>

        {{-- 11. Modifications to Terms --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">11. Modifications to Terms</h2>
            <p class="text-sm text-black/80 dark:text-white/80">
                The Institution reserves the right to modify these Terms at any time. Material changes will be
                communicated via email to all registered users at least <strong>30 days</strong> before they take
                effect. Continued use of the System after the effective date constitutes acceptance of the updated
                Terms.
            </p>
        </section>

        {{-- 12. Governing Law --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">12. Governing Law</h2>
            <p class="text-sm text-black/80 dark:text-white/80">
                These Terms and Conditions shall be governed by and construed in accordance with the laws of the
                <strong>Republic of the Philippines</strong>, including but not limited to the Data Privacy Act of 2012
                (RA 10173), the Electronic Commerce Act of 2000 (RA 8792), and the Cybercrime Prevention Act of 2012 (RA
                10175). Any disputes shall be subject to the exclusive jurisdiction of the courts of Victorias City,
                Negros Occidental.
            </p>
        </section>

        {{-- 13. Contact --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">13. Contact Information</h2>
            <div
                class="p-4 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] space-y-2 text-sm text-black/80 dark:text-white/80">
                <p><strong>Alumni Network Administration</strong><br>Colegio de Sta. Ana de Victorias, Inc.<br>Victorias
                    City, Negros Occidental, Philippines</p>
                <p>Email: <strong>dpo@csav.edu.ph</strong></p>
            </div>
        </section>

    </div>

    {{-- Back --}}
    <div class="text-center mt-8">
        <a href="{{ url()->previous() }}" class="text-[#1877F2] text-sm font-semibold hover:underline">&larr; Back</a>
    </div>
</div>
