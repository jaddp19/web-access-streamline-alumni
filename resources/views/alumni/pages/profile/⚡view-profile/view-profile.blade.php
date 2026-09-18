<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== FB-STYLE COVER + PROFILE HEADER ========== --}}
    <div class="bg-white dark:bg-[#242526]">
        <div class="max-w-[900px] mx-auto">

            {{-- Cover photo --}}
            <div
                class="h-40 sm:h-56 lg:h-64 bg-gradient-to-r from-[#123524] via-[#1C6B45] to-[#123524] relative overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]"></div>
                    <div class="absolute right-20 top-10 w-32 h-32 rounded-full bg-white/30"></div>
                    <div class="absolute right-60 -bottom-10 w-40 h-40 rounded-full bg-[#D4A537]"></div>
                </div>
            </div>
            {{-- Profile strip --}}
            <div class="px-4 pb-4 -mt-16 sm:-mt-20 relative">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        @php
                            $rawAvatar = $this->userProfile?->avatar;
                            $avatarUrl = $rawAvatar
                                ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                                    ? $rawAvatar
                                    : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                                : null;
                            $initial = strtoupper(substr($this->alumni->name ?? '?', 0, 1));
                        @endphp

                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $this->alumni->name }}"
                                class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-4 ring-white dark:ring-[#242526] shadow-lg bg-[#D4A537] shrink-0"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span style="display: none;"
                                class="w-32 h-32 sm:w-40 sm:h-40 rounded-full ring-4 ring-white dark:ring-[#242526] shadow-lg bg-yellow-500 items-center justify-center shrink-0">
                                <span class="text-[#0f2b1c] font-bold text-5xl sm:text-6xl"
                                    style="font-family: 'Fraunces', serif;">
                                    {{ $initial }}
                                </span>
                            </span>
                        @else
                            <span
                                class="w-32 h-32 sm:w-40 sm:h-40 rounded-full ring-4 ring-white dark:ring-[#242526] shadow-lg bg-yellow-500 flex items-center justify-center shrink-0">
                                <span class="text-[#0f2b1c] font-bold text-5xl sm:text-6xl"
                                    style="font-family: 'Fraunces', serif;">
                                    {{ $initial }}
                                </span>
                            </span>
                        @endif
                        <div class="pb-2">
                            <h1 class="text-3xl font-bold text-black dark:text-white"
                                style="font-family: 'Fraunces', serif;">
                                {{ $this->alumni->name }}
                            </h1>
                            <p class="text-black/60 dark:text-white/60 text-sm mt-0.5">
                                @if ($this->userProfile?->batch)
                                    Batch {{ $this->userProfile->batch->batch_name }}
                                @else
                                    Alumni Member
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pb-2">
                        @if ($this->userProfile?->is_verified)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FLASH MESSAGES ========== --}}
    <div class="max-w-[900px] mx-auto px-4 pt-4 space-y-3">
        @if (session('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ========== SINGLE-COLUMN CONTENT ========== --}}
    <div class="max-w-[900px] mx-auto px-4 py-6 space-y-4">

        {{-- ========== PERSONAL INFORMATION ========== --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                    Personal Information
                </h2>
                <a href="{{ route('alumni.profile.update', $this->alumni->id) }}"
                    class="text-[#1877F2] text-sm font-semibold hover:underline">
                    Edit
                </a>
            </div>

            <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                {{-- Email --}}
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Email</p>
                    <p class="font-medium text-black dark:text-white mt-1 break-all">{{ $this->alumni->email }}</p>
                </div>

                {{-- Gender --}}
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Gender</p>
                    <p class="font-medium text-black dark:text-white mt-1">
                        {{ $this->contact['gender'] ? ucfirst($this->contact['gender']) : '—' }}
                    </p>
                </div>

                {{-- Primary phone --}}
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Contact
                        Number</p>
                    <p class="font-medium text-black dark:text-white mt-1">
                        @if ($this->contact['phone_number_1'])
                            {{ $this->contact['phone_number_1'] }}
                        @else
                            <span class="text-black/40 dark:text-white/40 italic">Not set</span>
                        @endif
                    </p>
                </div>

                {{-- Alternate phone (only if present) --}}
                @if ($this->contact['phone_number_2'])
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                            Alternate Number</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->contact['phone_number_2'] }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Address --}}
            <div class="mt-5 pt-4 border-t border-black/5 dark:border-white/10">
                <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide mb-1">Address
                </p>
                @if ($this->contact['address'])
                    <p class="text-sm font-medium text-black dark:text-white">
                        {{ $this->contact['address'] }}
                    </p>
                    @if ($this->contact['latitude'] && $this->contact['longitude'])
                        <a href="https://www.google.com/maps?q={{ $this->contact['latitude'] }},{{ $this->contact['longitude'] }}"
                            target="_blank" rel="noopener"
                            class="inline-flex items-center gap-1 text-[#1877F2] text-xs font-semibold hover:underline mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            Open in Google Maps
                        </a>
                    @endif
                @elseif ($this->contact['latitude'] && $this->contact['longitude'])
                    <a href="https://www.google.com/maps?q={{ $this->contact['latitude'] }},{{ $this->contact['longitude'] }}"
                        target="_blank" rel="noopener" class="text-[#1877F2] text-sm font-semibold hover:underline">
                        View location on map
                    </a>
                @else
                    <p class="text-sm text-black/40 dark:text-white/40 italic">Location not set yet.</p>
                @endif
            </div>
        </div>

        {{-- ========== EDUCATION ========== --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                    Education
                </h2>
                @if ($this->userProfile)
                    <a href="{{ route('alumni.profile.update-educational', $this->userProfile->id) }}"
                        class="text-[#1877F2] text-sm font-semibold hover:underline">
                        Edit
                    </a>
                @endif
            </div>

            <div class="flex items-start gap-3 mb-4">
                <div
                    class="w-12 h-12 rounded-lg bg-[#1877F2]/10 dark:bg-[#1877F2]/15 flex items-center justify-center text-[#1877F2] shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-black dark:text-white text-sm">Colegio de Sta. Ana de Victorias</p>
                    <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">
                        {{ $this->course->course_title ?? 'Program not set' }}
                    </p>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-x-6 gap-y-4 pt-4 border-t border-black/5 dark:border-white/10">
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Course</p>
                    <p class="font-medium text-black dark:text-white mt-1">{{ $this->course->course_title ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                        Department</p>
                    <p class="font-medium text-black dark:text-white mt-1">
                        {{ $this->course->department->dept_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Batch</p>
                    <p class="font-medium text-black dark:text-white mt-1">
                        {{ $this->userProfile?->batch?->batch_name ?? '—' }}</p>
                </div>
            </div>

            {{-- ========== BOARD EXAMINATION (only for verified board-program alumni) ========== --}}
            @if ($this->course?->course_type === 'board' && $this->userProfile?->is_verified)
                <div class="mt-5 pt-5 border-t border-black/5 dark:border-white/10">
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-9 h-9 rounded-lg bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#123524] dark:text-[#D4A537] uppercase tracking-wide">
                            Board Examination
                        </h3>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wide">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verified
                        </span>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                        {{-- Board Exam Date --}}
                        <div>
                            <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                Date Taken
                            </p>
                            <p class="font-medium text-black dark:text-white mt-1">
                                @if ($this->userProfile?->board_taken)
                                    {{ \Carbon\Carbon::parse($this->userProfile->board_taken)->format('F d, Y') }}
                                @else
                                    <span class="text-black/40 dark:text-white/40 italic">Not set</span>
                                @endif
                            </p>
                        </div>

                        {{-- Board Rating --}}
                        <div>
                            <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                Rating
                            </p>
                            @if ($this->userProfile?->board_rate !== null)
                                @php
                                    $rate = (float) $this->userProfile->board_rate;
                                    $passed = $rate >= 75;
                                @endphp
                                <p
                                    class="mt-1 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold
                              {{ $passed
                                  ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400'
                                  : 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        @if ($passed)
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        @endif
                                    </svg>
                                    {{ number_format($rate, 2) }}%
                                </p>
                                <p class="text-[11px] text-black/40 dark:text-white/40 mt-1">
                                    {{ $passed ? 'Passed' : 'Below passing mark (75%)' }}
                                </p>
                            @else
                                <p class="font-medium text-black/40 dark:text-white/40 italic mt-1">Not set</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @unless ($this->userProfile)
                <div
                    class="mt-4 text-sm text-black/60 dark:text-white/60 italic bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10 rounded-xl p-4">
                    No profile found. Please update your personal information first.
                </div>
            @endunless
        </div>

        {{-- ========== WORK EXPERIENCE ========== --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                    Work Experience
                </h2>
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('alumni.profile.create-employment') }}" class="text-[#1877F2] hover:underline">
                        Add
                    </a>
                </div>
            </div>

            @if ($this->workHistories->isEmpty())
                <div class="text-center py-8">
                    <div
                        class="w-14 h-14 mx-auto mb-3 rounded-full bg-[#1877F2]/10 dark:bg-[#1877F2]/15 flex items-center justify-center text-[#1877F2]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <p class="text-sm text-black/50 dark:text-white/50">No work experience added yet.</p>
                    <a href="{{ route('alumni.profile.create-employment') }}"
                        class="inline-block mt-3 text-[#1877F2] text-sm font-semibold hover:underline">
                        Add your first job
                    </a>
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($this->workHistories as $history)
                        <div wire:key="wh-{{ $history->id }}" class="flex items-start gap-3">

                            {{-- Company logo / fallback --}}
                            <div
                                class="w-12 h-12 rounded-lg bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0 overflow-hidden">
                                @if ($history->company?->company_logo)
                                    <img src="{{ filter_var($history->company->company_logo, FILTER_VALIDATE_URL)
                                        ? $history->company->company_logo
                                        : \Illuminate\Support\Facades\Storage::url($history->company->company_logo) }}"
                                        alt="{{ $history->company->company_name }}"
                                        class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <svg class="w-6 h-6 text-black/30 dark:text-white/30" fill="none"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-black dark:text-white text-sm truncate">
                                            {{ $history->work_name ?: 'Position not specified' }}
                                        </p>
                                        <p class="text-xs text-black/60 dark:text-white/60 truncate">
                                            {{ $history->company?->company_name ?? 'Company not specified' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        @if ($history->is_current_job)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wide">
                                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                Current
                                            </span>
                                        @endif

                                        {{-- Edit pencil --}}
                                        <a href="{{ route('alumni.profile.update-employment', ['history' => $history->id]) }}"
                                            class="text-[#1877F2] hover:text-[#166FE5] transition shrink-0"
                                            title="Edit this work experience">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Meta row --}}
                                <div
                                    class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-black/50 dark:text-white/50">
                                    @if ($history->date_hired)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                            </svg>
                                            {{ $history->date_hired->format('M Y') }}
                                            @if ($history->is_current_job)
                                                – Present
                                            @endif
                                        </span>
                                    @endif

                                    @if ($history->date_hired)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $this->durationLabel($history) }}
                                        </span>
                                    @endif

                                    @if ($history->company?->company_address)
                                        <span class="inline-flex items-center gap-1 truncate max-w-full">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                            </svg>
                                            <span class="truncate">{{ $history->company->company_address }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
