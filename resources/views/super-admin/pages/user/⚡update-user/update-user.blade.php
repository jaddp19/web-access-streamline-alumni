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
                    <p class="text-white/50 text-xs sm:text-sm">Registrar</p>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate"
                        style="font-family: 'Fraunces', serif;">
                        Update User
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

        <!-- ===================== FORM CARD ===================== -->
        <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
            <form wire:submit="update" class="space-y-4 sm:space-y-5">

                <!-- Names -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div class="min-w-0">
                        <label for="first_name"
                            class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                            First Name
                        </label>
                        <input wire:model="first_name" type="text" id="first_name" autocomplete="given-name" placeholder="Juan"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('first_name') border-red-400 dark:border-red-500/50 @enderror">
                        @error('first_name')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label for="last_name"
                            class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                            Last Name
                        </label>
                        <input wire:model="last_name" type="text" id="last_name" autocomplete="family-name" placeholder="Dela Cruz"
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
                    <input wire:model="middle_name" type="text" id="middle_name" autocomplete="additional-name" placeholder="Martinez"
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
                    <input wire:model="email" type="email" id="email" autocomplete="email" placeholder="juandelacruz@gmail.com"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('email') border-red-400 dark:border-red-500/50 @enderror">
                    @error('email')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- School ID -->
                <div>
                    <label for="school_year"
                        class="block mb-2 text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        School ID
                    </label>
                    <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                        {{-- Year dropdown --}}
                        <select wire:model="school_year" id="school_year"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('school_year') border-red-400 dark:border-red-500/50 @enderror">
                            <option value="" disabled>Year</option>
                            @foreach ($this->batchYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>

                        {{-- Dash --}}
                        <span class="text-black/40 dark:text-white/40 font-bold select-none">-</span>

                        {{-- 4-digit suffix --}}
                        <input wire:model="school_id_suffix" type="text" id="school_id_suffix"
                            inputmode="numeric" autocomplete="off"
                            maxlength="4" placeholder="0000"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition @error('school_id_suffix') border-red-400 dark:border-red-500/50 @enderror">
                    </div>

                    @error('school_year')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                    @error('school_id_suffix')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===================== ROLE ===================== -->
                <div class="pt-2 border-t border-black/5 dark:border-white/10">
                    <h2 class="mt-4 mb-1 text-sm font-bold text-[#123524] dark:text-white">
                        Assign Role
                    </h2>
                    <p class="text-xs text-black/50 dark:text-white/50 mb-3">
                        {{ $this->isSelf ? "You can't remove your own role." : 'Select one role for this user.' }}
                    </p>

                    @error('selectedRole')
                        <span class="block mb-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror

                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @forelse ($this->roles as $role)
                            @php
                                $roleName = is_string($role) ? $role : $role->name;
                                $isActive = $selectedRole === $roleName;
                            @endphp
                            <label wire:key="role-{{ \Illuminate\Support\Str::slug($roleName) }}"
                                class="flex items-center p-3 w-full bg-[#F1EFE7] dark:bg-[#3A3B3C] border rounded-xl text-sm transition cursor-pointer
                                {{ $isActive
                                    ? 'border-[#D4A537] ring-1 ring-[#D4A537]/40'
                                    : 'border-black/5 dark:border-white/5 hover:border-[#D4A537]' }}
                                {{ $this->isSelf && $isActive ? 'opacity-60 cursor-not-allowed' : '' }}">
                                <input type="radio" name="role" value="{{ $roleName }}"
                                    wire:model.live="selectedRole"
                                    @disabled($this->isSelf && $isActive)
                                    class="shrink-0 size-4 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526] disabled:opacity-60">
                                <span class="ms-3 text-black dark:text-white font-medium truncate">
                                    {{ \Illuminate\Support\Str::ucfirst($roleName) }}
                                </span>
                            </label>
                        @empty
                            <p class="text-black/50 dark:text-white/50 text-sm col-span-full">No roles available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- ===================== RESET PASSWORD ===================== -->
                <div class="pt-2 border-t border-black/5 dark:border-white/10">
                    <label class="mt-4 flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model.live="resetPassword"
                            class="mt-0.5 shrink-0 size-4 rounded text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526] border-black/20 dark:border-white/20">
                        <span class="min-w-0">
                            <span class="block text-sm font-bold text-[#123524] dark:text-white group-hover:text-[#D4A537] transition">
                                Reset Password
                            </span>
                            <span class="block text-xs text-black/50 dark:text-white/50 mt-0.5">
                                Replaces the current password with the default password for the selected role.
                            </span>
                        </span>
                    </label>

                    @if ($resetPassword)
                        @if ($this->generatedPassword)
                            <div class="mt-3 rounded-xl border border-[#D4A537]/40 bg-[#D4A537]/5 dark:bg-[#D4A537]/10 p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
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
                                            The user should change this after their next login.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 p-3 sm:p-4">
                                <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-400">
                                    Select a role above first — the password is generated from it.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- ===================== ACTIONS ===================== -->
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                    <button type="submit" wire:loading.attr="disabled" wire:target="update"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="update">Update</span>
                        <span wire:loading wire:target="update">Updating…</span>
                        <svg wire:loading.remove wire:target="update" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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