<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- ===================== BACK ===================== -->
            <div class="mb-4 sm:mb-5">
                <a href="{{ route('super-admin.department.view') }}"
                    class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to departments
                </a>
            </div>

            <!-- ===================== CARD ===================== -->
            <div class="rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

                <!-- Header -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 sm:gap-4 border-b border-black/5 dark:border-white/5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Create Department
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Add a new department to the directory</p>
                    </div>
                </div>

                <!-- Flashes -->
                @if (session('success'))
                    <div class="mx-4 sm:mx-6 mt-4 bg-green-50 dark:bg-emerald-500/10 border border-green-200 dark:border-emerald-500/20 text-green-700 dark:text-emerald-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-4 sm:mx-6 mt-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit="create" class="p-4 sm:p-6 space-y-4 sm:space-y-5">

                    <!-- Name -->
                    <div>
                        <label for="dept_name"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Department Name
                        </label>
                        <input wire:model="dept_name" type="text" id="dept_name"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('dept_name')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                        @error('dept_name')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="dept_code"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Department Code
                        </label>
                        <input wire:model="dept_code" type="text" id="dept_code" placeholder="e.g. CS, BSIT"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('dept_code')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                        @error('dept_code')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="dept_desc"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Description
                        </label>
                        <textarea wire:model="dept_desc" id="dept_desc" rows="3"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('dept_desc')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror"></textarea>
                        @error('dept_desc')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label for="dept_logo"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Department Logo
                        </label>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <input wire:model="dept_logo" type="file" id="dept_logo"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full text-xs sm:text-sm text-black/70 dark:text-white/70
                                    file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0
                                    file:text-xs sm:file:text-sm file:font-semibold
                                    file:bg-[#123524] dark:file:bg-[#D4A537]
                                    file:text-white dark:file:text-[#123524]
                                    hover:file:bg-[#0d2819] dark:hover:file:bg-[#E5B94A]
                                    file:cursor-pointer cursor-pointer
                                    border border-black/10 dark:border-white/10 rounded-lg
                                    bg-white dark:bg-[#3A3B3C]">

                            {{-- Preview --}}
                            @if ($dept_logo)
                                <img src="{{ $dept_logo->temporaryUrl() }}" alt="Logo preview" loading="lazy"
                                    class="w-16 h-16 rounded-xl object-cover border border-black/10 dark:border-white/10 shrink-0">
                            @endif
                        </div>

                        <div wire:loading wire:target="dept_logo"
                            class="flex items-center gap-2 text-xs text-black/50 dark:text-white/50 mt-2">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Uploading…
                        </div>

                        @error('dept_logo')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Active toggle -->
                    <div class="flex items-center justify-between gap-3 bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 rounded-xl px-4 sm:px-5 py-4">
                        <div class="pr-3 min-w-0">
                            <p class="font-semibold text-sm text-black dark:text-white">Active</p>
                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">
                                Inactive departments won't be selectable for new courses or alumni records.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                        <button type="submit" wire:loading.attr="disabled" wire:target="create,dept_logo"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="create">Create Department</span>
                            <span wire:loading wire:target="create">Creating…</span>
                            <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('super-admin.department.view') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>