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
                            <p class="text-xs uppercase text-green-700">
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

                <!-- Total Roles Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase text-green-700">
                                Total Roles
                            </p>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <h3 class="text-xl sm:text-2xl font-medium text-black">
                                0
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Total Alumni Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase text-green-700">
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

                <!-- Total Faculty Members Card -->
                <div class="flex flex-col justify-center border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase text-green-700">
                                Total Faculty Members
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
                <!-- Card -->
                <div
                    class="p-4 md:p-5 flex flex-col border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <!-- Header -->
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div>
                            <h2 class="text-xs sm:text-sm text-green-700">
                                Alumni Graduates
                            </h2>
                            <p class="text-base sm:text-xl md:text-2xl font-medium text-black">
                            
                            </p>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Chart Container -->
                    <div id="hs-multiple-bar-charts" class="w-full h-64 sm:h-80 md:h-96">
                        <canvas id="alumniDynamicChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                const alumniCtx = document.getElementById('alumniDynamicChart');

                new Chart(alumniCtx, {
                    type: 'bar',
                    data: {
                        labels: @json(array_keys($alumniByDept)),   // Department names
                        datasets: [{
                            label: 'Alumni Count',
                            data: @json(array_values($alumniByDept)), // Counts per department
                            backgroundColor: ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // 🔑 allows chart to fill container height
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Alumni of each Department' }
                        }
                    }
                });
                </script>

                <!-- End Card -->

                <!-- Card -->
                <div
                    class="p-4 md:p-5 min-h-102.5 flex flex-col border border-white/30 bg-green-90 shadow-2xl rounded-2xl hover:bg-gray-200">
                    <!-- Header -->
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div>
                            <h2 class="text-sm text-green-700   ">
                                Comparative Analysis
                            </h2>
                            <p class="text-xl sm:text-2xl font-medium text-black">
                                
                            </p>
                        </div>
                    </div>
                    <!-- End Header -->

                    <div id="hs-single-area-chart">
                        <!-- Chart Container -->
                        <div id="hs-comparative-chart" class="w-full h-64 sm:h-80 md:h-96">
                            <canvas id="comparativeChart" class="w-full h-full"></canvas>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                        const compCtx = document.getElementById('comparativeChart');

                        new Chart(compCtx, {
                            type: 'bar',
                            data: {
                                labels: ['Engineering', 'Business', 'Education', 'IT', 'Nursing'],
                                datasets: [
                                    {
                                        label: 'Aligned with Work',
                                        data: [80, 70, 60, 75, 65], // static values
                                        backgroundColor: '#16a34a'
                                    },
                                    {
                                        label: 'Not Aligned',
                                        data: [20, 30, 40, 25, 35], // static values
                                        backgroundColor: '#ef4444'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Course Alignment with Current Work (Static)'
                                    }
                                }
                            }
                        });
                        </script>

                    </div>
                </div>
                <!-- End Card -->
            </div>
            <!-- End Charts Grid -->
        </div>
    </div>
</div>
