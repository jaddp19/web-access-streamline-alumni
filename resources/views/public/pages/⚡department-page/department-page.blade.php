<div>
    <section class="relative min-h-screen overflow-hidden bg-[#F7F5EF] py-20">

        {{-- Decorative background layers --}}
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>
            <div class="absolute -top-32 -right-32 w-[32rem] h-[32rem] bg-[#123524]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[28rem] h-[28rem] bg-[#D4A537]/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: radial-gradient(#123524 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6">

            {{-- Header --}}
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-6 py-2 bg-[#123524] rounded-full shadow-md mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
                    <span class="text-white text-xs font-bold tracking-[0.2em]">COLEGIO DE STA. ANA DE VICTORIAS</span>
                </div>

                <h1 class="text-4xl lg:text-5xl font-bold text-[#123524] mb-3 tracking-tight" style="font-family: 'Fraunces', serif;">
                    Academic Departments
                    <span class="block mt-2 relative inline-block">
                        <span class="relative z-10">Excellence in Every Field</span>
                        <span class="absolute left-0 bottom-0 w-full h-1 bg-[#D4A537]/30 -z-0"></span>
                    </span>
                </h1>

                <p class="text-[#123524]/70 max-w-2xl mx-auto text-lg">
                    Explore our diverse academic departments committed to shaping future leaders.
                </p>
            </div>

            {{-- Departments Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($this->departments as $department)
                    <div wire:key="dept-{{ $department->id }}"
                         class="group bg-white rounded-2xl p-6 border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                        {{-- Icon / Logo --}}
                        <div class="w-14 h-14 mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors overflow-hidden">
                            @if ($department->dept_logo)
                                <img src="{{ Storage::url($department->dept_logo) }}"
                                     alt="{{ $department->dept_name }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @else
                                {{-- Fallback icon --}}
                                <svg class="w-7 h-7 text-[#123524]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                            @endif
                        </div>

                        {{-- Name --}}
                        <h3 class="text-lg font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                            {{ $department->dept_name }}
                        </h3>

                        {{-- Optional short description --}}
                        @if ($department->dept_desc)
                            <p class="text-sm text-[#123524]/60 mt-1 line-clamp-2">
                                {{ Str::limit($department->dept_desc, 80) }}
                            </p>
                        @endif

                        {{-- Student count --}}
                        <div class="flex items-center gap-1.5 mt-2 text-[#123524]/60 text-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
                            {{ number_format($department->students_count) }} Students
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#123524]/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <p class="text-[#123524]/60">No departments available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>