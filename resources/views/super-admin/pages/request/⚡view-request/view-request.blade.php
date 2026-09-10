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
                    Alumni Application Queue
                </h1>
                <p class="text-[#123524]/60 mt-0.5 text-sm">Review pending registrations awaiting manual approval.</p>
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
            <div wire:key="user-{{ $user->id }}" class="bg-white rounded-2xl p-6 shadow-sm border border-[#123524]/5 hover:shadow-md transition-shadow flex flex-col md:flex-row md:items-start justify-between gap-6">

                <div class="flex-1 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full bg-[#123524]/10 flex items-center justify-center text-[#123524] font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
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

                        <div class="mt-2 inline-flex items-center gap-1.5 text-xs text-[#123524]/50 bg-[#F7F5EF] rounded-lg px-2.5 py-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                            <span class="font-mono">School ID: {{ $user->school_id }}</span>
                        </div>

                        @php
                            $location = $user->userProfile && is_array($user->userProfile->location)
                                ? $user->userProfile->location
                                : [];
                        @endphp

                        @if (! empty($location['rejected_at']))
                            <div class="mt-3 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl p-3.5">
                                <div class="text-xs text-red-500 font-semibold tracking-wide uppercase flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    Previously Rejected
                                </div>
                                <div class="mt-1">{{ $location['rejection_reason'] }}</div>
                                <div class="text-xs text-red-400 mt-1">{{ \Carbon\Carbon::parse($location['rejected_at'])->diffForHumans() }}</div>
                            </div>
                        @endif
                    </div>
                </div>

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
                <p class="text-[#123524]/50">No pending verifications right now.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $pendingUsers->links() }}
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

                <textarea wire:model="rejectReasonInput" rows="3" placeholder="e.g. School ID does not match our records."
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

    {{-- He who is contented is rich. - Laozi --}}
</div>
