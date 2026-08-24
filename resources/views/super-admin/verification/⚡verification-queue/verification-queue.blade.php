<div class="p-8">

    {{-- Flash message --}}
    @if (session('status'))
        <div class="mb-6 px-5 py-3 bg-[#123524]/5 border border-[#123524]/10 rounded-xl text-[#123524] text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                Alumni Verification Queue
            </h1>
            <p class="text-[#123524]/60 mt-1 text-sm">Review pending registrations awaiting manual approval.</p>
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
            <div wire:key="user-{{ $user->id }}" class="bg-white rounded-2xl p-6 shadow-lg border border-[#123524]/5 flex flex-col md:flex-row md:items-start justify-between gap-6">

                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h3 class="font-bold text-[#123524]">{{ $user->name }}</h3>

                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#123524]/5 text-[#123524]/60 font-medium">
                            Registered {{ $user->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-sm text-[#123524]/60">{{ $user->email }}</p>

                    <div class="mt-3 text-sm text-[#123524]/70 bg-[#F7F5EF] rounded-xl p-4">
                        <div class="text-xs text-[#123524]/40 font-semibold tracking-wide">SCHOOL ID</div>
                        <div class="mt-0.5 font-mono">{{ $user->school_id }}</div>
                    </div>
                </div>

                <div class="flex md:flex-col gap-2 shrink-0">
                    <button wire:click="approve({{ $user->id }})"
                        wire:confirm="Approve {{ $user->name }} as a verified alumni?"
                        class="flex-1 md:flex-none px-5 py-2.5 bg-[#123524] text-white text-sm font-semibold rounded-xl hover:bg-[#0d2819] transition whitespace-nowrap">
                        Approve
                    </button>
                    <button wire:click="openRejectModal({{ $user->id }})"
                        class="flex-1 md:flex-none px-5 py-2.5 bg-white border border-red-300 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition whitespace-nowrap">
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
