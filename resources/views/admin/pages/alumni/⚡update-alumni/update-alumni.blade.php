<div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10">

    <!-- ========== BACK ========== -->
    <div class="mb-4 sm:mb-5">
        <a href="{{ route('admin.alumni.view') }}"
            class="inline-flex items-center gap-x-2 px-3 py-1.5 sm:px-3.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M15 15l-6-6 6-6" />
            </svg>
            <span>Back</span>
        </a>
    </div>

    <!-- ========== HEADER ========== -->
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-5">
        <div class="absolute -right-10 -top-10 w-32 sm:w-48 h-32 sm:h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-14 sm:top-16 w-16 sm:w-24 h-16 sm:h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-3 sm:gap-4">
            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white/50 text-xs sm:text-sm">Program Head</p>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate"
                    style="font-family: 'Fraunces', serif;">
                    View Alumni Account
                </h1>
            </div>
        </div>
    </div>

    <!-- ========== FLASHES ========== -->
    @if (session('success'))
        <div class="mb-4 sm:mb-5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 sm:mb-5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 sm:mb-5 bg-[#D4A537]/10 dark:bg-[#D4A537]/15 border border-[#D4A537]/30 text-[#123524] dark:text-[#E5B94A] rounded-xl p-3 sm:p-4 text-xs sm:text-sm flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <span>You are viewing this alumni's information. Name, email, and school ID cannot be changed here.</span>
    </div>

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
        <form wire:submit="updateAlumni" class="space-y-4 sm:space-y-5">

            <!-- Names (Read-Only) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                <div class="min-w-0">
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        First Name
                    </label>
                    <input type="text" wire:model="first_name" disabled
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
                </div>

                <div class="min-w-0">
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Last Name
                    </label>
                    <input type="text" wire:model="last_name" disabled
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
                </div>
            </div>

            <!-- Middle Name (Read-Only) -->
            <div>
                <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Middle Name
                </label>
                <input type="text" wire:model="middle_name" disabled
                    class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
            </div>

            <!-- Full name preview -->
            @if ($this->fullName)
                <p class="text-[11px] sm:text-xs text-black/50 dark:text-white/50 -mt-2">
                    Full name: <span class="font-semibold text-[#123524] dark:text-white">{{ $this->fullName }}</span>
                </p>
            @endif

            <!-- Email (Read-Only) -->
            <div>
                <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Email
                </label>
                <input type="email" wire:model="email" disabled
                    class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
            </div>

            <!-- School ID (Read-Only) -->
            <div>
                <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    School ID
                </label>
                <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                    <input wire:model="school_year" type="text" disabled
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
                    <span class="text-black/40 dark:text-white/40 font-bold select-none">-</span>
                    <input wire:model="school_id_suffix" type="text" disabled
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-sm text-black/50 dark:text-white/50 cursor-not-allowed">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                <a href="{{ route('admin.alumni.view') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Back to Alumni List
                </a>
            </div>
        </form>
    </div>
</div>