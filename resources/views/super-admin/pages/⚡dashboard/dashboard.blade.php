<div>
    <!-- Content -->
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">

            <!-- Page heading -->
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">Overview</h1>
                <p class="text-sm text-black/50 mt-0.5">A snapshot of your alumni network.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">

                <!-- Total Users Card -->
                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5 hover:shadow-md transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-green-700/5"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Total Users</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->users }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Active Card -->
                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5 hover:shadow-md transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-emerald-500/5"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Active Alumni</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->active }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Alumni Card -->
                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5 hover:shadow-md transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-[#D4A537]/10"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Total Alumni</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->alumni }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total Program Heads Card -->
                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5 hover:shadow-md transition-shadow">
                    <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-blue-500/5"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">Program Heads</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->programHeads }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Stats Grid -->

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Alumni Graduates Chart -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h2 class="text-sm font-bold text-[#0f2b1c]">Alumni Graduates</h2>
                            <p class="text-xs text-black/40 mt-0.5">Breakdown by department</p>
                        </div>
                        <span class="w-8 h-8 rounded-lg bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </span>
                    </div>
                    <div id="hs-multiple-bar-charts" class="w-full h-64 sm:h-72 md:h-80">
                        <canvas id="alumniDynamicChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Comparative Analysis Chart -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h2 class="text-sm font-bold text-[#0f2b1c]">Comparative Analysis</h2>
                            <p class="text-xs text-black/40 mt-0.5">Course alignment with current work</p>
                        </div>
                        <span class="w-8 h-8 rounded-lg bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                            </svg>
                        </span>
                    </div>
                    <div id="hs-comparative-chart" class="w-full h-64 sm:h-72 md:h-80">
                        <canvas id="comparativeChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
            <!-- End Charts Grid -->

            <!-- Analytics Section -->
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">Analytics</h2>
                <p class="text-sm text-black/50 mt-0.5">Deeper breakdown from the tracer study.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Employment Type -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Type</h2>
                    <p class="text-xs text-black/40 mb-3">Full-time, part-time, freelance, etc.</p>
                    <div class="w-full h-64">
                        <canvas id="employmentTypeChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Organization Type -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Organization Type</h2>
                    <p class="text-xs text-black/40 mb-3">Where alumni currently work</p>
                    <div class="w-full h-64">
                        <canvas id="organizationTypeChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Employment Area -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Area</h2>
                    <p class="text-xs text-black/40 mb-3">Philippines vs. abroad</p>
                    <div class="w-full h-64">
                        <canvas id="employmentAreaChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Time to First Job -->
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Time to First Job</h2>
                    <p class="text-xs text-black/40 mb-3">How long after graduation alumni got employed</p>
                    <div class="w-full h-64">
                        <canvas id="monthsToFirstJobChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
            <!-- End Analytics Section -->
        </div>
    </div>
</div>

<!-- Chart.js Script (included once) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Alumni Graduates Chart
    const rawLabels = @json($this->alumniByDept);
    const alumniCounts = @json(array_values($this->alumniByDept));

    const alumniLabels = rawLabels.map(label => {
        return label
            .trim()
            .split(/\s+/)
            .map(word => word.charAt(0).toUpperCase())
            .join('');
    });

    const alumniCtx = document.getElementById('alumniDynamicChart');
    new Chart(alumniCtx, {
        type: 'bar',
        data: {
            labels: alumniLabels,
            datasets: [{
                label: 'Alumni Count',
                data: alumniCounts,
                backgroundColor: '#16a34a',
                borderRadius: 6,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800 },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f2b1c',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const fullLabel = rawLabels[context.dataIndex];
                            const value = context.formattedValue;
                            return fullLabel + ': ' + value;
                        }
                    }
                }
            }
        }
    });

    // Comparative Analysis Chart
    const analyticsData = @json($this->courseAnalytics);
    const compRawLabels = analyticsData.map(item => item.course_title);
    const compLabels = compRawLabels.map(label => {
        return label
            .trim()
            .split(/\s+/)
            .map(word => word.charAt(0).toUpperCase())
            .join('');
    });

    const compCtx = document.getElementById('comparativeChart');
    new Chart(compCtx, {
        type: 'bar',
        data: {
            labels: compLabels,
            datasets: [
                {
                    label: 'Aligned with Work',
                    data: analyticsData.map(item => item.related_rate),
                    backgroundColor: '#16a34a',
                    borderRadius: 6,
                    maxBarThickness: 28
                },
                {
                    label: 'Not Aligned',
                    data: analyticsData.map(item => 100 - item.related_rate),
                    backgroundColor: '#D4A537',
                    borderRadius: 6,
                    maxBarThickness: 28
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800 },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: '#0f2b1c',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const fullLabel = compRawLabels[context.dataIndex];
                            const value = context.formattedValue;
                            return fullLabel + ': ' + value;
                        }
                    }
                }
            }
        }
    });

    // Employment Type Chart
    const typeData = @json($this->employmentTypeBreakdown);
    const typeCtx = document.getElementById('employmentTypeChart');
    new Chart(typeCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(typeData).map(l => l.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
            datasets: [{
                data: Object.values(typeData),
                backgroundColor: '#16a34a',
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Organization Type Chart
    const orgData = @json($this->organizationTypeBreakdown);
    const orgCtx = document.getElementById('organizationTypeChart');
    new Chart(orgCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(orgData).map(l => l.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
            datasets: [{
                data: Object.values(orgData),
                backgroundColor: '#D4A537',
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                y: { grid: { display: false } }
            }
        }
    });

    // Employment Area Chart
    const areaData = @json($this->employmentAreaBreakdown);
    const areaCtx = document.getElementById('employmentAreaChart');
    new Chart(areaCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(areaData).map(l => l.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
            datasets: [{
                data: Object.values(areaData),
                backgroundColor: ['#16a34a', '#3b82f6'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 20, font: { size: 11 } }
                }
            }
        }
    });

    // Months to First Job Chart
    const monthsData = @json($this->monthsToFirstJobBreakdown);
    const monthsCtx = document.getElementById('monthsToFirstJobChart');
    new Chart(monthsCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(monthsData).map(l => l.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
            datasets: [{
                data: Object.values(monthsData),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                maxBarThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
