<div>
    <div class="max-w-4xl w-full px-3 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-14 mx-auto">

        <!-- Back Button -->
        <div class="mb-4 sm:mb-5">
            <a href="{{ route('super-admin.user.view') }}"
                class="inline-flex items-center gap-x-2 px-3 py-1.5 sm:px-3.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 15l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- ===================== HEADER ===================== -->
        <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-5">
            {{-- Decorative circles — scaled to viewport --}}
            <div class="absolute -right-10 -top-10 w-32 sm:w-48 h-32 sm:h-48 rounded-full bg-[#D4A537]/10"></div>
            <div class="absolute -right-4 top-14 sm:top-16 w-16 sm:w-24 h-16 sm:h-24 rounded-full bg-[#D4A537]/10"></div>

            <div class="relative flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                {{-- Avatar --}}
                <div class="shrink-0">
                    @if ($user->userProfile?->avatar)
                        @php
                            $avatar = $user->userProfile->avatar;
                            $avatarUrl = str_starts_with($avatar, 'http')
                                ? $avatar
                                : \Illuminate\Support\Facades\Storage::url($avatar);
                        @endphp
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" loading="lazy"
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover border-2 border-[#D4A537] bg-[#123524]/20">
                    @else
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold text-lg sm:text-xl">
                            {{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
                        </div>
                    @endif
                </div>

                {{-- Name + badges --}}
                <div class="min-w-0 flex-1">
                    <p class="text-white/50 text-xs sm:text-sm">Alumni Profile</p>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate"
                        style="font-family: 'Fraunces', serif;">
                        {{ $user->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-2">
                        @foreach ($user->roles as $role)
                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-[#D4A537] text-[#123524]">
                                {{ \Illuminate\Support\Str::ucfirst($role->name) }}
                            </span>
                        @endforeach

                        @if ($user->userProfile?->is_verified)
                            <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-white/10 text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified
                            </span>
                        @endif

                        @if ($user->userProfile?->is_private)
                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-white/10 text-white/70">
                                Private profile
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:gap-5">

            <!-- ===================== ACCOUNT INFO ===================== -->
            <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2 class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Account
                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="min-w-0">
                        <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Email</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-all">{{ $user->email }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">School ID</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">{{ $user->school_id ?? '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Batch</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $user->userProfile?->batch?->batch_name ?? '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Joined</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- ===================== PROFILE INFO ===================== -->
            <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2 class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Profile
                </h2>

                @if ($user->userProfile)
                    @php
                        $profile  = $user->userProfile;
                        $location = $profile->location ?? [];

                        // Gender
                        $rawGender = $profile->gender ?? ($location['gender'] ?? null);
                        $gender    = $rawGender ? \Illuminate\Support\Str::headline(strtolower($rawGender)) : '—';

                        // Phone 1
                        $rawPhone     = $profile->contact_number_1 ?? ($location['phone_number_1'] ?? null);
                        $displayPhone = $rawPhone;
                        if ($rawPhone && str_starts_with($rawPhone, '+')) {
                            $displayPhone = preg_replace('/^\+(\d{1,3})(\d{3})(\d{3})(\d+)$/', '+$1 $2 $3 $4', $rawPhone) ?? $rawPhone;
                        }

                        // Phone 2
                        $rawPhone2     = $profile->contact_number_2 ?? ($location['phone_number_2'] ?? null);
                        $displayPhone2 = $rawPhone2;
                        if ($rawPhone2 && str_starts_with($rawPhone2, '+')) {
                            $displayPhone2 = preg_replace('/^\+(\d{1,3})(\d{3})(\d{3})(\d+)$/', '+$1 $2 $3 $4', $rawPhone2) ?? $rawPhone2;
                        }

                        // Full address
                        $fullAddress = $location['address']
                            ?? collect([
                                $location['street_address'] ?? null,
                                $location['barangay_name']  ?? null,
                                $location['city_name']      ?? null,
                                $location['province_name']  ?? null,
                                $location['region_name']    ?? null,
                            ])->filter()->implode(', ');
                    @endphp

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Gender</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $gender }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Mobile Number</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 font-medium">
                                @if ($displayPhone)
                                    <a href="tel:{{ $rawPhone }}" class="hover:text-[#123524] dark:hover:text-[#D4A537] transition break-all">
                                        {{ $displayPhone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>

                        @if ($displayPhone2)
                            <div class="min-w-0">
                                <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Alternate Number</dt>
                                <dd class="text-sm sm:text-base text-black dark:text-white mt-1 font-medium">
                                    <a href="tel:{{ $rawPhone2 }}" class="hover:text-[#123524] dark:hover:text-[#D4A537] transition break-all">
                                        {{ $displayPhone2 }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div class="sm:col-span-2 min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Address</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">{{ $fullAddress ?: '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Board Exam Taken</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $profile->board_taken ?? '—' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Board Rating</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $profile->board_rate ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-black/50 dark:text-white/50">This alumni hasn't completed their profile yet.</p>
                @endif
            </div>

            <!-- ===================== EDUCATION ===================== -->
            <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2 class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Education
                </h2>

                @forelse ($user->userProfile?->courses ?? [] as $course)
                    <div wire:key="course-{{ $course->id }}"
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 p-3 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] mb-2 last:mb-0">
                        <div class="min-w-0">
                            <p class="text-sm sm:text-base text-black dark:text-white font-medium break-words">{{ $course->course_title }}</p>
                            <p class="text-xs text-black/50 dark:text-white/50 break-words">
                                {{ $course->department?->dept_name }} &middot; {{ $course->course_code }}
                            </p>
                        </div>
                        @if ($course->course_type)
                            <span class="self-start sm:self-center text-[10px] sm:text-xs font-semibold text-[#123524] dark:text-[#D4A537] bg-[#D4A537]/20 px-2.5 py-1 rounded-full whitespace-nowrap">
                                {{ \Illuminate\Support\Str::headline($course->course_type) }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-black/50 dark:text-white/50">No course records found.</p>
                @endforelse
            </div>

            <!-- ===================== WORK HISTORY ===================== -->
            <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2 class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Work History
                </h2>

                @forelse ($user->workHistories as $work)
                    <div wire:key="work-{{ $work->id }}"
                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 p-3 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] mb-2 last:mb-0">

                        {{-- Left: logo + info --}}
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            {{-- Company logo / fallback --}}
                            <div class="w-10 h-10 rounded-lg bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 flex items-center justify-center shrink-0 overflow-hidden">
                                @if ($work->company?->company_logo)
                                    <img src="{{ filter_var($work->company->company_logo, FILTER_VALIDATE_URL)
                                            ? $work->company->company_logo
                                            : \Illuminate\Support\Facades\Storage::url($work->company->company_logo) }}"
                                        alt="{{ $work->company->company_name }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <svg class="w-5 h-5 text-black/30 dark:text-white/30" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Job info --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-sm sm:text-base text-black dark:text-white font-medium break-words">{{ $work->work_name }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50 break-words">{{ $work->company?->company_name ?? '—' }}</p>
                                <p class="text-[11px] sm:text-xs text-black/40 dark:text-white/40 mt-1">
                                    Hired {{ $work->date_hired?->format('M d, Y') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Right: badge --}}
                        @if ($work->is_current_job)
                            <span class="self-start sm:self-center shrink-0 text-[10px] sm:text-xs font-semibold text-green-700 dark:text-emerald-400 bg-green-100 dark:bg-emerald-500/15 px-2.5 py-1 rounded-full whitespace-nowrap">
                                Currently Employed
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-black/50 dark:text-white/50">No work history records found.</p>
                @endforelse
            </div>

            <!-- ===================== TRACER STUDY ===================== -->
            <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2 class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Tracer Study
                </h2>

                @if ($user->tracerStudy?->civilStatusEmployment)
                    @php($cse = $user->tracerStudy->civilStatusEmployment)

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Civil Status</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ \Illuminate\Support\Str::headline($cse->civil_status) }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Employment Status</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ \Illuminate\Support\Str::headline($cse->employment_status) }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Current Job Position</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">{{ $cse->current_job_position ?? '—' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Related to Degree</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employed_related_to_degree ? \Illuminate\Support\Str::headline($cse->employed_related_to_degree) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Employment Type</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employment_type ? \Illuminate\Support\Str::headline($cse->employment_type) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Organization Type</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->organization_type ? \Illuminate\Support\Str::headline($cse->organization_type) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Employment Area</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employment_area ? \Illuminate\Support\Str::headline($cse->employment_area) : '—' }}
                                @if ($cse->employment_area === 'abroad' && $cse->abroad_country)
                                    ({{ $cse->abroad_country }})
                                @endif
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Time to First Job</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $this->monthsToFirstJobLabel() }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-black/50 dark:text-white/50">No tracer study submission found.</p>
                @endif

                @if ($user->tracerStudy?->furtherStudy)
                    @php($fs = $user->tracerStudy->furtherStudy)
                    <div class="mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-black/5 dark:border-white/10">
                        <h3 class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold mb-2">
                            Further Studies
                        </h3>
                        @if ($fs->is_pursued_further_studies)
                            <p class="text-sm sm:text-base text-black dark:text-white">
                                Pursuing further studies &mdash; {{ $fs->level_of_study }}
                            </p>
                        @else
                            <p class="text-sm text-black/50 dark:text-white/50">Not currently pursuing further studies.</p>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>