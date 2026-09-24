<div>
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8 bg-[#F8FAFC] dark:bg-[#18191A]">
        <div class="space-y-6">

            {{-- Header with batch selector --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white"
                        style="font-family: 'Fraunces', serif;">
                        Overview
                    </h1>
                    <p class="text-sm text-black/50 dark:text-white/50 mt-0.5">
                        @if ($selectedBatchId)
                            Showing analytics for Batch
                            {{ collect($this->batches)->firstWhere('id', (int) $selectedBatchId)['batch_name'] ?? '—' }}.
                        @else
                            A snapshot of your alumni network across all batches.
                        @endif
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    <label for="batch-filter"
                        class="text-[11px] font-bold text-black/50 dark:text-white/50 uppercase tracking-wide whitespace-nowrap">
                        Batch
                    </label>

                    <div class="relative">
                        <select id="batch-filter" wire:model.live.debounce.500ms="selectedBatchId"
                            wire:loading.attr="disabled" wire:target="selectedBatchId"
                            class="px-3 py-2 pr-9 text-sm rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#3A3B3C] text-[#0f2b1c] dark:text-white font-semibold
                                   focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                                   min-w-[150px] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
                            <option value="">Overall</option>
                            @foreach ($this->batches as $batch)
                                <option value="{{ $batch['id'] }}">{{ $batch['batch_name'] }}</option>
                            @endforeach
                        </select>

                        <div wire:loading wire:target="selectedBatchId"
                            class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 animate-spin text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loading wrapper --}}
            <div wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedBatchId"
                class="transition-opacity duration-200 space-y-6">

                {{-- Top stat cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                    <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="relative flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Total Users</p>
                                <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->users }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="relative flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Total Courses</p>
                                <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->courses }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="relative flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] dark:text-[#D4A537] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Total Alumni</p>
                                <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->alumni }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="relative flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#1C6B45]/10 dark:bg-[#1C6B45]/20 flex items-center justify-center text-[#1C6B45] dark:text-emerald-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 dark:text-white/50 font-semibold truncate">Program Heads</p>
                                <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] dark:text-white mt-0.5">{{ $this->programHeads }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- ===== Alumni by Department / Courses (DRILLABLE) ===== --}}
                    <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white">Alumni Graduates</h2>
                                <p id="dept-subtitle" class="text-xs text-black/40 dark:text-white/40 mt-0.5">
                                    Breakdown by department · click a bar to see courses
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

                    {{-- ===== Comparative Analysis (DRILLABLE) ===== --}}
                    <div class="bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm rounded-2xl p-4 md:p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <h2 class="text-sm font-bold text-[#0f2b1c] dark:text-white">Comparative Analysis</h2>
                                <p id="comparative-subtitle" class="text-xs text-black/40 dark:text-white/40 mt-0.5">
                                    Course alignment with work · by department · click a bar to see courses
                                </p>
                                <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">
                                    {{ $this->furtherStudiesRate }}% pursued further studies
                                </p>
                            </div>
                            <button type="button" id="comparative-back-btn"
                                class="hidden shrink-0 inline-flex items-center gap-1 text-[11px] font-semibold text-[#1877F2] hover:underline">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                Back to departments
                            </button>
                        </div>
                        @if (empty($this->courseAnalyticsByDept))
                            <p class="text-sm text-black/40 dark:text-white/40 py-16 text-center">No course analytics yet.</p>
                        @else
                            <div class="w-full h-64 sm:h-72 md:h-80"><canvas id="comparativeChart"></canvas></div>
                        @endif
                    </div>
                </div>

                {{-- ===== Alumni by Year (DRILLABLE) ===== --}}
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

                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c] dark:text-white" style="font-family: 'Fraunces', serif;">
                        Analytics
                    </h2>
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
            </div>
        </div>
    </div>

    {{-- Data payload for charts --}}
    <script type="application/json" id="analytics-payload">
        {!! json_encode([
            'alumniByDept'            => $this->alumniByDept,
            'alumniByDeptAndCourse'   => $this->alumniByDeptAndCourse,
            'alumniByBatch'           => $this->alumniByBatch,
            'alumniByBatchAndCourse'  => $this->alumniByBatchAndCourse,
            'courseAnalytics'         => $this->courseAnalytics,
            'courseAnalyticsByDept'   => $this->courseAnalyticsByDept,
            'employmentStatus'        => $this->employmentStatusBreakdown,
            'employmentType'          => $this->employmentTypeBreakdown,
            'organizationType'        => $this->organizationTypeBreakdown,
            'employmentArea'          => $this->employmentAreaBreakdown,
            'monthsToFirstJob'        => $this->monthsToFirstJobBreakdown,
        ]) !!}
    </script>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endassets

