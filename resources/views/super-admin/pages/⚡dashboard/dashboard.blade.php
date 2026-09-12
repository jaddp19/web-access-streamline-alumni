<div>
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">

            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">Overview
                </h1>
                <p class="text-sm text-black/50 mt-0.5">A snapshot of your alumni network.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="relative flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">
                                Total Users</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->users }}</h3>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="relative flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">
                                Active Alumni</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->active }}</h3>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="relative flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">
                                Total Alumni</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->alumni }}</h3>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <div class="relative flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-[10px] sm:text-xs uppercase tracking-wide text-black/50 font-semibold truncate">
                                Program Heads</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-[#0f2b1c] mt-0.5">{{ $this->programHeads }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

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

            <div>
                <h2 class="text-lg sm:text-xl font-bold text-[#0f2b1c]" style="font-family: 'Fraunces', serif;">
                    Analytics</h2>
                <p class="text-sm text-black/50 mt-0.5">Deeper breakdown from the tracer study.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Status</h2>
                    <p class="text-xs text-black/40 mb-3">Employed, unemployed, self-employed</p>
                    @if (empty($this->employmentStatusBreakdown))
                        <p class="text-sm text-black/40 py-16 text-center">No tracer employment data yet. Submit a
                            tracer study first.</p>
                    @else
                        <div class="w-full h-64"><canvas id="employmentStatusChart"></canvas></div>
                    @endif
                </div>

                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Employment Type</h2>
                    <p class="text-xs text-black/40 mb-3">Full-time, part-time, freelance, etc.</p>
                    @if (empty($this->employmentTypeBreakdown))
                        <p class="text-sm text-black/40 py-16 text-center">No employment type yet. Alumni must mark
                            themselves as employed in the tracer form.</p>
                    @else
                        <div class="w-full h-64"><canvas id="employmentTypeChart"></canvas></div>
                    @endif
                </div>
            </div>

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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white border border-black/5 shadow-sm rounded-2xl p-4 md:p-5 lg:col-span-2">
                    <h2 class="text-sm font-bold text-[#0f2b1c] mb-1">Time to First Job</h2>
                    <p class="text-xs text-black/40 mb-3">How long after graduation alumni got employed</p>
                    @if (collect($this->monthsToFirstJobBreakdown)->sum() === 0)
                        <p class="text-sm text-black/40 py-16 text-center">No time-to-first-job data yet.</p>
                    @else
                        <div class="w-full h-64"><canvas id="monthsToFirstJobChart"></canvas></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
    <script>
        const pretty = (label) => String(label).replace(/-/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
        const initials = (label) => String(label).trim().split(/\s+/).map((w) => w.charAt(0).toUpperCase()).join('');

        const waitForChart = () => new Promise((resolve) => {
            if (window.Chart) return resolve(window.Chart);
            const t = setInterval(() => {
                if (window.Chart) {
                    clearInterval(t);
                    resolve(window.Chart);
                }
            }, 40);
            setTimeout(() => {
                clearInterval(t);
                resolve(window.Chart || null);
            }, 4000);
        });

        const makeChart = (ChartLib, id, config) => {
            const el = document.getElementById(id);
            if (!el || !ChartLib) return;
            new ChartLib(el, config);
        };

        const alumniByDept = @json($this->alumniByDept);
        const analyticsData = @json($this->courseAnalytics);
        const statusData = @json($this->employmentStatusBreakdown);
        const typeData = @json($this->employmentTypeBreakdown);
        const orgData = @json($this->organizationTypeBreakdown);
        const areaData = @json($this->employmentAreaBreakdown);
        const monthsData = @json($this->monthsToFirstJobBreakdown);

        waitForChart().then((ChartLib) => {
            const rawLabels = Object.keys(alumniByDept || {});
            makeChart(ChartLib, 'alumniDynamicChart', {
                type: 'bar',
                data: {
                    labels: rawLabels.map(initials),
                    datasets: [{
                        data: Object.values(alumniByDept || {}),
                        backgroundColor: '#16a34a',
                        borderRadius: 6,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (i) => rawLabels[i[0]?.dataIndex] ?? ''
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            const analyticsData = @json($this->courseAnalytics);
            const compLabels = (analyticsData || []).map((item) => item.course_code);
            const compTitles = (analyticsData || []).map((item) => item.course_title);

            makeChart(ChartLib, 'comparativeChart', {
                type: 'bar',
                data: {
                    labels: compLabels,
                    datasets: [{
                            label: 'Aligned with Work',
                            data: (analyticsData || []).map((i) => i.related_rate),
                            backgroundColor: '#16a34a',
                            borderRadius: 6,
                            maxBarThickness: 28
                        },
                        {
                            label: 'Not Aligned',
                            data: (analyticsData || []).map((i) => 100 - i.related_rate),
                            backgroundColor: '#D4A537',
                            borderRadius: 6,
                            maxBarThickness: 28
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#f1f1f1'
                            },
                            ticks: {
                                callback: (value) => value + '%'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${ctx.formattedValue}%`
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'barPercentLabels',
                    afterDatasetsDraw(chart) {
                        const {
                            ctx
                        } = chart;
                        ctx.save();
                        ctx.font = 'bold 11px sans-serif';
                        ctx.fillStyle = '#0f2b1c';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        chart.data.datasets.forEach((dataset, datasetIndex) => {
                            chart.getDatasetMeta(datasetIndex).data.forEach((bar,
                            index) => {
                                const value = dataset.data[index];
                                if (value === null || value === undefined) return;
                                ctx.fillText(value + '%', bar.x, bar.y - 4);
                            });
                        });
                        ctx.restore();
                    }
                }]
            });

            makeChart(ChartLib, 'employmentStatusChart', {
                type: 'pie',
                data: {
                    labels: Object.keys(statusData || {}).map(pretty),
                    datasets: [{
                        data: Object.values(statusData || {}),
                        backgroundColor: ['#16a34a', '#D4A537', '#3b82f6', '#94a3b8'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            makeChart(ChartLib, 'employmentTypeChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(typeData || {}).map(pretty),
                    datasets: [{
                        data: Object.values(typeData || {}),
                        backgroundColor: '#16a34a',
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            makeChart(ChartLib, 'organizationTypeChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(orgData || {}).map(pretty),
                    datasets: [{
                        data: Object.values(orgData || {}),
                        backgroundColor: '#D4A537',
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            makeChart(ChartLib, 'employmentAreaChart', {
                type: 'pie',
                data: {
                    labels: Object.keys(areaData || {}).map(pretty),
                    datasets: [{
                        data: Object.values(areaData || {}),
                        backgroundColor: ['#16a34a', '#3b82f6'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            makeChart(ChartLib, 'monthsToFirstJobChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(monthsData || {}).map(pretty),
                    datasets: [{
                        data: Object.values(monthsData || {}),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endscript
