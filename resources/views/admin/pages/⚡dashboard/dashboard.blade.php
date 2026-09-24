<div>
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white" style="font-family: 'Fraunces', serif;">
                        Overview
                    </h1>
                    <p class="text-sm text-black/50 dark:text-white/50 mt-0.5">
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
                        <label for="batch-filter" class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">Batch</label>
                        <div class="relative">
                            <select id="batch-filter" wire:model.live.debounce.500ms="selectedBatchId"
                                wire:loading.attr="disabled" wire:target="selectedBatchId"
                                class="px-3 py-2 pr-9 text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition min-w-[150px] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
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
                <div class="rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 p-6 sm:p-8 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-500/15 flex items-center justify-center text-amber-700 dark:text-amber-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-amber-900 dark:text-amber-300" style="font-family: 'Fraunces', serif;">No department assigned yet</h2>
                    <p class="text-sm text-amber-800/80 dark:text-amber-200/70 mt-2 max-w-md mx-auto">
                        You don't have access to any alumni data until the registrar assigns you to a department.
                    </p>
                </div>
            @else
                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedBatchId"
                    class="transition-opacity duration-200 space-y-6">

                    {{-- Stat cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                        <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Board Passers</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->boardPassers }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Courses</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->courses }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#D4A537]/15 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#a97f1f] dark:text-[#E5B94A] shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Total Alumni</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->alumni }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="relative flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-500/15 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Pending Verifications</p>
                                    <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->pendingVerification }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main charts --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                        {{-- Alumni Graduates (drill-down aware) --}}
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0">
                                    <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white">Alumni Graduates</h2>
                                    <p id="dept-subtitle" class="text-xs text-black/40 dark:text-white/40 mt-0.5">
                                        @if ($this->isRegistrar)
                                            Breakdown by department · click a bar to see courses
                                        @else
                                            Breakdown by course
                                        @endif
                                    </p>
                                </div>
                                <button type="button" id="dept-back-btn"
                                    class="hidden shrink-0 inline-flex items-center gap-1 text-[11px] font-semibold text-[#1877F2] hover:underline">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                    </svg>
                                    Back to departments
                                </button>
                            </div>
                            @if (empty($this->alumniByDept))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No alumni-to-department records yet.</p>
                            @else
                                <div class="w-full h-64 sm:h-72 md:h-80"><canvas id="alumniDynamicChart"></canvas></div>
                            @endif
                        </div>

                        {{-- Comparative Analysis --}}
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white">Comparative Analysis</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mt-0.5 mb-1">Course alignment with current work</p>
                            <p class="text-xs text-black/50 dark:text-white/50 mb-3">{{ $this->furtherStudiesRate }}% pursued further studies</p>
                            @if (empty($this->courseAnalytics))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No course analytics yet.</p>
                            @else
                                <div class="w-full h-64 sm:h-72 md:h-80"><canvas id="comparativeChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    {{-- Alumni by Year (drill-down aware) --}}
                    <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white">Alumni Graduates by Year</h2>
                                <p id="batch-subtitle" class="text-xs text-black/40 dark:text-white/40 mt-0.5">
                                    Total graduates per batch · click a bar to see courses
                                </p>
                            </div>
                            <button type="button" id="batch-back-btn"
                                class="hidden shrink-0 inline-flex items-center gap-1 text-[11px] font-semibold text-[#1877F2] hover:underline">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                Back to batches
                            </button>
                        </div>
                        @if (empty($this->alumniByBatch))
                            <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No batch records yet.</p>
                        @else
                            <div class="w-full h-72"><canvas id="alumniByBatchChart"></canvas></div>
                        @endif
                    </div>

                    {{-- Analytics section --}}
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c] dark:text-white" style="font-family: 'Fraunces', serif;">Analytics</h2>
                        <p class="text-sm text-black/50 dark:text-white/50 mt-0.5">Deeper breakdown from the tracer study.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Employment Status</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Employed, unemployed, self-employed</p>
                            @if (empty($this->employmentStatusBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No tracer employment data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentStatusChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Employment Type</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Full-time, part-time, freelance, etc.</p>
                            @if (empty($this->employmentTypeBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No employment type yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentTypeChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Organization Type</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Where alumni currently work</p>
                            @if (empty($this->organizationTypeBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No organization type yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="organizationTypeChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Employment Area</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Philippines vs. abroad</p>
                            @if (empty($this->employmentAreaBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No employment area yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="employmentAreaChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Time to First Job</h2>
                        <p class="text-xs text-black/40 dark:text-white/40 mb-3">How long after graduation alumni got employed</p>
                        @if (collect($this->monthsToFirstJobBreakdown)->sum() === 0)
                            <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No time-to-first-job data yet.</p>
                        @else
                            <div class="w-full h-64"><canvas id="monthsToFirstJobChart"></canvas></div>
                        @endif
                    </div>

                    <div class="pt-2">
                        <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c] dark:text-white" style="font-family: 'Fraunces', serif;">Demographics & Outcomes</h2>
                        <p class="text-sm text-black/50 dark:text-white/50 mt-0.5">Additional insights from profiles and work history.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Gender Distribution</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Male vs. female alumni</p>
                            @if (empty($this->genderBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No gender data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="genderChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Civil Status</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Single, married, widowed, etc.</p>
                            @if (empty($this->civilStatusBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No civil status data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="civilStatusChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Further Studies Level</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Certificate, Bachelor, Master, Doctorate</p>
                            @if (empty($this->furtherStudiesLevelBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No further studies data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="furtherStudiesChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Job Alignment to Degree</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Related, partially related, not related</p>
                            @if (empty($this->jobAlignmentBreakdown))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No job alignment data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="jobAlignmentChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Top Employers</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Most common companies among alumni</p>
                            @if (empty($this->topEmployers))
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No employer data yet.</p>
                            @else
                                <div class="w-full h-72"><canvas id="topEmployersChart"></canvas></div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                            <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Board Exam Performance</h2>
                            <p class="text-xs text-black/40 dark:text-white/40 mb-3">Passed vs. failed (75%+ = passed)</p>
                            @if (collect($this->boardExamBreakdown)->sum() === 0)
                                <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No board exam data yet.</p>
                            @else
                                <div class="w-full h-64"><canvas id="boardExamChart"></canvas></div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white mb-1">Alumni by Region</h2>
                        <p class="text-xs text-black/40 dark:text-white/40 mb-3">Geographic distribution (top regions)</p>
                        @if (empty($this->alumniByRegion))
                            <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No region data yet.</p>
                        @else
                            <div class="w-full h-72"><canvas id="alumniByRegionChart"></canvas></div>
                        @endif
                    </div>

                </div>
            @endif
        </div>
    </div>

    @unless ($this->hasNoDepartment)
        <script type="application/json" id="analytics-payload">
            {!! json_encode([
                'isRegistrar'             => $this->isRegistrar,
                'alumniByDept'            => $this->alumniByDept,
                'alumniByDeptAndCourse'   => $this->alumniByDeptAndCourse,
                'alumniByBatch'           => $this->alumniByBatch,
                'alumniByBatchAndCourse'  => $this->alumniByBatchAndCourse,
                'courseAnalytics'         => $this->courseAnalytics,
                'employmentStatus'        => $this->employmentStatusBreakdown,
                'employmentType'          => $this->employmentTypeBreakdown,
                'organizationType'        => $this->organizationTypeBreakdown,
                'employmentArea'          => $this->employmentAreaBreakdown,
                'monthsToFirstJob'        => $this->monthsToFirstJobBreakdown,
                'gender'                  => $this->genderBreakdown,
                'civilStatus'             => $this->civilStatusBreakdown,
                'furtherStudies'          => $this->furtherStudiesLevelBreakdown,
                'topEmployers'            => $this->topEmployers,
                'jobAlignment'            => $this->jobAlignmentBreakdown,
                'boardExam'               => $this->boardExamBreakdown,
                'alumniByRegion'          => $this->alumniByRegion,
            ]) !!}
        </script>
    @endunless
</div>

@unless ($this->hasNoDepartment)
    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endassets

    @script
    <script>
        // =====================================================================
        // Palette
        // =====================================================================
        const PALETTE = {
            greenDark:   '#0f2b1c',
            greenMid:    '#1C6B45',
            greenBright: '#16a34a',
            greenLight:  '#10b981',
            goldDeep:    '#a97f1f',
            gold:        '#D4A537',
            goldLight:   '#E5B94A',
            goldSoft:    '#FCD34D',
        };

        const PIE_PALETTE = [
            PALETTE.greenBright, PALETTE.gold,
            PALETTE.greenMid,    PALETTE.goldLight,
            PALETTE.greenLight,  PALETTE.goldSoft,
            PALETTE.greenDark,   PALETTE.goldDeep,
        ];

        // =====================================================================
        // Drill state (client-side, per chart)
        // =====================================================================
        const drillState = {
            dept:  { view: 'departments', selected: null },
            batch: { view: 'batches',     selected: null },
        };

        // =====================================================================
        // Helpers
        // =====================================================================
        const isDark = () => document.documentElement.classList.contains('dark');

        const theme = () => ({
            mutedText:   isDark() ? 'rgba(229,231,235,0.6)' : 'rgba(55,65,81,0.6)',
            gridColor:   isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
            borderColor: isDark() ? '#242526' : '#fff',
            tooltipBg:   '#0f2b1c',
            pieLabel:    '#fff',
            fontFamily:  "'Inter', 'Segoe UI', system-ui, sans-serif",
        });

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

        const barGradient = (ctx, chartArea, baseColor) => {
            if (!chartArea) return baseColor;
            const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            g.addColorStop(0, baseColor);
            g.addColorStop(1, baseColor + 'cc');
            return g;
        };

        const makeChart = (ChartLib, id, config) => {
            const el = document.getElementById(id);
            if (!el || !ChartLib) return;
            destroyChart(id);
            new ChartLib(el, config);
        };

        const integerTicks = () => ({
            beginAtZero: true,
            ticks: {
                stepSize: 1, precision: 0, autoSkip: false,
                color: theme().mutedText,
                callback: (v) => Number.isInteger(v) ? v : '',
            },
            grid: { color: theme().gridColor, drawBorder: false },
        });

        let ChartLibRef = null;

        // =====================================================================
        // Renderers for drill-down charts
        // =====================================================================
        const renderDeptChart = (ChartLib) => {
            const p = window.__payload;
            const state = drillState.dept;
            const drillable = p.isRegistrar;   // only registrar has multiple departments

            let labels = [], totals = [], names = [];
            let clickable = false;
            let subtitleText = '';
            let backBtnVisible = false;

            if (state.view === 'departments') {
                const map = p.alumniByDept || {};
                const codes = Object.keys(map);
                labels = codes;
                names  = codes.map(c => map[c]?.name || c);
                totals = codes.map(c => map[c]?.total || 0);
                clickable = drillable;
                subtitleText = 'Breakdown by department' + (drillable ? ' · click a bar to see courses' : '');
                backBtnVisible = false;
            } else {
                // Courses view
                const map = p.alumniByDeptAndCourse || {};
                // For program head: use the only department (first key)
                // For registrar who drilled: use selected key
                const deptKey = state.selected || Object.keys(map)[0];
                const dept = map[deptKey] || { courses: {}, name: deptKey };
                const codes = Object.keys(dept.courses || {});
                labels = codes;
                names  = codes.map(c => dept.courses[c]?.name || c);
                totals = codes.map(c => dept.courses[c]?.total || 0);
                clickable = false;
                subtitleText = p.isRegistrar
                    ? `${dept.name} · breakdown by course`
                    : `Breakdown by course within ${dept.name}`;
                backBtnVisible = p.isRegistrar;   // only registrar can go back
            }

            const subEl = document.getElementById('dept-subtitle');
            if (subEl) subEl.textContent = subtitleText;

            const backBtn = document.getElementById('dept-back-btn');
            if (backBtn) {
                backBtn.classList.toggle('hidden', ! backBtnVisible);
                backBtn.classList.toggle('inline-flex', backBtnVisible);
            }

            if (labels.length === 0) return;

            const t = theme();

            makeChart(ChartLib, 'alumniDynamicChart', {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: totals,
                        backgroundColor: (ctx) => {
                            const { ctx: c, chartArea } = ctx.chart;
                            return barGradient(c, chartArea, PALETTE.greenBright);
                        },
                        hoverBackgroundColor: PALETTE.greenMid,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 48,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    onHover: (event, els) => {
                        if (! clickable) return;
                        event.native.target.style.cursor = els.length > 0 ? 'pointer' : 'default';
                    },
                    onClick: (event, els) => {
                        if (! clickable || els.length === 0) return;
                        drillState.dept.view = 'courses';
                        drillState.dept.selected = labels[els[0].index];
                        renderDeptChart(ChartLib);
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: t.tooltipBg, padding: 10, cornerRadius: 8,
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 12 },
                            displayColors: false,
                            callbacks: {
                                title: (items) => names[items[0]?.dataIndex] || labels[items[0]?.dataIndex],
                                label: (ctx) => `${ctx.raw} alumni`,
                                afterLabel: () => clickable ? 'Click to see courses' : '',
                            },
                        },
                    },
                    scales: {
                        y: integerTicks(),
                        x: {
                            ticks: { color: t.mutedText, font: { weight: '600' } },
                            grid: { display: false }, border: { display: false },
                        },
                    },
                },
            });
        };

        const renderBatchChart = (ChartLib) => {
            const p = window.__payload;
            const state = drillState.batch;
            const t = theme();

            let labels = [], totals = [], names = [];
            let clickable = false;
            let subtitleText = '';
            let backBtnVisible = false;
            let batchKey = null;

            if (state.view === 'batches') {
                const map = p.alumniByBatch || {};
                const ids = Object.keys(map);
                labels = ids.map(id => map[id].batch_name);
                names  = labels.slice();
                totals = ids.map(id => map[id].total);
                clickable = true;
                subtitleText = 'Total graduates per batch · click a bar to see courses';
                backBtnVisible = false;
            } else {
                const map = p.alumniByBatchAndCourse || {};
                batchKey = state.selected;
                const batch = map[batchKey] || { courses: {}, batch_name: batchKey };
                const codes = Object.keys(batch.courses || {});
                labels = codes;
                names  = codes.map(c => batch.courses[c]?.name || c);
                totals = codes.map(c => batch.courses[c]?.total || 0);
                clickable = false;
                subtitleText = `Batch ${batch.batch_name} · breakdown by course`;
                backBtnVisible = true;
            }

            const subEl = document.getElementById('batch-subtitle');
            if (subEl) subEl.textContent = subtitleText;

            const backBtn = document.getElementById('batch-back-btn');
            if (backBtn) {
                backBtn.classList.toggle('hidden', ! backBtnVisible);
                backBtn.classList.toggle('inline-flex', backBtnVisible);
            }

            if (labels.length === 0) return;

            makeChart(ChartLib, 'alumniByBatchChart', {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: totals,
                        backgroundColor: (ctx) => {
                            const { ctx: c, chartArea } = ctx.chart;
                            return barGradient(c, chartArea, PALETTE.gold);
                        },
                        hoverBackgroundColor: PALETTE.goldDeep,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 52,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    onHover: (event, els) => {
                        if (! clickable) return;
                        event.native.target.style.cursor = els.length > 0 ? 'pointer' : 'default';
                    },
                    onClick: (event, els) => {
                        if (! clickable || els.length === 0) return;
                        const idx = els[0].index;
                        const map = p.alumniByBatch || {};
                        const ids = Object.keys(map);
                        const found = ids.find(id => map[id].batch_name === labels[idx]);
                        if (! found) return;
                        drillState.batch.view = 'courses';
                        drillState.batch.selected = found;
                        renderBatchChart(ChartLib);
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: t.tooltipBg, padding: 10, cornerRadius: 8,
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 12 },
                            displayColors: false,
                            callbacks: {
                                title: (items) => names[items[0]?.dataIndex] || labels[items[0]?.dataIndex],
                                label: (ctx) => `${ctx.raw} graduate${ctx.raw === 1 ? '' : 's'}`,
                                afterLabel: () => clickable ? 'Click to see courses' : '',
                            },
                        },
                    },
                    scales: {
                        y: integerTicks(),
                        x: {
                            ticks: { color: t.mutedText, font: { weight: '600' } },
                            grid: { display: false }, border: { display: false },
                        },
                    },
                },
            });
        };

        // =====================================================================
        // Back-button handlers
        // =====================================================================
        if (! window.__adminDeptBack) {
            window.__adminDeptBack = true;
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#dept-back-btn');
                if (! btn || ! ChartLibRef) return;
                drillState.dept.view = 'departments';
                drillState.dept.selected = null;
                renderDeptChart(ChartLibRef);
            });
        }

        if (! window.__adminBatchBack) {
            window.__adminBatchBack = true;
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#batch-back-btn');
                if (! btn || ! ChartLibRef) return;
                drillState.batch.view = 'batches';
                drillState.batch.selected = null;
                renderBatchChart(ChartLibRef);
            });
        }

        // =====================================================================
        // Init
        // =====================================================================
        const initCharts = async () => {
            const ChartLib = await waitForChart();
            if (!ChartLib) return;
            ChartLibRef = ChartLib;

            const p = getPayload();
            window.__payload = p;

            const t = theme();

            ChartLib.defaults.font.family = t.fontFamily;
            ChartLib.defaults.font.size   = 11;
            ChartLib.defaults.animation.duration = 500;
            ChartLib.defaults.animation.easing   = 'easeOutQuart';

            // Reset drill state on every init
            // Program head → start at 'courses' (skip department level)
            // Registrar   → start at 'departments'
            drillState.dept  = p.isRegistrar
                ? { view: 'departments', selected: null }
                : { view: 'courses',     selected: null };
            drillState.batch = { view: 'batches', selected: null };

            // 1. Alumni by Dept / Courses (drill-down aware)
            renderDeptChart(ChartLib);

            // 2. Comparative Analysis
            const ca = p.courseAnalytics || [];
            if (ca.length > 0) {
                makeChart(ChartLib, 'comparativeChart', {
                    type: 'bar',
                    data: {
                        labels: ca.map(i => i.course_code),
                        datasets: [
                            {
                                label: 'Aligned',
                                data: ca.map(i => i.related_rate),
                                backgroundColor: PALETTE.greenBright,
                                hoverBackgroundColor: PALETTE.greenMid,
                                borderRadius: 6, borderSkipped: false, maxBarThickness: 28,
                            },
                            {
                                label: 'Not Aligned',
                                data: ca.map(i => 100 - i.related_rate),
                                backgroundColor: PALETTE.gold,
                                hoverBackgroundColor: PALETTE.goldDeep,
                                borderRadius: 6, borderSkipped: false, maxBarThickness: 28,
                            },
                        ],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: { top: 16 } },
                        scales: {
                            y: {
                                beginAtZero: true, max: 110,
                                grid: { color: t.gridColor, drawBorder: false },
                                border: { display: false },
                                ticks: { stepSize: 25, color: t.mutedText, callback: v => v > 100 ? '' : v + '%' },
                            },
                            x: {
                                ticks: { color: t.mutedText, font: { weight: '600' } },
                                grid: { display: false }, border: { display: false },
                            },
                        },
                        plugins: {
                            legend: {
                                position: 'top', align: 'end',
                                labels: {
                                    color: t.mutedText, boxWidth: 10, boxHeight: 10,
                                    usePointStyle: true, pointStyle: 'rectRounded',
                                    padding: 14, font: { size: 11, weight: '600' },
                                },
                            },
                            tooltip: {
                                backgroundColor: t.tooltipBg, padding: 10, cornerRadius: 8,
                                callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.formattedValue}%` },
                            },
                        },
                    },
                    plugins: [{
                        id: 'barPercentLabels',
                        afterDatasetsDraw(chart) {
                            const { ctx } = chart;
                            ctx.save();
                            ctx.font = '600 10px ' + t.fontFamily;
                            ctx.fillStyle = t.mutedText;
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            chart.data.datasets.forEach((dataset, di) => {
                                chart.getDatasetMeta(di).data.forEach((bar, i) => {
                                    const v = dataset.data[i];
                                    if (v == null) return;
                                    ctx.fillText(v + '%', bar.x, bar.y - 4);
                                });
                            });
                            ctx.restore();
                        },
                    }],
                });
            }

            // 3. Alumni by Batch (drill-down aware)
            renderBatchChart(ChartLib);

            // ===== Pie helpers =====
            const pieLabelPlugin = {
                id: 'piePercentLabels',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    const dataset = chart.data.datasets[0];
                    const total = dataset.data.reduce((a, b) => a + b, 0);
                    if (!total) return;
                    chart.getDatasetMeta(0).data.forEach((arc, i) => {
                        const v = dataset.data[i];
                        if (!v) return;
                        const pct = ((v / total) * 100).toFixed(0) + '%';
                        const pos = arc.tooltipPosition();
                        ctx.save();
                        ctx.fillStyle = t.pieLabel;
                        ctx.font = '700 12px ' + t.fontFamily;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(pct, pos.x, pos.y);
                        ctx.restore();
                    });
                },
            };

            const pieTooltip = {
                backgroundColor: t.tooltipBg, padding: 10, cornerRadius: 8,
                callbacks: {
                    label: (ctx) => {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) + '%' : '0%';
                        return ` ${ctx.raw} (${pct})`;
                    },
                },
            };

            const pieLegend = {
                position: 'bottom',
                labels: {
                    color: t.mutedText, boxWidth: 10, boxHeight: 10,
                    usePointStyle: true, pointStyle: 'circle',
                    padding: 12, font: { size: 11, weight: '600' },
                },
            };

            const pieConfig = (id, dataMap, colors) => {
                const keys = Object.keys(dataMap || {});
                if (keys.length === 0) return;
                makeChart(ChartLib, id, {
                    type: 'doughnut',
                    data: {
                        labels: keys.map(pretty),
                        datasets: [{
                            data: keys.map(k => dataMap[k]),
                            backgroundColor: colors || PIE_PALETTE,
                            borderWidth: 3,
                            borderColor: t.borderColor,
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: { legend: pieLegend, tooltip: pieTooltip },
                    },
                    plugins: [pieLabelPlugin],
                });
            };

            pieConfig('employmentStatusChart', p.employmentStatus);
            pieConfig('employmentAreaChart', p.employmentArea, [PALETTE.greenBright, PALETTE.gold]);
            pieConfig('genderChart', p.gender, [PALETTE.greenMid, PALETTE.gold]);
            pieConfig('civilStatusChart', p.civilStatus);
            pieConfig('jobAlignmentChart', p.jobAlignment, [PALETTE.greenBright, '#ef4444', PALETTE.gold ]);
            pieConfig('boardExamChart', p.boardExam, [PALETTE.greenBright, '#ef4444']);

            // ===== Bar configs =====
            const barConfig = (id, dataMap, color, horizontal = false) => {
                const keys = Object.keys(dataMap || {});
                if (keys.length === 0) return;
                makeChart(ChartLib, id, {
                    type: 'bar',
                    data: {
                        labels: keys.map(pretty),
                        datasets: [{
                            data: keys.map(k => dataMap[k]),
                            backgroundColor: color,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 45,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        indexAxis: horizontal ? 'y' : 'x',
                        plugins: { legend: { display: false } },
                        scales: horizontal
                            ? { x: integerTicks(), y: { grid: { display: false }, ticks: { color: t.mutedText, font: { weight: '600' } } } }
                            : { y: integerTicks(), x: { grid: { display: false }, ticks: { color: t.mutedText, font: { weight: '600' } } } },
                    },
                });
            };

            barConfig('employmentTypeChart', p.employmentType, PALETTE.greenMid);
            barConfig('organizationTypeChart', p.organizationType, PALETTE.gold, true);
            barConfig('monthsToFirstJobChart', p.monthsToFirstJob, PALETTE.greenBright);
            barConfig('furtherStudiesChart', p.furtherStudies, PALETTE.goldLight);
            barConfig('topEmployersChart', p.topEmployers, PALETTE.goldDeep, true);
            barConfig('alumniByRegionChart', p.alumniByRegion, PALETTE.greenMid, true);
        };

        initCharts();

        if (! window.__adminBatchChanged) {
            window.__adminBatchChanged = true;
            $wire.on('batch-changed', () => requestAnimationFrame(() => initCharts()));
            $wire.on('analytics-refreshed', () => requestAnimationFrame(() => initCharts()));
        }

        if (! window.__adminThemeObserver) {
            window.__adminThemeObserver = new MutationObserver(() => requestAnimationFrame(() => initCharts()));
            window.__adminThemeObserver.observe(document.documentElement, {
                attributes: true, attributeFilter: ['class'],
            });
        }
    </script>
    @endscript
@endunless