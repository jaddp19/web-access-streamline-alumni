<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER ========== -->
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Admin</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Create Alumni Account</h1>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-5 bg-[#D4A537]/10 dark:bg-[#D4A537]/15 border border-[#D4A537]/30 dark:border-[#D4A537]/30 text-[#123524] dark:text-[#E5B94A] rounded-xl p-4 text-sm flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <span>Only the name, email, and school ID are set here. The alumni will fill in their own batch, course, contact info, avatar, and location after logging in with the default password <strong>csav.alumni</strong>.</span>
    </div>

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-3xl p-8">
        <form wire:submit.prevent="saveAlumni" class="space-y-5">

            <!-- Name (first / middle / last) -->
            <div class="grid sm:grid-cols-2 gap-5">
                <!-- First Name -->
                <div>
                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">First Name</label>
                    <input type="text" wire:model.defer="first_name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                    @error('first_name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Last Name</label>
                    <input type="text" wire:model.defer="last_name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                    @error('last_name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Middle Name (optional) -->
            <div>
                <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Middle Name <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                </label>
                <input type="text" wire:model.defer="middle_name"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                @error('middle_name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Live preview of full name -->
            @if ($this->fullName)
                <div class="text-xs text-black/50 dark:text-white/50 -mt-2">
                    Full name: <span class="font-semibold text-[#123524] dark:text-white">{{ $this->fullName }}</span>
                </div>
            @endif

            <!-- Email -->
            <div>
                <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Email</label>
                <input type="email" wire:model.defer="email"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                @error('email') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- School ID -->
            <div>
                <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">School ID</label>
                <input type="text" wire:model.defer="school_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition"
                    pattern="[0-9\-]+" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9\-]/g, '')">
                @error('school_id') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password notice -->
            <div class="rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] p-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#D4A537]/20 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">Default Password</p>
                        <p class="font-mono text-base font-bold text-[#123524] dark:text-[#E5B94A] mt-1 break-all">csav.alumni</p>
                        <p class="text-xs text-black/50 dark:text-white/50 mt-1.5">
                            This will be emailed to the new alumni. They should change it after first login.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="saveAlumni"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveAlumni">Create Alumni</span>
                    <span wire:loading wire:target="saveAlumni">Creating…</span>
                    <svg wire:loading.remove wire:target="saveAlumni" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('admin.alumni.view') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>