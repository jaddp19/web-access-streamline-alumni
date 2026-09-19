<div class="p-8">

    {{-- Flash message --}}
    @if (session('status'))
        <div class="mb-6 flex items-start gap-2.5 px-5 py-3 bg-[#123524]/5 border border-[#123524]/10 rounded-xl text-[#123524] text-sm font-medium">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                    Alumni Verification Queue
                </h1>
                <p class="text-[#123524]/60 mt-0.5 text-sm">Board-program alumni awaiting manual verification of their PRC board exam results.</p>
            </div>
        </div>

        <div class="relative w-full sm:w-72">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name, email, or school ID..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#123524]/15 text-sm text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
        </div>
    </div>

    {{-- Queue list --}}
    <div class="space-y-4">
        @forelse ($pendingUsers as $user)
            @php
                $profile = $user->userProfile;
                $course  = $profile?->courses->first();

                $rawAvatar = $profile?->avatar;
                $avatarUrl = $rawAvatar
                    ? (filter_var($rawAvatar, FILTER_VALIDATE_URL) ? $rawAvatar : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                    : null;
                $initial = strtoupper(substr($user->name, 0, 1));
            @endphp

            <div wire:key="user-{{ $user->id }}" class="bg-white rounded-2xl p-6 shadow-sm border border-[#123524]/5 hover:shadow-md transition-shadow flex flex-col md:flex-row md:items-start justify-between gap-6">

                <div class="flex-1 flex items-start gap-4">
                    {{-- Avatar --}}
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}"
                             class="w-11 h-11 rounded-full object-cover shrink-0 bg-[#123524]/10"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none;" class="w-11 h-11 rounded-full bg-[#123524]/10 items-center justify-center text-[#123524] font-bold shrink-0">
                            {{ $initial }}
                        </div>
                    @else
                        <div class="w-11 h-11 rounded-full bg-[#123524]/10 flex items-center justify-center text-[#123524] font-bold shrink-0">
                            {{ $initial }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        {{-- Name + status badges --}}
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h3 class="font-bold text-[#123524]">{{ $user->name }}</h3>
                            <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold uppercase tracking-wide">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" /></svg>
                                Pending
                            </span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#123524]/5 text-[#123524]/60 font-medium">
                                Registered {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-sm text-[#123524]/60">{{ $user->email }}</p>

                        {{-- School ID --}}
                        <div class="mt-2 inline-flex items-center gap-1.5 text-xs text-[#123524]/50 bg-[#F7F5EF] rounded-lg px-2.5 py-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                            <span class="font-mono">School ID: {{ $user->school_id }}</span>
                        </div>

                        {{-- Board Program Info Grid --}}
                        <div class="mt-3 grid sm:grid-cols-2 gap-2">
                            {{-- Program --}}
                            @if ($course)
                                <div class="flex items-center gap-2 text-xs text-[#123524]/70 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                    </svg>
                                    <span class="font-semibold truncate">{{ $course->course_title }}</span>
                                </div>
                            @endif

                            {{-- Batch --}}
                            @if ($profile?->batch)
                                <div class="flex items-center gap-2 text-xs text-[#123524]/70 bg-[#F7F5EF] border border-black/5 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-[#a97f1f] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <span class="font-semibold">Batch {{ $profile->batch->batch_name }}</span>
                                </div>
                            @endif

                            {{-- Board Exam Date --}}
                            @if ($profile?->board_taken)
                                <div class="flex items-center gap-2 text-xs text-[#123524]/70 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <span class="font-semibold">
                                        Took exam {{ \Carbon\Carbon::parse($profile->board_taken)->format('M d, Y') }}
                                    </span>
                                </div>
                            @endif

                            {{-- Board Rating --}}
                            @if ($profile?->board_rate !== null)
                                @php
                                    $rate = (float) $profile->board_rate;
                                    $passed = $rate >= 75;
                                @endphp
                                <div class="flex items-center gap-2 text-xs font-semibold rounded-lg px-3 py-2 border
                                            {{ $passed ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-red-700 bg-red-50 border-red-100' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Board Rating: {{ number_format($rate, 2) }}%</span>
                                </div>
                            @endif
                        </div>

                        {{-- Rejection banner --}}
                        @if ($user->rejected_at)
                            <div class="mt-3 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl p-3.5">
                                <div class="text-xs text-red-500 font-semibold tracking-wide uppercase flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    Previously Rejected
                                </div>
                                <div class="mt-1">{{ $user->rejection_reason }}</div>
                                <div class="text-xs text-red-400 mt-1">{{ $user->rejected_at->diffForHumans() }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex md:flex-col gap-2 shrink-0 md:w-36">
                    <button wire:click="approve({{ $user->id }})"
                        wire:confirm="Approve {{ $user->name }} as a verified alumni?"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-[#123524] text-white text-sm font-semibold rounded-xl hover:bg-[#0d2819] transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Approve
                    </button>
                    <button wire:click="openRejectModal({{ $user->id }})"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white border border-red-300 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-[#123524]/5">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#D4A537]/15 flex items-center justify-center">
                    <svg class="w-7 h-7 text-[#D4A537]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <p class="text-[#123524]/50">No pending board exam verifications right now.</p>
            </div>
        @endforelse
    </div>

    {{-- Custom Pagination Footer --}}
    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Results count --}}
        <p class="text-sm text-[#123524]/60 text-center sm:text-left">
            Showing
            <span class="font-semibold text-[#123524]">{{ $pendingUsers->count() ?? 0 }}</span>
            of
            <span class="font-semibold text-[#123524]">{{ $pendingUsers->total() }}</span>
            {{ Str::plural('result', $pendingUsers->total()) }}
        </p>

        {{-- Prev / Next buttons --}}
        <div class="grid grid-cols-2 sm:inline-flex gap-x-2">
            {{-- Prev --}}
            @if ($pendingUsers->onFirstPage())
                <button disabled
                    class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 15l-6-6 6-6" />
                    </svg>
                    Prev
                </button>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" wire:target="previousPage,search"
                    class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 15l-6-6 6-6" />
                    </svg>
                    Prev
                </button>
            @endif

            {{-- Next --}}
            @if ($pendingUsers->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" wire:target="nextPage,search"
                    class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition disabled:opacity-50">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 3l6 6-6 6" />
                    </svg>
                </button>
            @else
                <button disabled
                    class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 3l6 6-6 6" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Reject reason modal --}}
    @if ($rejectingUserId)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4"
             wire:click.self="closeRejectModal">
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                    Reject Application
                </h3>
                <p class="text-sm text-[#123524]/60 mt-1 mb-4">
                    Optionally add a reason. This may be shared with the applicant.
                </p>

                <textarea wire:model="rejectReasonInput" rows="3" placeholder="e.g. Board exam date does not match PRC records."
                    class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-sm text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"></textarea>

                <div class="flex gap-3 mt-5">
                    <button wire:click="closeRejectModal"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm font-semibold hover:bg-[#123524]/5 transition">
                        Cancel
                    </button>
                    <button wire:click="confirmReject"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>