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
                    Update Alumni Account
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

    @if (session('warning'))
        <div class="mb-4 sm:mb-5 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
            {{ session('warning') }}
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
        <span>Name, email, and school ID cannot be changed here. You can only activate/deactivate the account or reset the password.</span>
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

            <!-- ========== ACTIVE ALUMNI (Editable) ========== -->
            <div class="pt-2 border-t border-black/5 dark:border-white/10">
                <label class="mt-4 flex items-start gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="is_active"
                        class="mt-0.5 shrink-0 size-4 rounded text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526] border-black/20 dark:border-white/20">
                    <span class="min-w-0">
                        <span class="block text-sm font-bold text-[#123524] dark:text-white group-hover:text-[#D4A537] transition">
                            Active Alumni
                        </span>
                        <span class="block text-xs text-black/50 dark:text-white/50 mt-0.5">
                            When unchecked, this alumni cannot log in. Their data stays intact.
                        </span>
                    </span>
                </label>

                @if (! $is_active)
                    <div class="mt-3 rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 p-3">
                        <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-400">
                            This alumni will be signed out on their next request and will not be able to log back in.
                        </p>
                    </div>
                @endif
            </div>

            <!-- ========== RESET PASSWORD (Editable) ========== -->
            <div class="pt-2 border-t border-black/5 dark:border-white/10">
                <label class="mt-4 flex items-start gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="resetPassword"
                        class="mt-0.5 shrink-0 size-4 rounded text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526] border-black/20 dark:border-white/20">
                    <span class="min-w-0">
                        <span class="block text-sm font-bold text-[#123524] dark:text-white group-hover:text-[#D4A537] transition">
                            Reset Password
                        </span>
                        <span class="block text-xs text-black/50 dark:text-white/50 mt-0.5">
                            Replaces the current password with the default alumni password.
                        </span>
                    </span>
                </label>

                @if ($resetPassword)
                    <div class="mt-3 rounded-xl border border-[#D4A537]/40 bg-[#D4A537]/5 dark:bg-[#D4A537]/10 p-3 sm:p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    New Password
                                </p>
                                <p class="font-mono text-sm sm:text-base font-bold text-[#123524] dark:text-[#E5B94A] mt-1 break-all">
                                    {{ $this->generatedPassword }}
                                </p>
                                <p class="text-[11px] sm:text-xs text-black/50 dark:text-white/50 mt-1.5">
                                    The alumni should change this after their next login.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ========== ACTIONS ========== -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                <button type="submit" wire:loading.attr="disabled" wire:target="updateAlumni"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="updateAlumni">Update Status</span>
                    <span wire:loading wire:target="updateAlumni">Updating…</span>
                    <svg wire:loading.remove wire:target="updateAlumni" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('admin.alumni.view') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
