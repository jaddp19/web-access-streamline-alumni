<div>
    <div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

        <!-- Back Button -->
        <div class="mb-5">
            <a href="{{ route('admin.alumni.view') }}"
                class="inline-flex items-center gap-x-2 px-3.5 py-2 text-sm font-semibold rounded-lg bg-white border border-black/10 text-[#123524] hover:bg-black/5 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 15l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
            <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

            <div class="relative flex items-center gap-4">
                @if ($user->userProfile?->avatar)
                    @php
                        $avatar = $user->userProfile->avatar;
                        $avatarUrl = str_starts_with($avatar, 'http') ? $avatar : Storage::url($avatar);
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                        class="w-16 h-16 rounded-2xl object-cover shrink-0 border-2 border-[#D4A537]">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold text-xl shrink-0">
                        {{ Str::of($user->name)->substr(0, 1)->upper() }}
                    </div>
                @endif
                <div>
                    <p class="text-white/50 text-sm">Alumni Profile</p>
                    <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        @foreach ($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#D4A537] text-[#123524]">
                                {{ Str::ucfirst($role->name) }}
                            </span>
                        @endforeach
                        @if ($user->userProfile?->is_verified)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified
                            </span>
                        @endif
                        @if ($user->userProfile?->is_private)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-white/70">
                                Private profile
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-5">

            <!-- Account Info -->
            <div class="bg-white border border-black/10 rounded-3xl p-8">
                <h2 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-4">Account</h2>
                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Email</dt>
                        <dd class="text-black mt-1">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">School ID</dt>
                        <dd class="text-black mt-1">{{ $user->school_id ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Batch</dt>
                        <dd class="text-black mt-1">{{ $user->userProfile?->batch?->batch_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Joined</dt>
                        <dd class="text-black mt-1">{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Profile Info -->
            <div class="bg-white border border-black/10 rounded-3xl p-8">
                <h2 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-4">Profile</h2>
                @if ($user->userProfile)
                    @php
                        $location = $user->userProfile->location ?? [];
                        $fullAddress = $location['address']
                            ?? collect([
                                $location['street_address'] ?? null,
                                $location['barangay_name'] ?? null,
                                $location['city_name'] ?? null,
                                $location['province_name'] ?? null,
                                $location['region_name'] ?? null,
                            ])->filter()->implode(', ');

                        // Format phone for nicer display (e.g. +63 917 123 4567)
                        $rawPhone = $location['phone_number_1'] ?? null;
                        $displayPhone = $rawPhone;
                        if ($rawPhone && str_starts_with($rawPhone, '+')) {
                            $displayPhone = preg_replace('/^\+(\d{1,3})(\d{3})(\d{3})(\d+)$/', '+$1 $2 $3 $4', $rawPhone)
                                         ?? $rawPhone;
                        }
                    @endphp

                    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Gender</dt>
                            <dd class="text-black mt-1">{{ $location['gender'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Mobile Number</dt>
                            <dd class="text-black mt-1 font-medium">
                                @if ($displayPhone)
                                    <a href="tel:{{ $rawPhone }}" class="hover:text-[#123524] transition">
                                        {{ $displayPhone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Address</dt>
                            <dd class="text-black mt-1">{{ $fullAddress ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Board Exam Taken</dt>
                            <dd class="text-black mt-1">{{ $user->userProfile->board_taken ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Board Rating</dt>
                            <dd class="text-black mt-1">{{ $user->userProfile->board_rate ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-black/50 text-sm">This alumni hasn't completed their profile yet.</p>
                @endif
            </div>

            <!-- Education -->
            <div class="bg-white border border-black/10 rounded-3xl p-8">
                <h2 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-4">Education</h2>
                @forelse ($user->userProfile?->courses ?? [] as $course)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#F1EFE7] mb-2 last:mb-0">
                        <div>
                            <p class="text-black font-medium">{{ $course->course_title }}</p>
                            <p class="text-black/50 text-xs">{{ $course->department?->dept_name }} &middot; {{ $course->course_code }}</p>
                        </div>
                        <span class="text-xs font-semibold text-[#123524] bg-[#D4A537]/20 px-2.5 py-1 rounded-full">
                            {{ Str::headline($course->course_type) }}
                        </span>
                    </div>
                @empty
                    <p class="text-black/50 text-sm">No course records found.</p>
                @endforelse
            </div>

            <!-- Work History -->
            <div class="bg-white border border-black/10 rounded-3xl p-8">
                <h2 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-4">Work History</h2>
                @forelse ($user->workHistories as $work)
                    <div class="flex items-start justify-between p-3 rounded-xl bg-[#F1EFE7] mb-2 last:mb-0">
                        <div>
                            <p class="text-black font-medium">{{ $work->work_name }}</p>
                            <p class="text-black/50 text-xs">{{ $work->company?->company_name }}</p>
                            <p class="text-black/40 text-xs mt-1">Hired {{ $work->date_hired?->format('M d, Y') }}</p>
                        </div>
                        <div class="flex flex-col gap-1 items-end">
                            @if ($work->is_current_job)
                                <span class="text-xs font-semibold text-[#123524] bg-[#D4A537]/20 px-2.5 py-1 rounded-full">Current Role</span>
                            @endif
                            @if ($work->is_current_employed)
                                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">Currently Employed</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-black/50 text-sm">No work history records found.</p>
                @endforelse
            </div>

            <!-- Tracer Study -->
            <div class="bg-white border border-black/10 rounded-3xl p-8">
                <h2 class="text-sm font-bold text-[#123524] uppercase tracking-wide mb-4">Tracer Study</h2>
                @if ($user->tracerStudy?->civilStatusEmployment)
                    @php($cse = $user->tracerStudy->civilStatusEmployment)
                    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Civil Status</dt>
                            <dd class="text-black mt-1">{{ Str::headline($cse->civil_status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Employment Status</dt>
                            <dd class="text-black mt-1">{{ Str::headline($cse->employment_status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Current Job Position</dt>
                            <dd class="text-black mt-1">{{ $cse->current_job_position ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Related to Degree</dt>
                            <dd class="text-black mt-1">{{ $cse->employed_related_to_degree ? Str::headline($cse->employed_related_to_degree) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Employment Type</dt>
                            <dd class="text-black mt-1">{{ $cse->employment_type ? Str::headline($cse->employment_type) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Organization Type</dt>
                            <dd class="text-black mt-1">{{ $cse->organization_type ? Str::headline($cse->organization_type) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Employment Area</dt>
                            <dd class="text-black mt-1">
                                {{ $cse->employment_area ? Str::headline($cse->employment_area) : '—' }}
                                @if ($cse->employment_area === 'abroad' && $cse->abroad_country)
                                    ({{ $cse->abroad_country }})
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-black/50 uppercase tracking-wide font-semibold">Time to First Job</dt>
                            <dd class="text-black mt-1">{{ $cse->months_to_first_job ? Str::headline($cse->months_to_first_job) : '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-black/50 text-sm">No tracer study submission found.</p>
                @endif

                @if ($user->tracerStudy?->furtherStudy)
                    @php($fs = $user->tracerStudy->furtherStudy)
                    <div class="mt-6 pt-6 border-t border-black/5">
                        <h3 class="text-xs text-black/50 uppercase tracking-wide font-semibold mb-2">Further Studies</h3>
                        @if ($fs->is_pursued_further_studies)
                            <p class="text-black">Pursuing further studies &mdash; {{ $fs->level_of_study }}</p>
                        @else
                            <p class="text-black/50 text-sm">Not currently pursuing further studies.</p>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
