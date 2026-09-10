<div wire:poll.5s="checkApprovalStatus" class="flex items-center justify-center py-20">
    <div class="rounded-2xl p-8 max-w-md text-center bg-white shadow-sm border border-[#123524]/5">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#D4A537]/15 flex items-center justify-center">
            <svg class="w-7 h-7 text-[#D4A537] animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-xl font-bold text-[#123524] mb-2" style="font-family: 'Fraunces', serif;">Registered Successfully</h2>
        <p class="text-[#123524]/60 mb-6">
            Please wait while an admin reviews and approves your alumni registration. This page will update automatically once you're approved.
        </p>

        <a href="{{ route('home') }}"
            class="inline-block bg-[#123524] text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-[#0d2819] transition">
            Back to Home
        </a>
    </div>
</div>
