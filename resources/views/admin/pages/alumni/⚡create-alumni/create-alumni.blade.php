<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER ========== -->
    <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Admin</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Create Alumni Account</h1>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 font-semibold rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-5 bg-[#D4A537]/10 border border-[#D4A537]/30 text-[#123524] rounded-xl p-4 text-sm flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <span>Only the name, email, and batch are set here. The alumni will complete their own avatar, contact info, location, and course after logging in and using "Forgot Password" to set their password.</span>
    </div>

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="saveAlumni" class="space-y-5">

            <!-- Name -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Name</label>
                <input type="text" wire:model.defer="name"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Email</label>
                <input type="email" wire:model.defer="email"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Batch -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Batch</label>
                <select wire:model.defer="batch_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    <option value="">-- Select batch --</option>
                    @foreach ($this->batches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                    @endforeach
                </select>
                @error('batch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Create Alumni
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('admin.alumni.view') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>      
