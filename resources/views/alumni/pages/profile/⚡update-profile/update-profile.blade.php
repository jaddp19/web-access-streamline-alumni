<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER (BENTO STYLE) ========== -->
    <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Edit Profile</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Personal Information</h1>
            </div>
        </div>
    </div>

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="saveProfile" class="space-y-5">

            <!-- Name + Email -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Name</label>
                    <input type="text" wire:model.defer="name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                </div>
            </div>

            <!-- School ID (disabled) -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">School ID</label>
                <input type="text" value="{{ $school_id }}" disabled
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-black/5 text-black/50 cursor-not-allowed">
                <p class="text-xs text-black/40 mt-1.5">Your school ID cannot be changed. Contact the registrar if this is incorrect.</p>
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Gender</label>
                <select wire:model.defer="gender"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Phone Numbers -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Phone Number 1</label>
                    <input type="number" wire:model.defer="phone_number_1"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        placeholder="Enter 10-digit number"
                        inputmode="numeric" pattern="[0-9]*" min="0" step="1">
                    @error('phone_number_1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Phone Number 2</label>
                    <input type="number" wire:model.defer="phone_number_2"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        placeholder="Optional 10-digit number"
                        inputmode="numeric" pattern="[0-9]*" min="0" step="1">
                    @error('phone_number_2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Addresses -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Permanent Address</label>
                <textarea wire:model.defer="permanent_address" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"></textarea>
                @error('permanent_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Current Address</label>
                <textarea wire:model.defer="current_address" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"></textarea>
                @error('current_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Save Changes
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('alumni.profile') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                    Back
                </a>
            </div>
        </form>

        @if (session('success'))
            <div class="mt-6 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>
