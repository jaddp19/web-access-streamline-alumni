<div>
    <div class="max-w-3xl w-full px-3 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-14 mx-auto">

        <!-- ===================== BACK ===================== -->
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
            <div class="absolute -right-10 -top-10 w-32 sm:w-48 h-32 sm:h-48 rounded-full bg-[#D4A537]/10"></div>
            <div class="absolute -right-4 top-14 sm:top-16 w-16 sm:w-24 h-16 sm:h-24 rounded-full bg-[#D4A537]/10"></div>

            <div class="relative flex items-center gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-white/50 text-xs sm:text-sm">Super Admin</p>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate"
                        style="font-family: 'Fraunces', serif;">
                        Create User
                    </h1>
                </div>
            </div>
        </div>

        <!-- ===================== FLASHES ===================== -->
        @if (session('success'))
            <div class="mb-4 sm:mb-5 bg-green-50 dark:bg-emerald-500/10 border border-green-200 dark:border-emerald-500/20 text-green-700 dark:text-emerald-400 font-semibold rounded-xl p-3 sm:p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 sm:mb-5 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-400 font-semibold rounded-xl p-3 sm:p-4 text-sm">
                {{ session('warning') }}
            </div>
        @endif

        <div class="mb-4 sm:mb-5 bg-[#D4A537]/10 dark:bg-[#D4A537]/15 border border-[#D4A537]/30 text-[#123524] dark:text-[#E5B94A] rounded-xl p-3 sm:p-4 text-xs sm:text-sm flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <span>The email below is used both to log in and to send this account's "account created" notification.</span>
        </div>

        <!-- ===================== FORM CARD ===================== -->
        <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
            <form wire:submit="create" class="space-y-4 sm:space-y-5">

                <!-- Names: stack on mobile, side-by-side from sm -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <!-- First Name -->
                    <div class="min-w-0">
                        <label for="first_name"
                            class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                            First Name
                        </label>
                        <input wire:model="first_name" type="text" id="first_name" autocomplete="given-name"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('first_name') border-red-400 dark:border-red-500/50 @enderror">
                        @error('first_name')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="min-w-0">
                        <label for="last_name"
                            class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                            Last Name
                        </label>
                        <input wire:model="last_name" type="text" id="last_name" autocomplete="family-name"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('last_name') border-red-400 dark:border-red-500/50 @enderror">
                        @error('last_name')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Middle Name -->
                <div>
                    <label for="middle_name"
                        class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        Middle Name
                        <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                    </label>
                    <input wire:model="middle_name" type="text" id="middle_name" autocomplete="additional-name"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('middle_name') border-red-400 dark:border-red-500/50 @enderror">
                    @error('middle_name')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email"
                        class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        Email
                    </label>
                    <input wire:model="email" type="email" id="email" autocomplete="email"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('email') border-red-400 dark:border-red-500/50 @enderror">
                    @error('email')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- School ID -->
                <div>
                    <label for="school_id"
                        class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        School ID
                    </label>
                    <input wire:model="school_id" type="text" id="school_id" inputmode="numeric"
                        autocomplete="off"
                        oninput="this.value = this.value.replace(/[^0-9\-]/g, '').slice(0, 9)"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('school_id') border-red-400 dark:border-red-500/50 @enderror">
                    @error('school_id')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===================== ROLE ===================== -->
                <div class="pt-1 sm:pt-2">
                    <h2 class="mb-1 text-sm font-bold text-[#123524] dark:text-white">
                        Assign Role <span class="text-red-500 dark:text-red-400">*</span>
                    </h2>
                    <p class="text-xs text-black/50 dark:text-white/50 mb-3">
                        The password is generated automatically based on the selected role.
                    </p>

                    @error('selectedRole')
                        <span class="block mb-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror

                    {{-- Responsive role tiles: 1 col → 2 col (sm) → 3 col (lg) --}}
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @forelse ($this->roles as $role)
                            @php
                                // Handle both: string roles OR Role models
                                $roleName = is_string($role) ? $role : $role->name;
                                $isActive = $selectedRole === $roleName;
                            @endphp
                            <label wire:key="role-{{ Str::slug($roleName) }}"
                                class="flex items-center p-3 w-full bg-[#F1EFE7] dark:bg-[#3A3B3C] border rounded-xl text-sm transition cursor-pointer
                                {{ $isActive
                                    ? 'border-[#D4A537] ring-1 ring-[#D4A537]/40'
                                    : 'border-black/5 dark:border-white/5 hover:border-[#D4A537]' }}">
                                <input type="radio" name="role" value="{{ $roleName }}"
                                    wire:model.live="selectedRole"
                                    class="shrink-0 size-4 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526]">
                                <span class="ms-3 text-black dark:text-white font-medium truncate">
                                    {{ Str::ucfirst($roleName) }}
                                </span>
                            </label>
                        @empty
                            <p class="text-black/50 dark:text-white/50 text-sm col-span-full">No Roles Found</p>
                        @endforelse
                    </div>
                </div>

                <!-- ===================== PASSWORD PREVIEW ===================== -->
                @if ($this->generatedPassword)
                    <div class="rounded-xl border border-[#D4A537]/40 bg-[#D4A537]/5 dark:bg-[#D4A537]/10 p-3 sm:p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Generated Password
                                </p>
                                <p class="font-mono text-sm sm:text-base font-bold text-[#123524] dark:text-[#E5B94A] mt-1 break-all">
                                    {{ $this->generatedPassword }}
                                </p>
                                <p class="text-[11px] sm:text-xs text-black/50 dark:text-white/50 mt-1.5">
                                    This will be emailed to {{ $email ?: 'the new user' }}. They should change it after first login.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] p-4 text-center">
                        <p class="text-xs text-black/50 dark:text-white/50 italic">
                            Select a role above to generate the password.
                        </p>
                    </div>
                @endif

                <!-- ===================== ACTIONS ===================== -->
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                    <button type="submit" wire:loading.attr="disabled" wire:target="create"
                        wire:loading.attr="aria-busy"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="create">Create</span>
                        <span wire:loading wire:target="create">Creating…</span>
                        <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                    <a href="{{ route('super-admin.user.view') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>