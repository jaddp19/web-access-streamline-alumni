<div class="mt-4 space-y-6">
    {{-- Personal information --}}
    <section>
        <h3 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-3">Personal Information</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Full Name</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $personal['name'] ?? '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Email</dt>
                <dd class="text-sm text-[#123524] mt-0.5 break-all">{{ $personal['email'] ?? '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Sex</dt>
                <dd class="text-sm text-[#123524] mt-0.5">
                    {{ filled($personal['gender'] ?? null) ? \Illuminate\Support\Str::headline($personal['gender']) : '—' }}
                </dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Mobile Number</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $personal['contact_number_1'] ?? '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Alternate Number</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $personal['contact_number_2'] ?: '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Year Graduated</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $personal['batch'] ?? '—' }}</dd>
            </div>
            <div class="min-w-0 sm:col-span-2">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Address</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $personal['address'] ?: '—' }}</dd>
            </div>
            <div class="min-w-0 sm:col-span-2">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Program</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $courseTitles ?: '—' }}</dd>
            </div>
        </dl>

        <div class="mt-4">
            <p class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold mb-2">Board Exam</p>
            <div class="space-y-2">
                @forelse ($personal['board_exams'] ?? [] as $exam)
                    <div class="rounded-xl border border-[#123524]/10 bg-[#F7F5EF]/60 p-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-[#123524]">
                                {{ filled($exam['exam_name'] ?? null) ? $exam['exam_name'] : 'Board Exam' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-1">
                                @if ($exam['passed'] ?? false)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide">Passed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wide">Failed</span>
                                @endif
                                @if ($exam['is_verified'] ?? false)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wide">Verified</span>
                                @endif
                                @if ($exam['is_top_notcher'] ?? false)
                                    <span class="px-2 py-0.5 rounded-full bg-[#D4A537]/20 text-[#8a6514] text-[10px] font-bold uppercase tracking-wide">
                                        Top {{ $exam['top_notcher_rank'] ?? '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-[#123524]/60 mt-1">
                            @if (filled($exam['attempt_number'] ?? null))
                                Attempt {{ $exam['attempt_number'] }} ·
                            @endif
                            {{ filled($exam['date_taken'] ?? null) ? \Carbon\Carbon::parse($exam['date_taken'])->format('F j, Y') : 'Date not set' }}
                            @if (($exam['rate'] ?? null) !== null)
                                · Rating {{ number_format((float) $exam['rate'], 2) }}
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-[#123524]/60">No board exam recorded.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Civil status and employment --}}
    <section>
        <h3 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-3">Civil Status & Employment</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Civil Status</dt>
                <dd class="text-sm text-[#123524] mt-0.5">
                    {{ filled($tracer['civil_status'] ?? null) ? \Illuminate\Support\Str::headline($tracer['civil_status']) : '—' }}
                </dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Employment Status</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $employmentStatusLabel }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Current Job Position</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $tracer['current_job_position'] ?: '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Related to Degree</dt>
                <dd class="text-sm text-[#123524] mt-0.5">
                    {{ filled($tracer['employed_related_to_degree'] ?? null) ? \Illuminate\Support\Str::headline($tracer['employed_related_to_degree']) : '—' }}
                </dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Employment Type</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $employmentTypeLabel }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Organization Type</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $organizationTypeLabel }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Employment Area</dt>
                <dd class="text-sm text-[#123524] mt-0.5">
                    {{ filled($tracer['employment_area'] ?? null) ? \Illuminate\Support\Str::headline($tracer['employment_area']) : '—' }}
                </dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Country (if abroad)</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $tracer['abroad_country'] ?: '—' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Months to First Job</dt>
                <dd class="text-sm text-[#123524] mt-0.5">
                    @php
                        $monthsLabel = match ($tracer['months_to_first_job'] ?? null) {
                            '1-3-months' => '1–3 Months',
                            '4-6-months' => '4–6 Months',
                            'more-than-6-months' => 'More than 6 Months',
                            'more-than-1-year' => 'More than 1 Year',
                            'not-yet-employed' => 'Not yet employed',
                            default => $tracer['months_to_first_job'] ?? '—',
                        };
                    @endphp
                    {{ $monthsLabel }}
                </dd>
            </div>
        </dl>
    </section>

    {{-- Work history --}}
    <section>
        <h3 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-3">Work History</h3>
        <div class="space-y-2">
            @forelse ($snapshot['work_histories'] ?? [] as $work)
                <div class="rounded-xl border border-[#123524]/10 bg-[#F7F5EF]/60 p-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#123524]">
                                {{ filled($work['work_name'] ?? null) ? $work['work_name'] : 'Position not specified' }}
                            </p>
                            <p class="text-sm text-[#123524]/60">
                                {{ filled($work['company_name'] ?? null) ? $work['company_name'] : 'Company not specified' }}
                            </p>
                        </div>
                        @if ($work['is_current_job'] ?? false)
                            <span class="px-2 py-0.5 rounded-full bg-[#123524] text-white text-[10px] font-bold uppercase tracking-wide">
                                Current
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-[#123524]/60 mt-1">
                        Hired {{ filled($work['date_hired'] ?? null) ? \Carbon\Carbon::parse($work['date_hired'])->format('F j, Y') : '—' }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-[#123524]/60">No work history recorded.</p>
            @endforelse
        </div>
    </section>

    {{-- Further studies --}}
    <section>
        <h3 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-3">Further Studies</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Pursued Further Studies</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ ($further['is_pursued_further_studies'] ?? false) ? 'Yes' : 'No' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-[10px] text-[#123524]/50 uppercase tracking-wide font-semibold">Level of Study</dt>
                <dd class="text-sm text-[#123524] mt-0.5">{{ $further['level_of_study'] ?: '—' }}</dd>
            </div>
        </dl>
    </section>
</div>
