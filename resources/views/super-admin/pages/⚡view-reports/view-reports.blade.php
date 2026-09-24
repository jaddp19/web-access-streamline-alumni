<div>
    <div class="w-full p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">

            {{-- ========== HEADER ========== --}}
            <div class="no-print flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white"
                        style="font-family: 'Fraunces', serif;">
                        Reports
                    </h1>
                    <p class="text-sm text-black/50 dark:text-white/50 mt-0.5">
                        Generate institutional reports across all batches, programs, and alumni.
                    </p>
                </div>
            </div>

            {{-- ========== TABS + FILTERS (no-print) ========== --}}
            <div
                class="no-print bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-3 sm:p-4 space-y-3">

                {{-- Row 1: Tabs + Print --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div
                        class="inline-flex items-center gap-1 p-1 rounded-xl bg-black/5 dark:bg-white/5 overflow-x-auto">
                        <button type="button" wire:click="setTab('employment')"
                            class="shrink-0 px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                                {{ $tab === 'employment'
                                    ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                    : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                            Employment Stats
                        </button>
                        <button type="button" wire:click="setTab('industry')"
                            class="shrink-0 px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                                {{ $tab === 'industry'
                                    ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                    : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                            Top Industries
                        </button>
                        <button type="button" wire:click="setTab('engagement')"
                            class="shrink-0 px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                                {{ $tab === 'engagement'
                                    ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                    : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                            Engagement
                        </button>
                    </div>

                    <button type="button" onclick="window.print()"
                        class="shrink-0 self-start sm:self-auto inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-xs sm:text-sm font-semibold hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                        <span class="hidden sm:inline">Print / PDF</span>
                        <span class="sm:hidden">Print</span>
                    </button>
                </div>

                {{-- Row 2: Filters --}}
                <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-black/5 dark:border-white/5">

                    {{-- Department --}}
                    <div class="flex items-center gap-1.5">
                        <label for="dept-filter"
                            class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">
                            Department
                        </label>
                        <select id="dept-filter" wire:model.live="selectedDepartmentId"
                            class="px-3 py-2 text-xs sm:text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[100px]">
                            <option value="">All</option>
                            @foreach ($this->departments as $dept)
                                <option value="{{ $dept['id'] }}">{{ $dept['dept_code'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course --}}
                    <div class="flex items-center gap-1.5">
                        <label for="course-filter"
                            class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">
                            Course
                        </label>
                        <select id="course-filter" wire:model.live="selectedCourseId"
                            class="px-3 py-2 text-xs sm:text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[110px]">
                            <option value="">All</option>
                            @foreach ($this->courses as $course)
                                <option value="{{ $course['id'] }}">{{ $course['course_code'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Batch --}}
                    <div class="flex items-center gap-1.5">
                        <label for="batch-filter"
                            class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">
                            Batch
                        </label>
                        <select id="batch-filter" wire:model.live="selectedBatchId"
                            class="px-3 py-2 text-xs sm:text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[130px]">
                            <option value="">All batches</option>
                            @foreach ($this->batches as $batch)
                                <option value="{{ $batch['id'] }}">{{ $batch['batch_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date range (engagement only) --}}
                    @if ($tab === 'engagement')
                        <div class="flex items-center gap-1.5">
                            <label for="date-filter"
                                class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">
                                Period
                            </label>
                            <select id="date-filter" wire:model.live="dateRange"
                                class="px-3 py-2 text-xs sm:text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[130px]">
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="180">Last 6 months</option>
                                <option value="365">Last 12 months</option>
                                <option value="all">All time</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ========== PRINT HEADER ========== --}}
            <div class="print-only hidden print:block mb-4 pb-4 border-b border-black">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">
                            Colegio de Sta. Ana de Victorias, Inc.
                        </h1>
                        <p class="text-sm text-black/60 mt-0.5">Alumni Information Tracking System</p>
                    </div>
                    <div class="text-right text-xs text-black/60">
                        <p class="font-semibold">{{ $this->reportLabel }}</p>
                        <p>Generated: {{ now()->format('F j, Y · g:i A') }}</p>
                        @if ($this->batchLabel)
                            <p>Batch: {{ $this->batchLabel }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ========== REPORT CONTENT ========== --}}
            <div
                class="print-area bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl overflow-hidden">

                {{-- ============ TAB 1 — EMPLOYMENT STATS ============ --}}
                @if ($tab === 'employment')
                    <div
                        class="px-4 sm:px-6 py-4 border-b border-black/5 dark:border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-[#0f2b1c] dark:text-white"
                                style="font-family: 'Fraunces', serif;">
                                Yearly Alumni Employment Statistics
                            </h2>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">
                                Employment distribution per batch · employment rate = employed / total
                            </p>
                        </div>

                        <button type="button" wire:click="exportTracerCsv" wire:loading.attr="disabled"
                            wire:target="exportTracerCsv"
                            class="no-print shrink-0 inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                                   bg-white dark:bg-[#3A3B3C]
                                   border border-black/10 dark:border-white/10
                                   text-[#123524] dark:text-white
                                   text-xs sm:text-sm font-semibold
                                   hover:bg-black/5 dark:hover:bg-white/5
                                   transition disabled:opacity-50 disabled:cursor-wait">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span wire:loading.remove wire:target="exportTracerCsv">Export CSV</span>
                            <span wire:loading wire:target="exportTracerCsv">Exporting…</span>
                        </button>
                    </div>

                    @if (empty($this->employmentRows))
                        <div class="px-6 py-16 text-center">
                            <p class="text-sm text-black/40 dark:text-white/40">No employment data available for the
                                selected filters.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs sm:text-sm">
                                <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Batch</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Total</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-400 text-[11px]">
                                            Employed</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-red-700 dark:text-red-400 text-[11px]">
                                            Unemployed</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-amber-700 dark:text-amber-400 text-[11px]">
                                            Self-Emp.</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Other</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    @foreach ($this->employmentRows as $row)
                                        <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                            <td class="px-4 py-3 font-semibold text-[#123524] dark:text-white">
                                                {{ $row['batch_name'] }}</td>
                                            <td class="px-4 py-3 text-end text-black/70 dark:text-white/70">
                                                {{ $row['total'] }}</td>
                                            <td
                                                class="px-4 py-3 text-end text-emerald-700 dark:text-emerald-400 font-semibold">
                                                {{ $row['employed'] }}</td>
                                            <td class="px-4 py-3 text-end text-red-700 dark:text-red-400">
                                                {{ $row['unemployed'] }}</td>
                                            <td class="px-4 py-3 text-end text-amber-700 dark:text-amber-400">
                                                {{ $row['self_employed'] }}</td>
                                            <td class="px-4 py-3 text-end text-black/70 dark:text-white/70">
                                                {{ $row['other'] }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold
                                                    {{ $row['employment_rate'] >= 75
                                                        ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                                        : ($row['employment_rate'] >= 50
                                                            ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400'
                                                            : 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400') }}">
                                                    {{ number_format($row['employment_rate'], 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot
                                    class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-t-2 border-black/10 dark:border-white/10">
                                    @php $t = $this->employmentTotals; @endphp
                                    <tr>
                                        <td
                                            class="px-4 py-3 font-bold text-[#123524] dark:text-white uppercase text-[11px] tracking-wide">
                                            Total</td>
                                        <td class="px-4 py-3 text-end font-bold text-[#123524] dark:text-white">
                                            {{ $t['total'] }}</td>
                                        <td
                                            class="px-4 py-3 text-end font-bold text-emerald-700 dark:text-emerald-400">
                                            {{ $t['employed'] }}</td>
                                        <td class="px-4 py-3 text-end font-bold text-red-700 dark:text-red-400">
                                            {{ $t['unemployed'] }}</td>
                                        <td class="px-4 py-3 text-end font-bold text-amber-700 dark:text-amber-400">
                                            {{ $t['self_employed'] }}</td>
                                        <td class="px-4 py-3 text-end font-bold text-black/70 dark:text-white/70">
                                            {{ $t['other'] }}</td>
                                        <td class="px-4 py-3 text-end">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#123524]/10 dark:bg-[#D4A537]/15 text-[#123524] dark:text-[#D4A537]">
                                                {{ number_format($t['employment_rate'], 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                @endif

                {{-- ============ TAB 2 — TOP INDUSTRIES ============ --}}
                @if ($tab === 'industry')
                    <div class="px-4 sm:px-6 py-4 border-b border-black/5 dark:border-white/5">
                        <h2 class="text-sm sm:text-base font-bold text-[#0f2b1c] dark:text-white"
                            style="font-family: 'Fraunces', serif;">
                            Top Industries & Employers
                        </h2>
                        <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">
                            Distribution by organization type and most common employers
                        </p>
                    </div>

                    <div class="px-4 sm:px-6 pt-5">
                        <h3
                            class="text-[11px] font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 mb-3">
                            By Organization Type
                        </h3>

                        @if (empty($this->industryDistribution))
                            <p class="text-sm text-black/40 dark:text-white/40 py-8 text-center">No industry data
                                available.</p>
                        @else
                            <div class="overflow-x-auto border border-black/5 dark:border-white/5 rounded-xl">
                                <table class="min-w-full text-xs sm:text-sm">
                                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                                        <tr>
                                            <th
                                                class="px-4 py-2.5 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Organization Type</th>
                                            <th
                                                class="px-4 py-2.5 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Alumni</th>
                                            <th
                                                class="px-4 py-2.5 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Share</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                        @foreach ($this->industryDistribution as $type => $count)
                                            @php
                                                $label = str_replace('-', ' ', $type);
                                                $label = ucwords($label);
                                                $share =
                                                    $this->industryTotal > 0
                                                        ? round(($count / $this->industryTotal) * 100, 1)
                                                        : 0;
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-2.5 text-[#123524] dark:text-white font-medium">
                                                    {{ $label }}</td>
                                                <td
                                                    class="px-4 py-2.5 text-end text-black/70 dark:text-white/70 font-semibold">
                                                    {{ $count }}</td>
                                                <td class="px-4 py-2.5 text-end text-black/60 dark:text-white/60">
                                                    {{ number_format($share, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="px-4 sm:px-6 pt-6 pb-5">
                        <h3
                            class="text-[11px] font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 mb-3">
                            Top Employers
                        </h3>

                        @if (empty($this->topEmployers))
                            <p class="text-sm text-black/40 dark:text-white/40 py-8 text-center">No employer data
                                available.</p>
                        @else
                            @php $totalEmployers = array_sum(array_column($this->topEmployers, 'total')); @endphp
                            <div class="overflow-x-auto border border-black/5 dark:border-white/5 rounded-xl">
                                <table class="min-w-full text-xs sm:text-sm">
                                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                                        <tr>
                                            <th
                                                class="px-4 py-2.5 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px] w-12">
                                                #</th>
                                            <th
                                                class="px-4 py-2.5 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Company</th>
                                            <th
                                                class="px-4 py-2.5 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px] hidden sm:table-cell">
                                                Location</th>
                                            <th
                                                class="px-4 py-2.5 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Alumni</th>
                                            <th
                                                class="px-4 py-2.5 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                                Share</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                        @foreach ($this->topEmployers as $i => $row)
                                            @php $share = $totalEmployers > 0 ? round(($row['total'] / $totalEmployers) * 100, 1) : 0; @endphp
                                            <tr>
                                                <td class="px-4 py-2.5 font-bold text-[#123524]/40 dark:text-white/40">
                                                    {{ $i + 1 }}</td>
                                                <td class="px-4 py-2.5 text-[#123524] dark:text-white font-medium">
                                                    {{ $row['company_name'] }}</td>
                                                <td
                                                    class="px-4 py-2.5 text-black/60 dark:text-white/60 text-xs hidden sm:table-cell">
                                                    {{ $row['company_address'] ?: '—' }}</td>
                                                <td
                                                    class="px-4 py-2.5 text-end text-black/70 dark:text-white/70 font-semibold">
                                                    {{ $row['total'] }}</td>
                                                <td class="px-4 py-2.5 text-end text-black/60 dark:text-white/60">
                                                    {{ number_format($share, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ============ TAB 3 — ENGAGEMENT ============ --}}
                @if ($tab === 'engagement')
                    <div
                        class="px-4 sm:px-6 py-4 border-b border-black/5 dark:border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-[#0f2b1c] dark:text-white"
                                style="font-family: 'Fraunces', serif;">
                                Alumni Engagement Participation Rates
                            </h2>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">
                                RSVP response breakdown per event · participation = yes / invited alumni
                            </p>
                        </div>

                        <button type="button" wire:click="exportRsvpCsv" wire:loading.attr="disabled"
                            wire:target="exportRsvpCsv"
                            class="no-print shrink-0 inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                                   bg-white dark:bg-[#3A3B3C]
                                   border border-black/10 dark:border-white/10
                                   text-[#123524] dark:text-white
                                   text-xs sm:text-sm font-semibold
                                   hover:bg-black/5 dark:hover:bg-white/5
                                   transition disabled:opacity-50 disabled:cursor-wait">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span wire:loading.remove wire:target="exportRsvpCsv">Export CSV</span>
                            <span wire:loading wire:target="exportRsvpCsv">Exporting…</span>
                        </button>
                    </div>

                    @if (empty($this->engagementRows))
                        <div class="px-6 py-16 text-center">
                            <p class="text-sm text-black/40 dark:text-white/40">No events found for the selected
                                filters.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs sm:text-sm">
                                <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Event</th>
                                        <th
                                            class="px-4 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px] hidden md:table-cell">
                                            Date</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Invited</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-400 text-[11px]">
                                            Yes</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-amber-700 dark:text-amber-400 text-[11px]">
                                            Maybe</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-red-700 dark:text-red-400 text-[11px]">
                                            No</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px] hidden sm:table-cell">
                                            Pending</th>
                                        <th
                                            class="px-4 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                            Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    @foreach ($this->engagementRows as $row)
                                        <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                            <td class="px-4 py-3">
                                                <p
                                                    class="font-semibold text-[#123524] dark:text-white truncate max-w-[220px]">
                                                    {{ $row['title'] }}</p>
                                                <p class="text-[11px] text-black/40 dark:text-white/40 md:hidden">
                                                    {{ \Carbon\Carbon::parse($row['starts_at'])->format('M j, Y') }}
                                                </p>
                                            </td>
                                            <td
                                                class="px-4 py-3 text-black/60 dark:text-white/60 hidden md:table-cell whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($row['starts_at'])->format('M j, Y') }}</td>
                                            <td class="px-4 py-3 text-end text-black/70 dark:text-white/70">
                                                {{ $row['invited'] }}</td>
                                            <td
                                                class="px-4 py-3 text-end text-emerald-700 dark:text-emerald-400 font-semibold">
                                                {{ $row['yes'] }}</td>
                                            <td class="px-4 py-3 text-end text-amber-700 dark:text-amber-400">
                                                {{ $row['maybe'] }}</td>
                                            <td class="px-4 py-3 text-end text-red-700 dark:text-red-400">
                                                {{ $row['no'] }}</td>
                                            <td
                                                class="px-4 py-3 text-end text-black/50 dark:text-white/50 hidden sm:table-cell">
                                                {{ $row['pending'] }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold
                                                    {{ $row['participation'] >= 50
                                                        ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                                        : ($row['participation'] >= 25
                                                            ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400'
                                                            : 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400') }}">
                                                    {{ number_format($row['participation'], 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot
                                    class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-t-2 border-black/10 dark:border-white/10">
                                    @php $e = $this->engagementTotals; @endphp
                                    <tr>
                                        <td
                                            class="px-4 py-3 font-bold text-[#123524] dark:text-white uppercase text-[11px] tracking-wide">
                                            {{ $e['events'] }} events</td>
                                        <td class="px-4 py-3 hidden md:table-cell"></td>
                                        <td class="px-4 py-3 text-end text-black/50 dark:text-white/50">—</td>
                                        <td
                                            class="px-4 py-3 text-end font-bold text-emerald-700 dark:text-emerald-400">
                                            {{ $e['yes'] }}</td>
                                        <td class="px-4 py-3 text-end font-bold text-amber-700 dark:text-amber-400">
                                            {{ $e['maybe'] }}</td>
                                        <td class="px-4 py-3 text-end font-bold text-red-700 dark:text-red-400">
                                            {{ $e['no'] }}</td>
                                        <td
                                            class="px-4 py-3 text-end font-bold text-black/50 dark:text-white/50 hidden sm:table-cell">
                                            {{ $e['pending'] }}</td>
                                        <td class="px-4 py-3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                margin: 12mm;
            }

            body {
                background: #fff !important;
            }

            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            table {
                font-size: 11px !important;
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</div>
