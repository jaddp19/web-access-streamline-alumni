<div>
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">
                        Overview
                    </h1>
                    <p class="text-sm text-black/50 mt-0.5">
                        @if ($this->hasNoDepartment)
                            Waiting for department assignment.
                        @elseif ($selectedBatchId)
                            Showing analytics for Batch
                            {{ collect($this->batches)->firstWhere('id', (int) $selectedBatchId)['batch_name'] ?? '—' }}.
                        @elseif ($this->isRegistrar)
                            A snapshot of the alumni network across all batches.
                        @else
                            A snapshot of {{ $this->myDepartmentName }} alumni.
                        @endif
                    </p>
                </div>

                @unless ($this->hasNoDepartment)
                    <div class="shrink-0 flex items-center gap-2">
                        <button type="button" wire:click="refreshAnalytics"
                            wire:loading.attr="disabled" wire:target="refreshAnalytics,selectedBatchId"
                            class="p-2 rounded-xl border border-black/10 bg-white text-[#0f2b1c] hover:bg-[#D4A537]/10 transition disabled:opacity-50"
                            title="Refresh analytics">
                            <svg wire:loading.remove wire:target="refreshAnalytics,selectedBatchId" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <svg wire:loading wire:target="refreshAnalytics,selectedBatchId" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </button>

                        <label for="batch-filter" class="text-[11px] font-bold text-black/50 uppercase tracking-wide whitespace-nowrap">Batch</label>

                        <div class="relative">
                            <select id="batch-filter" wire:model.live.debounce.500ms="selectedBatchId"
                                wire:loading.attr="disabled" wire:target="selectedBatchId"
                                class="px-3 py-2 pr-9 text-sm rounded-xl border border-black/10 bg-white text-[#0f2b1c] font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[150px] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
                                <option value="">Overall</option>
                                @foreach ($this->batches as $batch)
                                    <option value="{{ $batch['id'] }}">{{ $batch['batch_name'] }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="selectedBatchId" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 animate-spin text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endunless
            </div>

            @if ($this->hasNoDepartment)
                <div class="rounded-2xl bg-amber-50 border border-amber-200 p-6 sm:p-8 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center text-amber-700">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-amber-900" style="font-family: 'Fraunces', serif;">No department assigned yet</h2>
                    <p class="text-sm text-amber-800/80 mt-2 max-w-md mx-auto">
                        You don't have access to any alumni data until the registrar assigns you to a department.
                    </p>
                </div>
            @else
                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedBatchId"
                    class="transition-opacity duration-200 space-y-6">

                    {{-- Stat cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                        <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Total Users</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->users }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Active Alumni</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->active }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Total Alumni</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->alumni }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Program Heads</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->programHeads }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main charts --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c]">Alumni Graduates</h2>
                            <p class="text-xs text-black/40 mt-0.5 mb-3">Breakdown by department</p>
                            @if (empty($this->alumniByDept))
                                <p class="text-sm text-black/40 py-16 text-center">No alumni-to-department records yet.</p>
                            @else
                                <div class="w-full h-64 sm:h-72 md:h-80"><canvas id="alumniDynamicChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c]">Comparative Analysis</h2>
                            <p class="text-xs text-black/40 mt-0.5 mb-1">Course alignment with current work</p>
                            <p class="text-xs text-black/50 mb-3">{{ $this->furtherStudiesRate }}% pursued further studies</p>
                            @if (empty($this->courseAnalytics))
                                <p class="text-sm text-black/40 py-16 text-center">No course analytics yet.</p>
                            @else
                                <div class="w-full h-64 sm:h-72 md:h-80"><canvas id="comparativeChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Alumni by Year --}}
                    <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <h2 class="text-sm font-bold text-[#0f2b1c]">Alumni Graduates by Year</h2>
                        <p class="text-xs text-black/40 mt-0.5 mb-3">Total graduates per batch</p>
                        @if (empty($this->alumniByBatch))
                            <p class="text-sm text-black/40 py-16 text-center">No batch records yet.</p>
                        @else
                            <div class="w-full h-72"><canvas id="alumniByBatchChart"></canvas></div>
                        @endif
                    </div>

                    {{-- Analytics section header --}}
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">Analytics</h2>
                        <p class="text-sm text-black/50 mt-0.5">Deeper breakdown from the tracer study.</p>
                    </div>

                    {{-- Row: Employment status + Employment type --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Status</h2>
                            <p class="text-xs text-black/40 mb-3">Employed, unemployed, self-employed</p>
                            @if (empty($this->employmentStatusBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No tracer employment data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentStatusChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Type</h2>
                            <p class="text-xs text-black/40 mb-3">Full-time, part-time, freelance, etc.</p>
                            @if (empty($this->employmentTypeBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No employment type yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentTypeChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Row: Org type + Employment area --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Organization Type</h2>
                            <p class="text-xs text-black/40 mb-3">Where alumni currently work</p>
                            @if (empty($this->organizationTypeBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No organization type yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="organizationTypeChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Area</h2>
                            <p class="text-xs text-black/40 mb-3">Philippines vs. abroad</p>
                            @if (empty($this->employmentAreaBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No employment area yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentAreaChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Time to First Job --}}
                    <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Time to First Job</h2>
                        <p class="text-xs text-black/40 mb-3">How long after graduation alumni got employed</p>
                        @if (collect($this->monthsToFirstJobBreakdown)->sum() === 0)
                            <p class="text-sm text-black/40 py-16 text-center">No time-to-first-job data yet.</p>
                        @else
                            <div class="w-full h-64"><canvas id="monthsToFirstJobChart"></canvas></div>
                        @endif
                    </div>

                    {{-- NEW SECTION HEADER --}}
                    <div class="pt-2">
                        <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">Demographics & Outcomes</h2>
                        <p class="text-sm text-black/50 mt-0.5">Additional insights from profiles and work history.</p>
                    </div>

                    {{-- Row: Gender + Civil status --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Gender Distribution</h2>
                            <p class="text-xs text-black/40 mb-3">Male vs. female alumni</p>
                            @if (empty($this->genderBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No gender data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="genderChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Civil Status</h2>
                            <p class="text-xs text-black/40 mb-3">Single, married, widowed, etc.</p>
                            @if (empty($this->civilStatusBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No civil status data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="civilStatusChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Row: Further studies + Job alignment --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Further Studies Level</h2>
                            <p class="text-xs text-black/40 mb-3">Certificate, Bachelor, Master, Doctorate</p>
                            @if (empty($this->furtherStudiesLevelBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No further studies data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="furtherStudiesChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Job Alignment to Degree</h2>
                            <p class="text-xs text-black/40 mb-3">Related, partially related, not related</p>
                            @if (empty($this->jobAlignmentBreakdown))
                                <p class="text-sm text-black/40 py-16 text-center">No job alignment data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="jobAlignmentChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Row: Top employers + Board exam --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Top Employers</h2>
                            <p class="text-xs text-black/40 mb-3">Most common companies among alumni</p>
                            @if (empty($this->topEmployers))
                                <p class="text-sm text-black/40 py-16 text-center">No employer data yet.</p>
                            @else
                                <div class="w-full h-72"><canvas id="topEmployersChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Board Exam Performance</h2>
                            <p class="text-xs text-black/40 mb-3">Passed vs. failed (75%+ = passed)</p>
                            @if (collect($this->boardExamBreakdown)->sum() === 0)
                                <p class="text-sm text-black/40 py-16 text-center">No board exam data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="boardExamChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Alumni by Region --}}
                    <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Alumni by Region</h2>
                        <p class="text-xs text-black/40 mb-3">Geographic distribution (top regions)</p>
                        @if (empty($this->alumniByRegion))
                            <p class="text-sm text-black/40 py-16 text-center">No region data yet.</p>
                        @else
                            <div class="w-full h-72"><canvas id="alumniByRegionChart"></canvas></div>
                        @endif
                    </div>

                </div>
            @endif
        </div>
    </div>

    @unless ($this->hasNoDepartment)
        {{-- Payload for charts --}}
        <script type="application/json" id="analytics-payload">
            {!! json_encode([
                'alumniByDept'              => $this->alumniByDept,
                'alumniByBatch'             => $this->alumniByBatch,
                'courseAnalytics'           => $this->courseAnalytics,
                'employmentStatus'          => $this->employmentStatusBreakdown,
                'employmentType'            => $this->employmentTypeBreakdown,
                'organizationType'          => $this->organizationTypeBreakdown,
                'employmentArea'            => $this->employmentAreaBreakdown,
                'monthsToFirstJob'          => $this->monthsToFirstJobBreakdown,
                'gender'                    => $this->genderBreakdown,
                'civilStatus'               => $this->civilStatusBreakdown,
                'furtherStudies'            => $this->furtherStudiesLevelBreakdown,
                'topEmployers'              => $this->topEmployers,
                'jobAlignment'              => $this->jobAlignmentBreakdown,
                'boardExam'                 => $this->boardExamBreakdown,
                'alumniByRegion'            => $this->alumniByRegion,
            ]) !!}
        </script>
    @endunless
</div>

@unless ($this->hasNoDepartment)
    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endassets

    @script
    <script>
        // ==== Helpers ====
        const pretty = (label) => {
            const str = String(label).toLowerCase();
            if (str === 'not-yet-employed') return 'Not yet Employed';
            if (str === 'more-than-6-months') return 'More than 6 Months';
            if (str === 'more-than-1-year') return 'More than 1 Year';
            if (str === 'single-parent') return 'Single Parent';
            if (str === 'partially-related') return 'Partially Related';
            const rangeMatch = str.match(/^(\d+)-(\d+)-months$/);
            if (rangeMatch) return `${rangeMatch[1]} - ${rangeMatch[2]} Months`;
            return str.replace(/-/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
        };

        const waitForChart = () => new Promise((resolve) => {
            if (window.Chart) return resolve(window.Chart);
            const t = setInterval(() => {
                if (window.Chart) { clearInterval(t); resolve(window.Chart); }
            }, 40);
            setTimeout(() => { clearInterval(t); resolve(window.Chart || null); }, 4000);
        });

        const getPayload = () => {
            try {
                const el = document.getElementById('analytics-payload');
                return el ? JSON.parse(el.textContent) : {};
            } catch (e) { console.warn('Payload parse failed', e); return {}; }
        };

        const destroyChart = (id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const existing = window.Chart.getChart(el);
            if (existing) existing.destroy();
        };

        const makeChart = (ChartLib, id, config) => {
            const el = document.getElementById(id);
            if (!el || !ChartLib) return;
            destroyChart(id);
            new ChartLib(el, config);
        };

        // ==== THE FIX: integer-only ticks ====
        const integerTicks = {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
                precision: 0,
                autoSkip: false,
                callback: (v) => Number.isInteger(v) ? v : ''
            },
            afterBuildTicks: (axis) => {
                axis.ticks = axis.ticks.filter(t => Number.isInteger(t.value));
            },
            grid: { color: '#f1f1f1' }
        };

        const pieTooltip = {
            callbacks: {
                label: (ctx) => {
                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) + '%' : '0%';
                    return `${ctx.label}: ${ctx.raw} (${pct})`;
                }
            }
        };

        const pieLabelPlugin = {
            id: 'piePercentLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                const dataset = chart.data.datasets[0];
                const total = dataset.data.reduce((a, b) => a + b, 0);
                if (!total) return;
                chart.getDatasetMeta(0).data.forEach((arc, i) => {
                    const value = dataset.data[i];
                    if (!value) return;
                    const pct = ((value / total) * 100).toFixed(1) + '%';
                    const pos = arc.tooltipPosition();
                    ctx.save();
                    ctx.fillStyle = '#000';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(pct, pos.x, pos.y);
                    ctx.restore();
                });
            }
        };

        const piePalette = ['#16a34a', '#D4A537', '#3b82f6', '#94a3b8', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'];

        const initCharts = async () => {
            const ChartLib = await waitForChart();
            if (!ChartLib) return;

            const p = getPayload();

            // ===== Alumni by Department =====
            const deptData = p.alumniByDept || {};
            const deptCodes = Object.keys(deptData);
            if (deptCodes.length > 0) {
                makeChart(ChartLib, 'alumniDynamicChart', {
                    type: 'bar',
                    data: {
                        labels: deptCodes,
                        datasets: [{
                            data: deptCodes.map(c => deptData[c].total),
                            backgroundColor: '#16a34a',
                            borderRadius: 6,
                            maxBarThickness: 42
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: (items) => deptData[deptCodes[items[0]?.dataIndex]]?.name || '',
                                    label: (ctx) => ctx.raw
                                }
                            }
                        },
                        scales: { y: integerTicks, x: { grid: { display: false } } }
                    }
                });
            }

            // ===== Comparative Analysis =====
            const ca = p.courseAnalytics || [];
            if (ca.length > 0) {
                makeChart(ChartLib, 'comparativeChart', {
                    type: 'bar',
                    data: {
                        labels: ca.map(i => i.course_code),
                        datasets: [
                            { label: 'Aligned', data: ca.map(i => i.related_rate), backgroundColor: '#16a34a', borderRadius: 6, maxBarThickness: 28 },
                            { label: 'Not Aligned', data: ca.map(i => 100 - i.related_rate), backgroundColor: '#D4A537', borderRadius: 6, maxBarThickness: 28 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, max: 110, grid: { color: '#f1f1f1' }, ticks: { stepSize: 25, callback: v => v > 100 ? '' : v + '%' } },
                            x: { grid: { display: false } }
                        },
                        plugins: {
                            legend: { position: 'top', align: 'end' },
                            tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.formattedValue}%` } }
                        }
                    },
                    plugins: [{
                        id: 'barPercentLabels',
                        afterDatasetsDraw(chart) {
                            const { ctx } = chart;
                            ctx.save();
                            ctx.font = 'bold 11px sans-serif';
                            ctx.fillStyle = '#0f2b1c';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            chart.data.datasets.forEach((dataset, di) => {
                                chart.getDatasetMeta(di).data.forEach((bar, i) => {
                                    const value = dataset.data[i];
                                    if (value == null) return;
                                    ctx.fillText(value + '%', bar.x, bar.y - 4);
                                });
                            });
                            ctx.restore();
                        }
                    }]
                });
            }

            // ===== Alumni by Batch =====
            const bd = p.alumniByBatch || {};
            const bIds = Object.keys(bd);
            if (bIds.length > 0) {
                makeChart(ChartLib, 'alumniByBatchChart', {
                    type: 'bar',
                    data: {
                        labels: bIds.map(id => bd[id].batch_name),
                        datasets: [{ data: bIds.map(id => bd[id].total), backgroundColor: '#D4A537', borderRadius: 6, maxBarThickness: 50 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: ctx => `${ctx.raw} graduate${ctx.raw === 1 ? '' : 's'}` } }
                        },
                        scales: { y: integerTicks, x: { grid: { display: false } } }
                    }
                });
            }

            // ===== Pies (status, area, gender, civil, job alignment, board exam) =====
            const pieConfig = (id, dataMap, colors) => {
                const keys = Object.keys(dataMap || {});
                if (keys.length === 0) return;
                makeChart(ChartLib, id, {
                    type: 'pie',
                    data: {
                        labels: keys.map(pretty),
                        datasets: [{ data: keys.map(k => dataMap[k]), backgroundColor: colors || piePalette, borderWidth: 2, borderColor: '#fff' }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' }, tooltip: pieTooltip }
                    },
                    plugins: [pieLabelPlugin]
                });
            };

            pieConfig('employmentStatusChart', p.employmentStatus);
            pieConfig('employmentAreaChart', p.employmentArea, ['#16a34a', '#3b82f6']);
            pieConfig('genderChart', p.gender, ['#3b82f6', '#ec4899']);
            pieConfig('civilStatusChart', p.civilStatus);
            pieConfig('jobAlignmentChart', p.jobAlignment, ['#16a34a', '#D4A537', '#ef4444']);
            pieConfig('boardExamChart', p.boardExam, ['#16a34a', '#ef4444']);

            // ===== Bars (integer ticks) =====
            const barConfig = (id, dataMap, color, horizontal = false) => {
                const keys = Object.keys(dataMap || {});
                if (keys.length === 0) return;
                makeChart(ChartLib, id, {
                    type: 'bar',
                    data: {
                        labels: keys.map(pretty),
                        datasets: [{ data: keys.map(k => dataMap[k]), backgroundColor: color, borderRadius: 6, maxBarThickness: 45 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        indexAxis: horizontal ? 'y' : 'x',
                        plugins: { legend: { display: false } },
                        scales: horizontal
                            ? { x: integerTicks, y: { grid: { display: false } } }
                            : { y: integerTicks, x: { grid: { display: false } } }
                    }
                });
            };

            barConfig('employmentTypeChart', p.employmentType, '#16a34a');
            barConfig('organizationTypeChart', p.organizationType, '#D4A537', true);
            barConfig('monthsToFirstJobChart', p.monthsToFirstJob, '#3b82f6');
            barConfig('furtherStudiesChart', p.furtherStudies, '#8b5cf6');
            barConfig('topEmployersChart', p.topEmployers, '#0f2b1c', true);
            barConfig('alumniByRegionChart', p.alumniByRegion, '#16a34a', true);
        };

        initCharts();

        $wire.on('batch-changed', () => requestAnimationFrame(() => initCharts()));
        $wire.on('analytics-refreshed', () => requestAnimationFrame(() => initCharts()));
    </script>
    @endscript
@endunless