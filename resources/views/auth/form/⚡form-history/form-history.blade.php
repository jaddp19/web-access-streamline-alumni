<div class="min-h-screen bg-[#F7F5EF] py-6 px-3 sm:py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-3xl mx-auto">

        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#123524] leading-tight"
                style="font-family: 'Fraunces', serif;">
                Form History
            </h1>
            <p class="text-sm sm:text-base text-[#123524]/60 mt-1.5 sm:mt-2">
                Every form accepted by a Program Head or Alumni Coordinator is kept here.
                Pending edits stay on your current form until they are approved.
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
            <a href="{{ route('alumni.settings') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#123524]/15 bg-white text-[#123524] text-sm font-semibold hover:bg-[#123524]/5 transition">
                Back to Settings
            </a>
            <a href="{{ route('update-form') }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-[#123524]/15 bg-white text-[#123524] text-sm font-semibold hover:bg-[#123524]/5 transition">
                Update Current Form
            </a>
            <a href="{{ route('form', ['fresh' => 1]) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#D4A537] hover:bg-[#c2942f] text-[#123524] text-sm font-semibold transition">
                Update New Form
            </a>
        </div>

        @if ($this->histories->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-[#123524]/10 p-6 sm:p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-[#D4A537]/15 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#a97f1f]" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-[#123524] mb-2" style="font-family: 'Fraunces', serif;">
                    No accepted forms yet
                </h2>
                <p class="text-sm text-[#123524]/60 max-w-md mx-auto leading-relaxed">
                    Your current form will appear here once a Program Head or Alumni Coordinator accepts it.
                    Until then, edits only update your pending form.
                </p>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($this->histories as $history)
                    @php
                        $snapshot = $history->snapshot ?? [];
                        $personal = $snapshot['personal'] ?? [];
                        $tracer = $snapshot['tracer'] ?? [];
                        $further = $snapshot['further_study'] ?? [];

                        $employmentStatus = $tracer['employment_status'] ?? null;
                        $employmentStatusLabel = $employmentStatus === 'other' && filled($tracer['employment_status_other'] ?? null)
                            ? 'Other ('.$tracer['employment_status_other'].')'
                            : ($employmentStatus ? \Illuminate\Support\Str::headline($employmentStatus) : '—');

                        $employmentType = $tracer['employment_type'] ?? null;
                        $employmentTypeLabel = $employmentType === 'other' && filled($tracer['employment_type_other'] ?? null)
                            ? 'Other ('.$tracer['employment_type_other'].')'
                            : ($employmentType ? \Illuminate\Support\Str::headline($employmentType) : '—');

                        $organizationType = $tracer['organization_type'] ?? null;
                        $organizationTypeLabel = $organizationType === 'other' && filled($tracer['organization_type_other'] ?? null)
                            ? 'Other ('.$tracer['organization_type_other'].')'
                            : ($organizationType ? \Illuminate\Support\Str::headline($organizationType) : '—');

                        $courseTitles = collect($personal['courses'] ?? [])->pluck('title')->filter()->implode(', ');
                    @endphp

                    <article class="bg-white rounded-2xl shadow-sm border border-[#123524]/10 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-[#123524]/10">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-lg sm:text-xl font-bold text-[#123524]"
                                            style="font-family: 'Fraunces', serif;">
                                            Form #{{ $history->version }}
                                        </h2>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide">
                                            Accepted
                                        </span>
                                    </div>
                                    <p class="text-sm text-[#123524]/60 mt-1">
                                        Approved {{ $history->approved_at?->format('F j, Y · g:i A') ?? '—' }}
                                    </p>
                                    <p class="text-xs text-[#123524]/50 mt-0.5">
                                        By {{ $history->approvedBy?->full_name ?: 'the alumni coordinator' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="min-w-0">
                                    <dt class="text-[10px] sm:text-xs text-[#123524]/50 uppercase tracking-wide font-semibold">
                                        Civil Status</dt>
                                    <dd class="text-sm sm:text-base text-[#123524] mt-1">
                                        {{ filled($tracer['civil_status'] ?? null) ? \Illuminate\Support\Str::headline($tracer['civil_status']) : '—' }}
                                    </dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-[10px] sm:text-xs text-[#123524]/50 uppercase tracking-wide font-semibold">
                                        Employment Status</dt>
                                    <dd class="text-sm sm:text-base text-[#123524] mt-1">{{ $employmentStatusLabel }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-[10px] sm:text-xs text-[#123524]/50 uppercase tracking-wide font-semibold">
                                        Current Job</dt>
                                    <dd class="text-sm sm:text-base text-[#123524] mt-1">
                                        {{ filled($tracer['current_job_position'] ?? null) ? $tracer['current_job_position'] : '—' }}
                                    </dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-[10px] sm:text-xs text-[#123524]/50 uppercase tracking-wide font-semibold">
                                        Further Studies</dt>
                                    <dd class="text-sm sm:text-base text-[#123524] mt-1">
                                        @if ($further['is_pursued_further_studies'] ?? false)
                                            Yes{{ filled($further['level_of_study'] ?? null) ? ' · '.$further['level_of_study'] : '' }}
                                        @else
                                            No
                                        @endif
                                    </dd>
                                </div>
                            </dl>

                            <details class="mt-5 pt-4 border-t border-[#123524]/10">
                                <summary
                                    class="cursor-pointer list-none inline-flex items-center gap-2 text-sm font-semibold text-[#123524] hover:text-[#0d2819]">
                                    View full form
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </summary>

                                @include('auth.form.partials.form-history-snapshot', [
                                    'snapshot' => $snapshot,
                                    'personal' => $personal,
                                    'tracer' => $tracer,
                                    'further' => $further,
                                    'employmentStatusLabel' => $employmentStatusLabel,
                                    'employmentTypeLabel' => $employmentTypeLabel,
                                    'organizationTypeLabel' => $organizationTypeLabel,
                                    'courseTitles' => $courseTitles,
                                ])
                            </details>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>
