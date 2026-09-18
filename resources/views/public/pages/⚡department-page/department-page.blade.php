<div>
    <section class="relative min-h-screen overflow-hidden bg-[#F7F5EF] py-20">

        {{-- Decorative background layers --}}
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>
            <div class="absolute -top-32 -right-32 w-[32rem] h-[32rem] bg-[#123524]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[28rem] h-[28rem] bg-[#D4A537]/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.04]"
                style="background-image: radial-gradient(#123524 1px, transparent 1px); background-size: 24px 24px;">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6">

            {{-- Header --}}
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-6 py-2 bg-[#123524] rounded-full shadow-md mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
                    <span class="text-white text-xs font-bold tracking-[0.2em]">COLEGIO DE STA. ANA DE VICTORIAS</span>
                </div>

                <h1 class="text-4xl lg:text-5xl font-bold text-[#123524] mb-3 tracking-tight"
                    style="font-family: 'Fraunces', serif;">
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
                        class="group bg-white rounded-2xl p-6 border border-[#123524]/5 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

                        {{-- Icon / Logo --}}
                        <div
                            class="w-14 h-14 mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center group-hover:bg-[#D4A537]/15 transition-colors overflow-hidden shrink-0">
                            @if ($department->dept_logo === 'CSAV-LOGO')
                                <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
                                    alt="{{ $department->dept_name }}" class="w-full h-full object-cover"
                                    loading="lazy">
                            @elseif ($department->dept_logo)
                                <img src="{{ Storage::url($department->dept_logo) }}" alt="{{ $department->dept_name }}"
                                    class="w-full h-full object-cover" loading="lazy">
                            @else
                                {{-- Fallback icon --}}
                                <svg class="w-7 h-7 text-[#123524]" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
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

                        {{-- Courses list --}}
                        @if ($department->courses->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-[#123524]/5">
                                <p class="text-xs font-bold text-[#123524]/40 uppercase tracking-wider mb-2">
                                    {{ $department->courses->count() }}
                                    {{ Str::plural('Program', $department->courses->count()) }}
                                </p>
                                <ul class="space-y-1.5">
                                    @foreach ($department->courses as $course)
                                        <li class="flex items-start gap-2 text-sm text-[#123524]/70">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-[#D4A537]" fill="none"
                                                stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="leading-snug">{{ $course->course_title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div
                            class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#123524]/5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#123524]/30" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
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
