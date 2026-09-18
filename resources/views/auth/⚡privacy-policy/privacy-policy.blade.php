<div class="max-w-[900px] mx-auto px-4 py-12 select-none">

    {{-- Header --}}
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#0a1a10] rounded-3xl p-8 mb-8">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Legal</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Privacy Policy</h1>
                <p class="text-white/40 text-xs mt-1">Effective Date: {{ now()->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div
        class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 rounded-3xl p-8 space-y-8 text-black dark:text-white">

        {{-- 1. Introduction --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">1. Introduction</h2>
            <p class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                Colegio de Sta. Ana de Victorias, Inc. ("the Institution", "we", "us") operates the <strong>Web-Access
                    Streamline Alumni Information Tracking System with Comparative Descriptive Analytics</strong> ("the
                System"). This Privacy Policy explains how we collect, use, store, and protect the personal data of our
                alumni in accordance with <strong>Republic Act No. 10173</strong>, also known as the <strong>Data
                    Privacy Act of 2012 (DPA)</strong>, and its Implementing Rules and Regulations (IRR).
            </p>
            <p class="text-sm leading-relaxed text-black/80 dark:text-white/80 mt-3">
                The Institution acts as the <strong>Personal Information Controller (PIC)</strong> as defined under
                Section 3(h) of the DPA and NPC Circular No. 2022-04.
            </p>
        </section>

        {{-- 2. Data Protection Principles --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">2. Data Protection Principles</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">We adhere to the following principles under Rule IV
                of the DPA IRR:</p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5">
                <li><strong>Transparency</strong> — Alumni are informed of what data is collected, why, and how it is
                    used.</li>
                <li><strong>Legitimate Purpose</strong> — Data is processed only for alumni tracking, tracer studies,
                    and institutional analytics.</li>
                <li><strong>Proportionality</strong> — Only data necessary for the stated purposes is collected.</li>
                <li><strong>Data Quality</strong> — Alumni can update their own profiles at any time.</li>
                <li><strong>Retention Limitation</strong> — Data is retained only as long as necessary for institutional
                    and legal purposes.</li>
                <li><strong>Security</strong> — Data is protected via authentication, role-based access, and audit
                    logging.</li>
            </ul>
        </section>

        {{-- 3. Personal Data Collected --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">3. Personal Data We Collect</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-1">Identity &amp; Contact Data</h3>
                    <p class="text-sm text-black/70 dark:text-white/70">Full name, email address, school ID, mobile
                        number(s), current address (region, province, city/municipality, barangay).</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-1">Demographic Data</h3>
                    <p class="text-sm text-black/70 dark:text-white/70">Gender, civil status, year graduated, degree
                        program, department.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-1">Employment Data</h3>
                    <p class="text-sm text-black/70 dark:text-white/70">Employment status, job title, company name,
                        industry, employment type, organization type, employment location (Philippines/abroad).</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-1">Educational Data</h3>
                    <p class="text-sm text-black/70 dark:text-white/70">Further studies pursued (level and field), board
                        examination results (if applicable).</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-1">Technical Data</h3>
                    <p class="text-sm text-black/70 dark:text-white/70">IP address (for session security), login
                        timestamps, and audit logs of profile changes.</p>
                </div>
            </div>
        </section>

        {{-- 4. Purpose & Legal Basis --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">4. Purpose and Legal Basis for Processing</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">We process your personal data under the following
                lawful bases (Section 12 and 13 of the DPA):</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-black/80 dark:text-white/80 border-collapse">
                    <thead>
                        <tr class="border-b border-black/10 dark:border-white/10">
                            <th class="text-left py-2 pr-4 font-semibold">Purpose</th>
                            <th class="text-left py-2 font-semibold">Legal Basis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        <tr>
                            <td class="py-2 pr-4">Alumni tracking and tracer studies</td>
                            <td class="py-2">Consent; Legitimate Interest</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4">Institutional planning and accreditation</td>
                            <td class="py-2">Legitimate Interest</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4">Regulatory reporting (CHED, PRC)</td>
                            <td class="py-2">Legal Obligation</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4">Employment and career analytics</td>
                            <td class="py-2">Consent</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">
                Where <strong>Sensitive Personal Information</strong> (such as civil status or board examination
                results) is processed, we obtain your <strong>explicit consent</strong> as required by Section 13 of the
                DPA.
            </p>
        </section>

        {{-- 5. Data Subject Rights --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">5. Your Rights as a Data Subject</h2>
            <p class="text-sm text-black/80 dark:text-white/80 mb-3">Under Section 16 of the DPA, you have the following
                rights:</p>
            <div class="space-y-3">
                @foreach ([
        'Right to be Informed' => 'You have the right to know whether your personal data is being processed.',
        'Right to Access' => 'You may request a copy of the personal data we hold about you.',
        'Right to Rectification' => 'You may correct any inaccurate or incomplete data via your profile page.',
        'Right to Erasure or Blocking' => 'You may request the deletion of your personal data, subject to legal retention requirements.',
        'Right to Object' => 'You may object to the processing of your data based on consent or legitimate interest.',
        'Right to Data Portability' => 'You may request your data in a structured, commonly used format.',
        'Right to File a Complaint' => 'You may lodge a complaint with the National Privacy Commission.',
    ] as $title => $desc)
                    <div class="flex items-start gap-3">
                        <span
                            class="shrink-0 w-6 h-6 rounded-full bg-[#D4A537]/20 text-[#a97f1f] text-xs font-bold flex items-center justify-center">{{ $loop->iteration }}</span>
                        <div>
                            <strong class="text-sm">{{ $title }}</strong>
                            <p class="text-sm text-black/70 dark:text-white/70">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-black/80 dark:text-white/80 mt-4">
                To exercise these rights, you may update your profile via <a href="{{ route('alumni.profile') }}"
                    class="text-[#1877F2] hover:underline">/alumni/profile</a>, or contact our Data Protection Officer
                at <strong>dpo@csav.edu.ph</strong>. Requests will be processed within <strong>15 business
                    days</strong>.
            </p>
        </section>

        {{-- 6. Data Retention --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">6. Data Retention</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-black/80 dark:text-white/80 border-collapse">
                    <thead>
                        <tr class="border-b border-black/10 dark:border-white/10">
                            <th class="text-left py-2 pr-4 font-semibold">Data Category</th>
                            <th class="text-left py-2 font-semibold">Retention Period</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        <tr>
                            <td class="py-2 pr-4">Active alumni profile</td>
                            <td class="py-2">Indefinite (until deletion requested)</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4">Tracer study responses</td>
                            <td class="py-2">10 years</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4">Audit logs</td>
                            <td class="py-2">3 years</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 7. Data Sharing --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">7. Data Sharing and Disclosure</h2>
            <p class="text-sm text-black/80 dark:text-white/80">Your personal data may be shared only with:</p>
            <ul class="space-y-2 text-sm text-black/80 dark:text-white/80 list-disc pl-5 mt-2">
                <li>Registrar and program heads, within the scope of their duties</li>
                <li>CHED, PRC, and government agencies, when legally required</li>
                <li>Accreditation bodies, during institutional reviews</li>
            </ul>
            <p class="text-sm text-black/80 dark:text-white/80 mt-3">Your data is <strong>never sold, rented, or
                    traded</strong> to third parties.</p>
        </section>

        {{-- 8. Security Measures --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">8. Security Measures</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C]">
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-2">Technical</h3>
                    <ul class="text-xs text-black/70 dark:text-white/70 space-y-1">
                        <li>Role-based access control (RBAC)</li>
                        <li>Password hashing with bcrypt</li>
                        <li>HTTPS/TLS encryption</li>
                        <li>CSRF protection</li>
                        <li>Audit logging</li>
                    </ul>
                </div>
                <div class="p-4 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C]">
                    <h3 class="text-sm font-semibold text-black dark:text-white mb-2">Organizational</h3>
                    <ul class="text-xs text-black/70 dark:text-white/70 space-y-1">
                        <li>Data Protection Officer (DPO) appointed</li>
                        <li>Staff privacy training</li>
                        <li>Incident response plan</li>
                        <li>Regular security audits</li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- 9. Breach Notification --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">9. Data Breach Notification</h2>
            <p class="text-sm text-black/80 dark:text-white/80">
                In the event of a personal data breach, the <strong>National Privacy Commission</strong> and
                <strong>affected data subjects</strong> will be notified within <strong>72 hours</strong>, in accordance
                with NPC Circular No. 16-03.
            </p>
        </section>

        {{-- 10. Contact --}}
        <section>
            <h2 class="text-lg font-bold text-[#123524] dark:text-[#D4A537] mb-3"
                style="font-family: 'Fraunces', serif;">10. Contact Information</h2>
            <div
                class="p-4 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] space-y-2 text-sm text-black/80 dark:text-white/80">
                <p><strong>Data Protection Officer (DPO)</strong><br>Colegio de Sta. Ana de Victorias, Inc.<br>Victorias
                    City, Negros Occidental, Philippines</p>
                <p>Email: <strong>dpo@csav.edu.ph</strong></p>
                <p class="pt-2 border-t border-black/5 dark:border-white/10">
                    <strong>National Privacy Commission</strong><br>
                    Website: privacy.gov.ph<br>
                    Email: complaints@privacy.gov.ph
                </p>
            </div>
        </section>

    </div>

    {{-- Back --}}
    <div class="text-center mt-8">
        <a href="{{ url()->previous() }}" class="text-[#1877F2] text-sm font-semibold hover:underline">&larr; Back</a>
    </div>
</div>
