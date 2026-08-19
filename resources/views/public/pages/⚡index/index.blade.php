<div>
    <!-- ========== HERO - ALUMNI HOMEPAGE ========== -->
    <section class="relative min-h-screen overflow-hidden bg-[#F7F5EF]">

        <!-- Decorative background layers -->
        <div class="absolute inset-0 -z-10">
            <!-- soft gradient wash -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>

            <!-- large blurred blobs -->
            <div class="absolute -top-32 -right-32 w-[32rem] h-[32rem] bg-[#123524]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[28rem] h-[28rem] bg-[#D4A537]/20 rounded-full blur-3xl"></div>

            <!-- subtle dot grid -->
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: radial-gradient(#123524 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-12 py-24 lg:py-36">
            <div class="grid lg:grid-cols-12 gap-16 lg:gap-20 items-center">

                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-8">

                    <!-- School Identifier -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 px-5 py-2 bg-[#123524] rounded-full shadow-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
                            <span class="text-white text-xs font-bold tracking-[0.2em]">COLEGIO DE STA. ANA DE VICTORIAS</span>
                        </div>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-5xl lg:text-6xl xl:text-7xl font-bold text-[#123524] leading-[1.05] tracking-tighter" style="font-family: 'Fraunces', serif;">
                        Alumni Community
                        <span class="block mt-2 relative inline-block">
                            <span class="relative z-10 text-[#123524]">& Lifelong Connections</span>
                            <span class="absolute left-0 bottom-1 w-full h-2 bg-[#D4A537]/30 -z-0"></span>
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="max-w-lg text-lg text-[#123524]/70 leading-relaxed">
                        Stay connected with fellow graduates, explore achievements, and celebrate the shared legacy
                        of faith, excellence, and service.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-5 pt-4">
                        <a href="{{ route('register') }}"
                           class="group px-9 py-4 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                            Register Now
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>

                        <a href="/about"
                           class="px-7 py-4 text-[#123524] font-semibold hover:text-[#D4A537] transition-colors flex items-center gap-2">
                            Learn more
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Visual -->
                <div class="lg:col-span-5">
                    <div class="relative">
                        <!-- gold frame offset behind image -->
                        <div class="absolute -inset-4 border-2 border-[#D4A537]/50 rounded-3xl -z-10 translate-x-4 translate-y-4"></div>

                        <div class="relative rounded-3xl overflow-hidden shadow-2xl ring-1 ring-black/5">
                            <img
                                src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=900&q=80"
                                alt="Alumni Gathering - Colegio de Sta. Ana de Victorias"
                                class="w-full h-auto aspect-video object-cover">

                            <!-- gradient overlay for depth -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#123524]/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mt-32 grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">

                <div class="group text-center p-8 lg:p-10 bg-white rounded-3xl border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors">
                        <svg class="w-6 h-6 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div class="text-4xl lg:text-5xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">{{ $this->alumni }}</div>
                    <div class="mt-2 text-[#123524]/60 text-xs font-bold tracking-widest">REGISTERED ALUMNI</div>
                </div>

                <div class="group text-center p-8 lg:p-10 bg-white rounded-3xl border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors">
                        <svg class="w-6 h-6 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <div class="text-4xl lg:text-5xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">128</div>
                    <div class="mt-2 text-[#123524]/60 text-xs font-bold tracking-widest">GLOBAL CHAPTERS</div>
                </div>

                <div class="group text-center p-8 lg:p-10 bg-white rounded-3xl border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors">
                        <svg class="w-6 h-6 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443" />
                        </svg>
                    </div>
                    <div class="text-4xl lg:text-5xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">1,350+</div>
                    <div class="mt-2 text-[#123524]/60 text-xs font-bold tracking-widest">SCHOLARSHIPS AWARDED</div>
                </div>

                <div class="group text-center p-8 lg:p-10 bg-white rounded-3xl border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors">
                        <svg class="w-6 h-6 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div class="text-4xl lg:text-5xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">{{ $this->departments }}</div>
                    <div class="mt-2 text-[#123524]/60 text-xs font-bold tracking-widest">DEPARTMENTS</div>
                </div>

            </div>
        </div>
    </section>
    <!-- ========== END HERO ========== -->
</div>
