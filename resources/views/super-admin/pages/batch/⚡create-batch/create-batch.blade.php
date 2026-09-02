<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-lg mx-auto">
            <!-- Card -->
            <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm p-6 sm:p-8">

                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('super-admin.batch.view') }}"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524]/70 hover:text-[#123524] transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        <span>Back to Batch Years</span>
                    </a>
                </div>

                <!-- Title -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Create Batch Year</h2>
                        <p class="text-sm text-black/50">Add a new alumni batch to the system</p>
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="create" class="space-y-5">
                    <div>
                        <label class="block text-xs uppercase tracking-wide font-semibold text-black/60 mb-2">Batch Year</label>
                        <input type="number" wire:model.defer="batch_year"
                            min="1900" max="{{ date('Y') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 bg-[#F7F5EF] text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
                            placeholder="e.g. 2026">
                        @error('batch_year')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-x-2 px-5 py-2.5 bg-[#123524] text-white text-sm font-semibold rounded-xl hover:bg-[#0d2819] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Create Batch
                    </button>
                </form>

                @if (session('success'))
                    <div class="mt-5 flex items-start gap-2.5 px-4 py-3 bg-[#123524]/5 border border-[#123524]/10 rounded-xl text-[#123524] text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
