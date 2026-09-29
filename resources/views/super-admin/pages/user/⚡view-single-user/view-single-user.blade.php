<div>
    <div class="max-w-4xl w-full px-3 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-14 mx-auto">

        {{-- Flash messages --}}
        @if (session('success'))
            <div
                class="mb-4 flex items-start gap-2.5 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm font-medium">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 flex items-start gap-2.5 px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-sm font-medium">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mb-4 sm:mb-5">
            <a href="{{ route('super-admin.user.view') }}"
                class="inline-flex items-center gap-x-2 px-3 py-1.5 sm:px-3.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 15l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- ===================== HEADER ===================== -->
        <div
            class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-5">
            {{-- Decorative circles — scaled to viewport --}}
            <div class="absolute -right-10 -top-10 w-32 sm:w-48 h-32 sm:h-48 rounded-full bg-[#D4A537]/10"></div>
            <div class="absolute -right-4 top-14 sm:top-16 w-16 sm:w-24 h-16 sm:h-24 rounded-full bg-[#D4A537]/10">
            </div>

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
                        <div
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold text-lg sm:text-xl">
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
                            <span
                                class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-[#D4A537] text-[#123524]">
                                {{ \Illuminate\Support\Str::ucfirst($role->name) }}
                            </span>
                        @endforeach

                        @if ($user->userProfile?->is_verified)
                            <span
                                class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-white/10 text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified
                            </span>
                        @endif

                        @if ($user->userProfile?->is_private)
                            <span
                                class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-white/10 text-white/70">
                                Private profile
                            </span>
                        @endif

                        @php $p = $user->userProfile; @endphp
                        @if ($p)
                            @if ($p->is_approved)
                                <span
                                    class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-400/20 text-emerald-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Approved
                                </span>
                            @elseif (filled($p->last_rejection_reason))
                                <span
                                    class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-red-400/20 text-red-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    Rejected
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-amber-400/20 text-amber-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Awaiting Approval
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== APPROVAL ACTIONS ===================== -->
        @if ($user->userProfile)
            @php $p = $user->userProfile; @endphp

            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    {{-- Status block --}}
                    <div class="min-w-0">
                        <h2
                            class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-1">
                            Profile Approval
                        </h2>

                        @if ($p->is_approved)
                            <p class="text-sm text-black/60 dark:text-white/60">
                                <span
                                    class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    This profile is approved
                                </span>
                                <span class="block mt-0.5 text-xs">
                                    The alumni's data is verified and their tracer submission is final.
                                </span>
                            </p>
                        @elseif (filled($p->last_rejection_reason))
                            <p class="text-sm text-black/60 dark:text-white/60">
                                <span
                                    class="inline-flex items-center gap-1.5 font-semibold text-red-700 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    This profile was rejected
                                </span>
                            </p>
                            <div
                                class="mt-2 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20">
                                <p
                                    class="text-[10px] uppercase tracking-wide font-semibold text-red-700 dark:text-red-400 mb-1">
                                    Reason sent to the alumni
                                </p>
                                <p class="text-sm text-red-800 dark:text-red-300 whitespace-pre-line">
                                    {{ $p->last_rejection_reason }}
                                </p>
                            </div>
                        @else
                            <p class="text-sm text-black/60 dark:text-white/60">
                                <span
                                    class="inline-flex items-center gap-1.5 font-semibold text-amber-700 dark:text-amber-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Awaiting your review
                                </span>
                                <span class="block mt-0.5 text-xs">
                                    Approve to finalize, or reject with a reason.
                                </span>
                            </p>
                        @endif
                    </div>

                    {{-- Buttons --}}
                    <div class="flex flex-col xs:flex-row gap-2 shrink-0">
                        @if (!$p->is_approved)
                            <button type="button" wire:click="approve" wire:loading.attr="disabled"
                                wire:target="approve"
                                class="inline-flex items-center justify-center gap-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="approve">Approve</span>
                                <span wire:loading wire:target="approve">Approving…</span>
                            </button>
                        @endif

                        <button type="button" wire:click="openRejectModal"
                            class="inline-flex items-center justify-center gap-x-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
                            {{ $p->is_approved ? 'Revoke / Reject' : 'Reject' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-4 sm:gap-5">

            <!-- ===================== ACCOUNT INFO ===================== -->
            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2
                    class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Account
                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="min-w-0">
                        <dt
                            class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                            Email</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-all">{{ $user->email }}
                        </dd>
                    </div>
                    <div class="min-w-0">
                        <dt
                            class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                            School ID</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">
                            {{ $user->school_id ?? '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt
                            class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                            Batch</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                            {{ $user->userProfile?->batch?->batch_name ?? '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt
                            class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                            Joined</dt>
                        <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                            {{ $user->created_at?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- ===================== PROFILE INFO ===================== -->
            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2
                    class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Profile
                </h2>

                @if ($user->userProfile)
                    @php
                        $profile = $user->userProfile;
                        $location = $profile->location ?? [];

                        // Gender
                        $rawGender = $profile->gender ?? ($location['gender'] ?? null);
                        $gender = $rawGender ? \Illuminate\Support\Str::headline(strtolower($rawGender)) : '—';

                        // Phone 1
                        $rawPhone = $profile->contact_number_1 ?? ($location['phone_number_1'] ?? null);
                        $displayPhone = $rawPhone;
                        if ($rawPhone && str_starts_with($rawPhone, '+')) {
                            $displayPhone =
                                preg_replace('/^\+(\d{1,3})(\d{3})(\d{3})(\d+)$/', '+$1 $2 $3 $4', $rawPhone) ??
                                $rawPhone;
                        }

                        // Phone 2
                        $rawPhone2 = $profile->contact_number_2 ?? ($location['phone_number_2'] ?? null);
                        $displayPhone2 = $rawPhone2;
                        if ($rawPhone2 && str_starts_with($rawPhone2, '+')) {
                            $displayPhone2 =
                                preg_replace('/^\+(\d{1,3})(\d{3})(\d{3})(\d+)$/', '+$1 $2 $3 $4', $rawPhone2) ??
                                $rawPhone2;
                        }

                        // Full address
                        $fullAddress =
                            $location['address'] ??
                            collect([
                                $location['street_address'] ?? null,
                                $location['barangay_name'] ?? null,
                                $location['city_name'] ?? null,
                                $location['province_name'] ?? null,
                                $location['region_name'] ?? null,
                            ])
                                ->filter()
                                ->implode(', ');
                    @endphp

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Gender</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">{{ $gender }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Mobile Number</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 font-medium">
                                @if ($displayPhone)
                                    <a href="tel:{{ $rawPhone }}"
                                        class="hover:text-[#123524] dark:hover:text-[#D4A537] transition break-all">
                                        {{ $displayPhone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>

                        @if ($displayPhone2)
                            <div class="min-w-0">
                                <dt
                                    class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Alternate Number</dt>
                                <dd class="text-sm sm:text-base text-black dark:text-white mt-1 font-medium">
                                    <a href="tel:{{ $rawPhone2 }}"
                                        class="hover:text-[#123524] dark:hover:text-[#D4A537] transition break-all">
                                        {{ $displayPhone2 }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div class="sm:col-span-2 min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Address</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">
                                {{ $fullAddress ?: '—' }}</dd>
                        </div>

                        @if ($profile->is_verified)
                            {{-- Verified board data → show normally --}}
                            <div class="min-w-0">
                                <dt
                                    class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Featured Board Date</dt>
                                <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                    {{ $profile->board_taken?->format('M d, Y') ?? '—' }}
                                </dd>
                            </div>
                            <div class="min-w-0">
                                <dt
                                    class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Featured Board Rating</dt>
                                <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                    {{ $profile->board_rate !== null ? number_format((float) $profile->board_rate, 2) . '%' : '—' }}
                                </dd>
                            </div>
                        @elseif ($profile->board_taken || $profile->board_rate !== null)
                            {{-- Unverified board data → hidden from public view; registrar sees a notice --}}
                            <div class="sm:col-span-2 min-w-0">
                                <dt
                                    class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Featured Board Details</dt>
                                <dd class="mt-1 flex items-start gap-2 text-sm text-amber-700 dark:text-amber-400">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    <span>
                                        <span class="font-semibold">Awaiting verification</span>
                                        &mdash; submitted board details are hidden from public view until the registrar
                                        approves this profile.
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-black/50 dark:text-white/50">This alumni hasn't completed their profile yet.
                    </p>
                @endif
            </div>

            <!-- ===================== BOARD EXAMINATIONS ===================== -->
            @php
                $boardExams = $user->userProfile?->boardExams ?? collect();
            @endphp

            @if ($boardExams->isNotEmpty())
                <div
                    class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                    <div class="flex items-center justify-between gap-3 mb-3 sm:mb-4 flex-wrap">
                        <div class="flex items-center gap-2">
                            <h2
                                class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide">
                                Board Examinations
                            </h2>

                            @if ($user->userProfile && !$user->userProfile->is_verified)
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    Unverified
                                </span>
                            @endif
                        </div>

                        <span
                            class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full font-semibold bg-[#123524]/5 dark:bg-white/10 text-[#123524]/60 dark:text-white/60">
                            {{ $boardExams->count() }}
                            {{ \Illuminate\Support\Str::plural('attempt', $boardExams->count()) }}
                        </span>
                    </div>

                    @foreach ($boardExams as $exam)
                        @php
                            $rate = (float) $exam->rate;
                            $passed = (bool) $exam->passed;
                        @endphp

                        <div wire:key="board-exam-{{ $exam->id }}"
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 p-3 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] mb-2 last:mb-0">

                            {{-- Left: attempt details --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide bg-[#123524] text-white">
                                        {{ $exam->attempt_label }}
                                    </span>

                                    @if ($exam->exam_name)
                                        <span class="text-xs font-semibold text-black/70 dark:text-white/70">
                                            {{ $exam->exam_name }}
                                        </span>
                                    @endif

                                    @if ($exam->is_top_notcher)
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide bg-[#D4A537] text-[#123524]">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4L2 9.4h7.6z" />
                                            </svg>
                                            {{ $exam->top_notcher_label }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-black/60 dark:text-white/60 mt-1.5">
                                    Taken {{ $exam->date_taken?->format('M d, Y') ?? '—' }}
                                    @if ($exam->rate !== null)
                                        &middot; Rating: <span
                                            class="font-semibold">{{ number_format($rate, 2) }}%</span>
                                    @endif
                                </p>

                                @if ($exam->remarks)
                                    <p class="text-[11px] text-black/40 dark:text-white/40 mt-1 italic">
                                        {{ $exam->remarks }}
                                    </p>
                                @endif
                            </div>

                            {{-- Right: pass/fail badge --}}
                            <span
                                class="self-start sm:self-center shrink-0 text-[10px] sm:text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap
                                {{ $passed
                                    ? 'text-green-700 dark:text-emerald-400 bg-green-100 dark:bg-emerald-500/15'
                                    : 'text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-500/15' }}">
                                {{ $passed ? 'Passed' : 'Failed' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- ===================== EDUCATION ===================== -->
            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2
                    class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Education
                </h2>

                @forelse ($user->userProfile?->courses ?? [] as $course)
                    <div wire:key="course-{{ $course->id }}"
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 p-3 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] mb-2 last:mb-0">
                        <div class="min-w-0">
                            <p class="text-sm sm:text-base text-black dark:text-white font-medium break-words">
                                {{ $course->course_title }}</p>
                            <p class="text-xs text-black/50 dark:text-white/50 break-words">
                                {{ $course->department?->dept_name }} &middot; {{ $course->course_code }}
                            </p>
                        </div>
                        @if ($course->course_type)
                            <span
                                class="self-start sm:self-center text-[10px] sm:text-xs font-semibold text-[#123524] dark:text-[#D4A537] bg-[#D4A537]/20 px-2.5 py-1 rounded-full whitespace-nowrap">
                                {{ \Illuminate\Support\Str::headline($course->course_type) }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-black/50 dark:text-white/50">No course records found.</p>
                @endforelse
            </div>

            <!-- ===================== WORK HISTORY ===================== -->
            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2
                    class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Work History
                </h2>

                @forelse ($user->workHistories as $work)
                    <div wire:key="work-{{ $work->id }}"
                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 p-3 rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] mb-2 last:mb-0">

                        {{-- Left: logo + info --}}
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            {{-- Company logo / fallback --}}
                            <div
                                class="w-10 h-10 rounded-lg bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 flex items-center justify-center shrink-0 overflow-hidden">
                                @if ($work->company?->company_logo)
                                    <img src="{{ filter_var($work->company->company_logo, FILTER_VALIDATE_URL)
                                        ? $work->company->company_logo
                                        : \Illuminate\Support\Facades\Storage::url($work->company->company_logo) }}"
                                        alt="{{ $work->company->company_name }}" class="w-full h-full object-cover"
                                        loading="lazy">
                                @else
                                    <svg class="w-5 h-5 text-black/30 dark:text-white/30" fill="none"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Job info --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-sm sm:text-base text-black dark:text-white font-medium break-words">
                                    {{ $work->work_name }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50 break-words">
                                    {{ $work->company?->company_name ?? '—' }}</p>
                                <p class="text-[11px] sm:text-xs text-black/40 dark:text-white/40 mt-1">
                                    Hired {{ $work->date_hired?->format('M d, Y') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Right: badge --}}
                        @if ($work->is_current_job)
                            <span
                                class="self-start sm:self-center shrink-0 text-[10px] sm:text-xs font-semibold text-green-700 dark:text-emerald-400 bg-green-100 dark:bg-emerald-500/15 px-2.5 py-1 rounded-full whitespace-nowrap">
                                Currently Employed
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-black/50 dark:text-white/50">No work history records found.</p>
                @endforelse
            </div>

            <!-- ===================== TRACER STUDY ===================== -->
            <div
                class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <h2
                    class="text-xs sm:text-sm font-bold text-[#123524] dark:text-white uppercase tracking-wide mb-3 sm:mb-4">
                    Tracer Study
                </h2>

                @if ($user->tracerStudy?->civilStatusEmployment)
                    @php($cse = $user->tracerStudy->civilStatusEmployment)

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Civil Status</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ \Illuminate\Support\Str::headline($cse->civil_status) }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Employment Status</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                @if ($cse->employment_status === 'other' && filled($cse->employment_status_other))
                                    Other ({{ $cse->employment_status_other }})
                                @else
                                    {{ \Illuminate\Support\Str::headline($cse->employment_status) }}
                                @endif
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Current Job Position</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1 break-words">
                                {{ $cse->current_job_position ?? '—' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Related to Degree</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employed_related_to_degree ? \Illuminate\Support\Str::headline($cse->employed_related_to_degree) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Employment Type</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employment_type ? \Illuminate\Support\Str::headline($cse->employment_type) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Organization Type</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->organization_type ? \Illuminate\Support\Str::headline($cse->organization_type) : '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Employment Area</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $cse->employment_area ? \Illuminate\Support\Str::headline($cse->employment_area) : '—' }}
                                @if ($cse->employment_area === 'abroad' && $cse->abroad_country)
                                    ({{ $cse->abroad_country }})
                                @endif
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                Time to First Job</dt>
                            <dd class="text-sm sm:text-base text-black dark:text-white mt-1">
                                {{ $this->monthsToFirstJobLabel() }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-black/50 dark:text-white/50">No tracer study submission found.</p>
                @endif

                @if ($user->tracerStudy?->furtherStudy)
                    @php($fs = $user->tracerStudy->furtherStudy)
                    <div class="mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-black/5 dark:border-white/10">
                        <h3
                            class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold mb-2">
                            Further Studies
                        </h3>
                        @if ($fs->is_pursued_further_studies)
                            <p class="text-sm sm:text-base text-black dark:text-white">
                                Pursuing further studies &mdash; {{ $fs->level_of_study }}
                            </p>
                        @else
                            <p class="text-sm text-black/50 dark:text-white/50">Not currently pursuing further studies.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        {{-- ===================== REJECTION MODAL ===================== --}}
        @if ($showRejectModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                wire:click.self="closeRejectModal">
                <div
                    class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">

                    {{-- Modal header --}}
                    <div class="px-5 sm:px-6 pt-5 sm:pt-6 pb-4 border-b border-black/5 dark:border-white/10">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/15 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-[#123524] dark:text-white">
                                    Reject {{ $user->name }}'s profile
                                </h3>
                                <p class="text-xs text-black/60 dark:text-white/60 mt-1">
                                    The alumni will receive this reason by email and their profile will show as
                                    "Rejected" until they resubmit.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Modal body --}}
                    <div class="px-5 sm:px-6 py-5 space-y-3">
                        <label
                            class="block text-xs font-semibold text-[#123524] dark:text-white uppercase tracking-wide">
                            Reason for rejection <span class="text-red-500">*</span>
                        </label>

                        <textarea wire:model="rejectionReason" rows="5" maxlength="1000"
                            placeholder="e.g. Board rating doesn't match the official PRC records. Please update your board exam details and resubmit."
                            class="w-full px-3 py-2.5 text-sm rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition resize-none
                            @error('rejectionReason') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror"></textarea>

                        @error('rejectionReason')
                            <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <p class="text-[11px] text-black/40 dark:text-white/40">
                            {{ strlen($rejectionReason) }} / 1000 characters
                        </p>
                    </div>

                    {{-- Modal footer --}}
                    <div
                        class="px-5 sm:px-6 py-4 border-t border-black/5 dark:border-white/10 flex flex-col xs:flex-row gap-2 justify-end">
                        <button type="button" wire:click="closeRejectModal"
                            class="w-full xs:w-auto px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 text-sm font-semibold text-black/70 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 transition">
                            Cancel
                        </button>
                        <button type="button" wire:click="reject" wire:loading.attr="disabled" wire:target="reject"
                            class="w-full xs:w-auto px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="reject">Reject &amp; Notify Alumni</span>
                            <span wire:loading wire:target="reject">Sending…</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
