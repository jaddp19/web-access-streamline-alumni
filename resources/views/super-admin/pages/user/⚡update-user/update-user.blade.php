<div>
    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

        <!-- Back Button -->
        <div class="mb-5">
            <a href="{{ route('super-admin.user.view') }}"
                class="inline-flex items-center gap-x-2 px-3.5 py-2 text-sm font-semibold rounded-lg bg-white border border-black/10 text-[#123524] hover:bg-black/5 transition">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 15l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
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
                    <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Update User</h1>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 text-green-700 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div
            class="mb-5 bg-[#D4A537]/10 border border-[#D4A537]/30 text-[#123524] rounded-xl p-4 text-sm flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <span>Leave the password fields blank to keep the current password unchanged.</span>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-black/10 rounded-3xl p-8">
            <form wire:submit='update' class="space-y-5">

                <!-- Name (first / middle / last) -->
                <div class="grid sm:grid-cols-2 gap-5">
                    <!-- First Name -->
                    <div>
                        <label for="hs-first-name"
                            class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">First
                            Name</label>
                        <input wire:model.defer="first_name" type="text" id="hs-first-name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('first_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="hs-last-name"
                            class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">Last
                            Name</label>
                        <input wire:model.defer="last_name" type="text" id="hs-last-name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('last_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Middle Name (full width, optional) -->
                <div>
                    <label for="hs-middle-name"
                        class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">
                        Middle Name <span class="text-black/40 text-[10px] font-normal normal-case">(optional)</span>
                    </label>
                    <input wire:model.defer="middle_name" type="text" id="hs-middle-name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('middle_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="hs-email"
                        class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">Email</label>
                    <input wire:model='email' type="email" name="hs-email" id="hs-email"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- School ID -->
                <div>
                    <label for="hs-school_id"
                        class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">
                        School ID
                    </label>
                    <input wire:model="school_id" type="text" name="hs-school_id" id="hs-school_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        required pattern="[0-9\-]+" inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9\-]/g, '')">
                    @error('school_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="hs-password"
                        class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">
                        New Password <span class="normal-case font-normal text-black/40">(optional)</span>
                    </label>
                    <input wire:model='password' type="password" name="hs-password" id="hs-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="hs-password_confirmation"
                        class="block mb-2 text-xs text-black/60 uppercase tracking-wide font-semibold">Confirm New
                        Password</label>
                    <input wire:model='password_confirmation' type="password" name="hs-password_confirmation"
                        id="hs-password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('password_confirmation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div class="pt-2">
                    <h2 class="mb-1 text-sm font-bold text-[#123524]">
                        Assign Role
                    </h2>
                    @error('selectedRole')
                        <div>
                            <span class="mt-2 text-sm text-red-600">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="mt-3 grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
                        @forelse ($this->roles as $role)
                            <label
                                class="flex items-center p-3 w-full bg-[#F1EFE7] border border-black/5 rounded-xl text-sm hover:border-[#D4A537] transition cursor-pointer">
                                <input type="checkbox" value="{{ $role->name }}" x-data x-init="$watch('$wire.selectedRole', value => {
                                    $el.checked = value === '{{ $role->name }}';
                                })"
                                    @checked($selectedRole === $role->name)
                                    @click="$wire.set('selectedRole', '{{ $role->name }}')"
                                    class="shrink-0 size-4 rounded-sm text-[#123524] focus:ring-[#123524]">
                                <span class="ms-3 text-black font-medium">
                                    {{ Str::ucfirst($role->name) }}
                                </span>
                            </label>
                        @empty
                            <p class="text-black/50 text-sm">No Roles Found</p>
                        @endforelse
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                    <button type="submit"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                        Update
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                    <a href="{{ route('super-admin.user.view') }}"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
