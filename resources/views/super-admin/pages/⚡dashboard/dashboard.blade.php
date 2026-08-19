<div>
    <!-- Content -->
    <div class="w-full shrink-0 p-4 sm:p-6 lg:p-8 flex-wrap px-7">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            
            <!-- Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">

                <!-- Total Users Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm uppercase text-green-700">
                                Total Users
                            </p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <h3 class="text-xl sm:text-2xl font-medium text-black">
                                {{ $this->users }}
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Total Active Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm uppercase text-green-700">
                                Total Active Alumni
                            </p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <h3 class="text-xl sm:text-2xl font-medium text-black">
                                {{ $this->active }}
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Total Alumni Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm uppercase text-green-700">
                                Total Alumni
                            </p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <h3 class="text-xl sm:text-2xl font-medium text-black">
                                {{ $this->alumni }}
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Total Program Heads Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm uppercase text-green-700">
                                Total Program Heads
                            </p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <h3 class="text-xl sm:text-2xl font-medium text-black">
                                {{ $this->programHeads }}
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>
            <!-- End Grid -->

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                <!-- Alumni Graduates Chart -->
                <div class="p-4 md:p-5 flex flex-col border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <!-- Header -->
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div>
                            <h2 class="text-xs sm:text-sm text-green-700">
                                Alumni Graduates
                            </h2>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Chart Container -->
                    <div id="hs-multiple-bar-charts" class="w-full h-64 sm:h-80 md:h-96">
                        <canvas id="alumniDynamicChart" class="w-full h-full"></canvas>
                    </div>
                </div>
                <!-- End Alumni Chart -->

                <!-- Comparative Analysis Chart -->
                <div class="p-4 md:p-5 flex flex-col border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <!-- Header -->
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div>
                            <h2 class="text-xs sm:text-sm text-green-700">
                                Comparative Analysis
                            </h2>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Chart Container -->
                    <div id="hs-comparative-chart" class="w-full h-64 sm:h-80 md:h-96">
                        <canvas id="comparativeChart" class="w-full h-full"></canvas>
                    </div>
                </div>
                <!-- End Comparative Chart -->

            </div>
            <!-- End Charts Grid -->
        </div>
    </div>
</div>

<!-- Chart.js Script (included once) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Alumni Graduates Chart
    const rawLabels = @json(array_keys($alumniByDept));
    const alumniCounts = @json(array_values($alumniByDept));

    // Convert department names into acronyms
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
                backgroundColor: ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800 },
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Alumni of each Department (Acronyms)' },
                tooltip: {
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
    const compRawLabels = @json(array_keys($alumniByDept)); // dynamic departments
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
                    data: [80, 70, 60, 75, 65], // static values for now
                    backgroundColor: '#16a34a'
                },
                {
                    label: 'Not Aligned',
                    data: [20, 30, 40, 25, 35], // static values for now
                    backgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800 },
            plugins: {
                title: {
                    display: true,
                    text: 'Course Alignment with Current Work (Dynamic Departments)'
                },
                tooltip: {
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
</script>