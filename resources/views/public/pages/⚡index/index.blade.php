<div>
    <!-- ========== HERO - ALUMNI HOMEPAGE (Simple & Professional) ========== -->
    <section class="relative min-h-screen overflow-hidden">

        <div class="relative max-w-7xl mx-auto px-6 lg:px-12 py-24 lg:py-40">
            <div class="grid lg:grid-cols-12 gap-16 lg:gap-20 items-center">

                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-10">

                    <!-- School Identifier -->
                    <div class="flex items-center gap-3">
                        <div class="px-5 py-2 bg-green-90 backdrop-blur-md border border-transparent rounded-2xl shadow-lg">
                            <span class="text-green-700 text-sm font-extrabold tracking-widest">COLEGIO DE STA. ANA DE VICTORIAS</span>
                        </div>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-5xl lg:text-6xl xl:text-7xl font-bold text-black leading-tight tracking-tighter">
                        Alumni Community
                        <span class="block text-green-700 mt-2">Data & Lifelong Connections</span>
                    </h1>

                    <!-- Description -->
                    <p class="max-w-lg text-lg text-black leading-relaxed">
                        Stay connected with fellow graduates, explore achievements, and celebrate the shared legacy
                        of faith, excellence, and service.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-5 pt-6">
                        <a href="{{ route('register') }}"
                           class="px-9 py-4 bg-green-700 hover:bg-green-800 text-white font-medium rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                            Register Now
                        </a>
                    </div>
                </div>

                <!-- Right Visual -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-transparent">
                        <img
                            src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=900&q=80"
                            alt="Alumni Gathering - Colegio de Sta. Ana de Victorias"
                            class="w-full h-auto aspect-video object-cover">
                    </div>
                </div>
            </div>

            <!-- Simple Statistics -->
            <div class="mt-28 grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
                <div class="text-center p-10 bg-green-90 backdrop-blur-md rounded-3xl border border-transparent shadow-2xl hover:bg-gray-200 transition">
                    <div class="text-5xl lg:text-6xl font-semibold text-black">{{ $this->alumni }}</div>
                    <div class="mt-3 text-black text-sm font-medium tracking-widest">REGISTERED ALUMNI</div>
                </div>
                <div class="text-center p-10 bg-green-90 backdrop-blur-md rounded-3xl border border-transparent shadow-2xl hover:bg-gray-200 transition">
                    <div class="text-5xl lg:text-6xl font-semibold text-black">128</div>
                    <div class="mt-3 text-black text-sm font-medium tracking-widest">GLOBAL CHAPTERS</div>
                </div>
                <div class="text-center p-10 bg-green-90 backdrop-blur-md rounded-3xl border border-transparent shadow-2xl hover:bg-gray-200 transition">
                    <div class="text-5xl lg:text-6xl font-semibold text-black">1,350+</div>
                    <div class="mt-3 text-black text-sm font-medium tracking-widest">SCHOLARSHIPS AWARDED</div>
                </div>
                <div class="text-center p-10 bg-green-90 backdrop-blur-md rounded-3xl border border-transparent shadow-2xl hover:bg-gray-200 transition">
                    <div class="text-5xl lg:text-6xl font-semibold text-black">{{ $this->departments }}</div>
                    <div class="mt-3 text-black text-sm font-medium tracking-widest">Departments</div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========== END HERO ========== -->
</div>