@script
    <script>
        // =====================================================================
        // Brand palette
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
        // Drill-down state (per chart, client-side only)
        // =====================================================================
        const drillState = {
            dept:        { view: 'departments', selected: null },
            batch:       { view: 'batches',     selected: null },
            comparative: { view: 'departments', selected: null },
        };

        // =====================================================================
        // Helpers
        // =====================================================================
        const isDark = () => document.documentElement.classList.contains('dark');

        const chartTheme = () => ({
            textColor:   isDark() ? '#e5e7eb' : '#374151',
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
            const rangeMatch = str.match(/^(\d+)-(\d+)-months$/);
            if (rangeMatch) return `${rangeMatch[1]} – ${rangeMatch[2]} Months`;
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
            } catch (e) {
                console.warn('Analytics payload parse failed', e);
                return {};
            }
        };

        const destroyChart = (id) => {
            const el = document.getElementById(id);
            if (!el || !window.Chart) return;
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

        let ChartLibRef = null;

        // =====================================================================
        // Init
        // =====================================================================
        const initCharts = async () => {
            const ChartLib = await waitForChart();
            if (!ChartLib) return;
            ChartLibRef = ChartLib;

            const payload = getPayload();
            const theme   = chartTheme();

            ChartLib.defaults.font.family = theme.fontFamily;
            ChartLib.defaults.font.size   = 11;
            ChartLib.defaults.animation.duration = 500;
            ChartLib.defaults.animation.easing   = 'easeOutQuart';

            const alumniByDept        = payload.alumniByDept          || {};
            const alumniByDeptCourse  = payload.alumniByDeptAndCourse || {};
            const alumniByBatch       = payload.alumniByBatch         || {};
            const alumniByBatchCourse = payload.alumniByBatchAndCourse|| {};
            const analyticsByDept     = payload.courseAnalyticsByDept || {};
            const statusData          = payload.employmentStatus      || {};
            const typeData            = payload.employmentType        || {};
            const orgData             = payload.organizationType      || {};
            const areaData            = payload.employmentArea        || {};
            const monthsData          = payload.monthsToFirstJob      || {};

            window.__analyticsData = {
                alumniByDept, alumniByDeptCourse,
                alumniByBatch, alumniByBatchCourse,
                analyticsByDept,
                theme,
            };

            // 1. Alumni by Department (drillable)
            renderDeptChart(ChartLib);

            // 2. Comparative Analysis (drillable)
            renderComparativeChart(ChartLib);

            // 3. Alumni by Year (drillable)
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
                        const value = dataset.data[i];
                        if (!value) return;
                        const pct = ((value / total) * 100).toFixed(0) + '%';
                        const pos = arc.tooltipPosition();
                        ctx.save();
                        ctx.fillStyle = theme.pieLabel;
                        ctx.font = '700 12px ' + theme.fontFamily;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(pct, pos.x, pos.y);
                        ctx.restore();
                    });
                },
            };

            const pieTooltip = {
                backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8,
                titleFont: { size: 12, weight: '600' },
                bodyFont:  { size: 12 },
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
                    color: theme.mutedText, boxWidth: 10, boxHeight: 10,
                    usePointStyle: true, pointStyle: 'circle',
                    padding: 12, font: { size: 11, weight: '600' },
                },
            };

            // 4. Employment Status
            if (Object.keys(statusData).length > 0) {
                makeChart(ChartLib, 'employmentStatusChart', {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData).map(pretty),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: PIE_PALETTE,
                            borderWidth: 3, borderColor: theme.borderColor, hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: { legend: pieLegend, tooltip: pieTooltip },
                    },
                    plugins: [pieLabelPlugin],
                });
            }

            // 5. Employment Type
            if (Object.keys(typeData).length > 0) {
                makeChart(ChartLib, 'employmentTypeChart', {
                    type: 'bar',
                    data: {
                        labels: Object.keys(typeData).map(pretty),
                        datasets: [{
                            data: Object.values(typeData),
                            backgroundColor: (ctx) => {
                                const { ctx: c, chartArea } = ctx.chart;
                                return barGradient(c, chartArea, PALETTE.greenMid);
                            },
                            hoverBackgroundColor: PALETTE.greenDark,
                            borderRadius: 8, borderSkipped: false, maxBarThickness: 44,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8, displayColors: false },
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: theme.gridColor, drawBorder: false }, border: { display: false }, ticks: { stepSize: 1, color: theme.mutedText, callback: (v) => Number.isInteger(v) ? v : null } },
                            x: { ticks: { color: theme.mutedText, font: { weight: '600' } }, grid: { display: false }, border: { display: false } },
                        },
                    },
                });
            }

            // 6. Organization Type
            if (Object.keys(orgData).length > 0) {
                makeChart(ChartLib, 'organizationTypeChart', {
                    type: 'bar',
                    data: {
                        labels: Object.keys(orgData).map(pretty),
                        datasets: [{
                            data: Object.values(orgData),
                            backgroundColor: (ctx) => {
                                const { ctx: c, chartArea } = ctx.chart;
                                if (!chartArea) return PALETTE.gold;
                                const g = c.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                                g.addColorStop(0, PALETTE.gold);
                                g.addColorStop(1, PALETTE.goldLight);
                                return g;
                            },
                            hoverBackgroundColor: PALETTE.goldDeep,
                            borderRadius: 8, borderSkipped: false, maxBarThickness: 32,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8, displayColors: false },
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { color: theme.gridColor, drawBorder: false }, border: { display: false }, ticks: { stepSize: 1, color: theme.mutedText, callback: (v) => Number.isInteger(v) ? v : null } },
                            y: { ticks: { color: theme.mutedText, font: { weight: '600' } }, grid: { display: false }, border: { display: false } },
                        },
                    },
                });
            }

            // 7. Employment Area
            if (Object.keys(areaData).length > 0) {
                makeChart(ChartLib, 'employmentAreaChart', {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(areaData).map(pretty),
                        datasets: [{
                            data: Object.values(areaData),
                            backgroundColor: [PALETTE.greenBright, PALETTE.gold],
                            borderWidth: 3, borderColor: theme.borderColor, hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: { legend: pieLegend, tooltip: pieTooltip },
                    },
                    plugins: [pieLabelPlugin],
                });
            }

            // 8. Months to First Job
            if (Object.keys(monthsData).length > 0) {
                makeChart(ChartLib, 'monthsToFirstJobChart', {
                    type: 'bar',
                    data: {
                        labels: Object.keys(monthsData).map(pretty),
                        datasets: [{
                            data: Object.values(monthsData),
                            backgroundColor: (ctx) => {
                                const { ctx: c, chartArea } = ctx.chart;
                                return barGradient(c, chartArea, PALETTE.greenBright);
                            },
                            hoverBackgroundColor: PALETTE.greenMid,
                            borderRadius: 8, borderSkipped: false, maxBarThickness: 48,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8, displayColors: false },
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: theme.gridColor, drawBorder: false }, border: { display: false }, ticks: { stepSize: 1, color: theme.mutedText, callback: (v) => Number.isInteger(v) ? v : null } },
                            x: { ticks: { color: theme.mutedText, font: { weight: '600' } }, grid: { display: false }, border: { display: false } },
                        },
                    },
                });
            }
        };

        // =====================================================================
        // Dept chart renderer
        // =====================================================================
        const renderDeptChart = (ChartLib) => {
            const data   = window.__analyticsData;
            const theme  = data.theme;
            const state  = drillState.dept;

            let labels = [], totals = [], names = [];
            let clickable = false, subtitleText = '', backBtnVisible = false;

            if (state.view === 'departments') {
                const codes = Object.keys(data.alumniByDept);
                labels  = codes;
                names   = codes.map(c => data.alumniByDept[c]?.name || c);
                totals  = codes.map(c => data.alumniByDept[c]?.total || 0);
                clickable = true;
                subtitleText = 'Breakdown by department · click a bar to see courses';
                backBtnVisible = false;
            } else {
                const dept = data.alumniByDeptCourse[state.selected] || { courses: {}, name: state.selected };
                const codes = Object.keys(dept.courses || {});
                labels  = codes;
                names   = codes.map(c => dept.courses[c]?.name || c);
                totals  = codes.map(c => dept.courses[c]?.total || 0);
                clickable = false;
                subtitleText = `${dept.name} · breakdown by course`;
                backBtnVisible = true;
            }

            const subEl = document.getElementById('dept-subtitle');
            if (subEl) subEl.textContent = subtitleText;

            const backBtn = document.getElementById('dept-back-btn');
            if (backBtn) {
                backBtn.classList.toggle('hidden', ! backBtnVisible);
                backBtn.classList.toggle('inline-flex', backBtnVisible);
            }

            if (labels.length === 0) return;

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
                        borderRadius: 8, borderSkipped: false, maxBarThickness: 48,
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
                        const idx  = els[0].index;
                        const code = labels[idx];
                        drillState.dept.view = 'courses';
                        drillState.dept.selected = code;
                        renderDeptChart(ChartLib);
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8,
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
                        y: { beginAtZero: true, grid: { color: theme.gridColor, drawBorder: false }, border: { display: false }, ticks: { stepSize: 1, precision: 0, color: theme.mutedText, callback: (v) => Number.isInteger(v) ? v : null } },
                        x: { ticks: { color: theme.mutedText, font: { weight: '600' } }, grid: { display: false }, border: { display: false } },
                    },
                },
            });
        };

        // =====================================================================
        // Comparative chart renderer (DRILLABLE)
        // =====================================================================
        const renderComparativeChart = (ChartLib) => {
            const data   = window.__analyticsData;
            const theme  = data.theme;
            const state  = drillState.comparative;

            let labels      = [];
            let relatedData = [];
            let notAligned  = [];
            let names       = [];
            let clickable   = false;
            let subtitleText = '';
            let backBtnVisible = false;

            if (state.view === 'departments') {
                const codes = Object.keys(data.analyticsByDept || {});
                labels      = codes;
                names       = codes.map(c => data.analyticsByDept[c]?.name || c);
                relatedData = codes.map(c => data.analyticsByDept[c]?.related_rate || 0);
                notAligned  = relatedData.map(r => 100 - r);
                clickable   = true;
                subtitleText = 'Course alignment with work · by department · click a bar to see courses';
                backBtnVisible = false;
            } else {
                const dept = data.analyticsByDept?.[state.selected] || { courses: {}, name: state.selected };
                const codes = Object.keys(dept.courses || {});
                labels      = codes;
                names       = codes.map(c => dept.courses[c]?.name || c);
                relatedData = codes.map(c => dept.courses[c]?.related_rate || 0);
                notAligned  = relatedData.map(r => 100 - r);
                clickable   = false;
                subtitleText = `${dept.name} · course alignment`;
                backBtnVisible = true;
            }

            const subEl = document.getElementById('comparative-subtitle');
            if (subEl) subEl.textContent = subtitleText;

            const backBtn = document.getElementById('comparative-back-btn');
            if (backBtn) {
                backBtn.classList.toggle('hidden', ! backBtnVisible);
                backBtn.classList.toggle('inline-flex', backBtnVisible);
            }

            if (labels.length === 0) return;

            makeChart(ChartLib, 'comparativeChart', {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Aligned with Work',
                            data: relatedData,
                            backgroundColor: PALETTE.greenBright,
                            hoverBackgroundColor: PALETTE.greenMid,
                            borderRadius: 6, borderSkipped: false, maxBarThickness: 28,
                        },
                        {
                            label: 'Not Aligned',
                            data: notAligned,
                            backgroundColor: PALETTE.gold,
                            hoverBackgroundColor: PALETTE.goldDeep,
                            borderRadius: 6, borderSkipped: false, maxBarThickness: 28,
                        },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    layout: { padding: { top: 16 } },
                    onHover: (event, els) => {
                        if (! clickable) return;
                        event.native.target.style.cursor = els.length > 0 ? 'pointer' : 'default';
                    },
                    onClick: (event, els) => {
                        if (! clickable || els.length === 0) return;
                        const idx  = els[0].index;
                        const code = labels[idx];
                        drillState.comparative.view = 'courses';
                        drillState.comparative.selected = code;
                        renderComparativeChart(ChartLib);
                    },
                    scales: {
                        y: {
                            beginAtZero: true, max: 110,
                            grid: { color: theme.gridColor, drawBorder: false },
                            border: { display: false },
                            ticks: { stepSize: 25, color: theme.mutedText, callback: (v) => v > 100 ? '' : v + '%' },
                        },
                        x: {
                            ticks: { color: theme.mutedText, font: { weight: '600' } },
                            grid: { display: false }, border: { display: false },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'top', align: 'end',
                            labels: {
                                color: theme.mutedText, boxWidth: 10, boxHeight: 10,
                                usePointStyle: true, pointStyle: 'rectRounded',
                                padding: 14, font: { size: 11, weight: '600' },
                            },
                        },
                        tooltip: {
                            backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8,
                            titleFont: { size: 12, weight: '600' },
                            bodyFont:  { size: 12 },
                            callbacks: {
                                title: (items) => names[items[0]?.dataIndex] || labels[items[0]?.dataIndex],
                                label: (ctx) => ` ${ctx.dataset.label}: ${ctx.formattedValue}%`,
                                afterLabel: (ctx) => {
                                    if (! clickable) return '';
                                    return ctx.datasetIndex === 1 ? 'Click to see courses' : '';
                                },
                            },
                        },
                    },
                },
                plugins: [{
                    id: 'barPercentLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        ctx.save();
                        ctx.font = '600 10px ' + theme.fontFamily;
                        ctx.fillStyle = theme.mutedText;
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
        };

        // =====================================================================
        // Batch chart renderer
        // =====================================================================
        const renderBatchChart = (ChartLib) => {
            const data   = window.__analyticsData;
            const theme  = data.theme;
            const state  = drillState.batch;

            let labels = [], totals = [], names = [];
            let clickable = false, subtitleText = '', backBtnVisible = false;

            if (state.view === 'batches') {
                const ids = Object.keys(data.alumniByBatch);
                labels = ids.map(id => data.alumniByBatch[id].batch_name);
                names  = labels.slice();
                totals = ids.map(id => data.alumniByBatch[id].total);
                clickable = true;
                subtitleText = 'Total graduates per batch · click a bar to see courses';
                backBtnVisible = false;
            } else {
                const batch = data.alumniByBatchCourse[state.selected] || { courses: {}, batch_name: state.selected };
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
                        borderRadius: 8, borderSkipped: false, maxBarThickness: 52,
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
                        const ids = Object.keys(data.alumniByBatch);
                        const batchId = ids.find(id => data.alumniByBatch[id].batch_name === labels[idx]);
                        if (! batchId) return;
                        drillState.batch.view = 'courses';
                        drillState.batch.selected = batchId;
                        renderBatchChart(ChartLib);
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8,
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
                        y: { beginAtZero: true, grid: { color: theme.gridColor, drawBorder: false }, border: { display: false }, ticks: { stepSize: 1, precision: 0, color: theme.mutedText, callback: (v) => Number.isInteger(v) ? v : null } },
                        x: { ticks: { color: theme.mutedText, font: { weight: '600' } }, grid: { display: false }, border: { display: false } },
                    },
                },
            });
        };

        // =====================================================================
        // Back-button handlers
        // =====================================================================
        if (! window.__analyticsDeptBack) {
            window.__analyticsDeptBack = true;
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#dept-back-btn');
                if (! btn || ! ChartLibRef) return;
                drillState.dept.view = 'departments';
                drillState.dept.selected = null;
                renderDeptChart(ChartLibRef);
            });
        }

        if (! window.__analyticsBatchBack) {
            window.__analyticsBatchBack = true;
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#batch-back-btn');
                if (! btn || ! ChartLibRef) return;
                drillState.batch.view = 'batches';
                drillState.batch.selected = null;
                renderBatchChart(ChartLibRef);
            });
        }

        if (! window.__analyticsComparativeBack) {
            window.__analyticsComparativeBack = true;
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#comparative-back-btn');
                if (! btn || ! ChartLibRef) return;
                drillState.comparative.view = 'departments';
                drillState.comparative.selected = null;
                renderComparativeChart(ChartLibRef);
            });
        }

        // =====================================================================
        // Debounced init
        // =====================================================================
        let __initScheduled = false;
        const scheduleInit = () => {
            if (__initScheduled) return;
            __initScheduled = true;
            requestAnimationFrame(() => {
                __initScheduled = false;
                drillState.dept        = { view: 'departments', selected: null };
                drillState.batch       = { view: 'batches',     selected: null };
                drillState.comparative = { view: 'departments', selected: null };
                initCharts();
            });
        };

        scheduleInit();

        if (! window.__analyticsBatchListener) {
            window.__analyticsBatchListener = true;
            $wire.on('batch-changed', () => scheduleInit());
        }

        if (! window.__analyticsThemeObserver) {
            window.__analyticsThemeObserver = new MutationObserver(() => scheduleInit());
            window.__analyticsThemeObserver.observe(document.documentElement, {
                attributes: true, attributeFilter: ['class'],
            });
        }
    </script>
@endscript