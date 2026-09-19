<div>
    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

        <!-- Back Button -->
        <div class="mb-5">
            <a href="{{ route('super-admin.user.view') }}"
                class="inline-flex items-center gap-x-2 px-3.5 py-2 text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 15l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-3xl p-8 mb-5">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
            <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

            <div class="relative flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-white/50 text-sm">Super Admin</p>
                    <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Create User</h1>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-5 bg-green-50 dark:bg-emerald-500/10 border border-green-200 dark:border-emerald-500/20 text-green-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div
            class="mb-5 bg-[#D4A537]/10 dark:bg-[#D4A537]/15 border border-[#D4A537]/30 dark:border-[#D4A537]/30 text-[#123524] dark:text-[#E5B94A] rounded-xl p-4 text-sm flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <span>The email below is used both to log in and to send this account's "account created"
                notification.</span>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-3xl p-8">
            <form wire:submit.prevent='create' class="space-y-5">

                <!-- Name (first / middle / last) -->
                <div class="grid sm:grid-cols-2 gap-5">
                    <!-- First Name -->
                    <div>
                        <label for="hs-first-name"
                            class="block mb-2 text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">First
                            Name</label>
                        <input wire:model.defer="first_name" type="text" id="hs-first-name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                        @error('first_name')
                            <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="hs-last-name"
                            class="block mb-2 text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">Last
                            Name</label>
                        <input wire:model.defer="last_name" type="text" id="hs-last-name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                        @error('last_name')
                            <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Middle Name (full width, optional) -->
                <div>
                    <label for="hs-middle-name"
                        class="block mb-2 text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        Middle Name <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                    </label>
                    <input wire:model.defer="middle_name" type="text" id="hs-middle-name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                    @error('middle_name')
                        <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="hs-email"
                        class="block mb-2 text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">Email</label>
                    <input wire:model.defer='email' type="email" name="hs-email" id="hs-email"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                    @error('email')
                        <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- School ID -->
                <div>
                    <label for="hs-school_id"
                        class="block mb-2 text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold">
                        School ID
                    </label>
                    <input wire:model.defer="school_id" type="text" name="hs-school_id" id="hs-school_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition"
                        required pattern="[0-9\-]+" inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9\-]/g, '')">
                    @error('school_id')
                        <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div class="pt-2">
                    <h2 class="mb-1 text-sm font-bold text-[#123524] dark:text-white">
                        Assign Role <span class="text-red-500 dark:text-red-400">*</span>
                    </h2>
                    <p class="text-xs text-black/50 dark:text-white/50 mb-3">
                        The password is generated automatically based on the selected role.
                    </p>

                    @error('selectedRole')
                        <div>
                            <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="mt-3 grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
                        @forelse ($this->roles as $role)
                            <label
                                class="flex items-center p-3 w-full bg-[#F1EFE7] dark:bg-[#3A3B3C] border rounded-xl text-sm transition cursor-pointer
                                {{ $selectedRole === $role->name ? 'border-[#D4A537] ring-1 ring-[#D4A537]/40' : 'border-black/5 dark:border-white/5 hover:border-[#D4A537]' }}">
                                <input type="radio" name="role" value="{{ $role->name }}"
                                    wire:model.live="selectedRole"
                                    class="shrink-0 size-4 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526]">
                                <span class="ms-3 text-black dark:text-white font-medium">
                                    {{ Str::ucfirst($role->name) }}
                                </span>
                            </label>
                        @empty
                            <p class="text-black/50 dark:text-white/50 text-sm">No Roles Found</p>
                        @endforelse
                    </div>
                </div>

                <!-- Generated password preview -->
                @if ($this->generatedPassword)
                    <div class="rounded-xl border border-[#D4A537]/40 dark:border-[#D4A537]/40 bg-[#D4A537]/5 dark:bg-[#D4A537]/10 p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Generated Password
                                </p>
                                <p class="font-mono text-base font-bold text-[#123524] dark:text-[#E5B94A] mt-1 break-all">
                                    {{ $this->generatedPassword }}
                                </p>
                                <p class="text-xs text-black/50 dark:text-white/50 mt-1.5">
                                    This will be emailed to {{ $email ?: 'the new user' }}. They should change it after
                                    first login.
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

                <!-- Actions -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                    <button type="submit" wire:loading.attr="disabled" wire:target="create"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="create">Create</span>
                        <span wire:loading wire:target="create">Creating…</span>
                        <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                    <a href="{{ route('super-admin.user.view') }}"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>