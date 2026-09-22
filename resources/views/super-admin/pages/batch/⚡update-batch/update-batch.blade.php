<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-lg mx-auto">

            <!-- ===================== CARD ===================== -->
            <div class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm p-4 sm:p-6 lg:p-8 overflow-hidden">

                <!-- Back -->
                <div class="mb-5 sm:mb-6">
                    <a href="{{ route('super-admin.batch.view') }}"
                        class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524]/70 dark:text-white/70 hover:text-[#123524] dark:hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        <span>Back to Batch Years</span>
                    </a>
                </div>

                <!-- Title -->
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] dark:text-[#D4A537] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Update Batch Year
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50 truncate">
                            Editing batch: {{ $batch->batch_name }}
                        </p>
                    </div>
                </div>

                <!-- Flashes -->
                @if (session('success'))
                    <div class="mb-4 sm:mb-5 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-[#123524]/5 dark:bg-emerald-500/10 border border-[#123524]/10 dark:border-emerald-500/20 rounded-xl text-[#123524] dark:text-emerald-400 text-xs sm:text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 sm:mb-5 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit="update" class="space-y-4 sm:space-y-5">
                    <div>
                        <label for="batch_year"
                            class="block text-[11px] sm:text-xs uppercase tracking-wide font-semibold text-black/60 dark:text-white/60 mb-2">
                            Batch Year
                        </label>
                        <input type="number" wire:model="batch_year" id="batch_year"
                            min="1900" max="{{ date('Y') }}"
                            placeholder="e.g. {{ date('Y') }}"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-[#123524] dark:text-white placeholder-[#123524]/30 dark:placeholder:text-white/30 focus:outline-none focus:ring-2 transition
                            @error('batch_year')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-[#123524]/15 dark:border-white/10 focus:ring-[#D4A537] focus:border-transparent
                            @enderror">
                        @error('batch_year')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs sm:text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        wire:loading.attr="disabled" wire:target="update"
                        class="w-full inline-flex items-center justify-center gap-x-2 px-5 py-2.5 bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold rounded-xl hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="update">Update Batch</span>
                        <span wire:loading wire:target="update">Updating…</span>
                        <svg wire:loading.remove wire:target="update" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>