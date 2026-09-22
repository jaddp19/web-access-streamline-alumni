<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- ===================== BACK ===================== -->
            <div class="mb-4 sm:mb-5">
                <a href="{{ route('super-admin.courses.view') }}"
                    class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to courses
                </a>
            </div>

            <!-- ===================== CARD ===================== -->
            <div class="rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

                <!-- Header -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 sm:gap-4 border-b border-black/5 dark:border-white/5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Update Course
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50 truncate">
                            Editing {{ $course->course_title }}
                        </p>
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
                <form wire:submit="update" class="p-4 sm:p-6 space-y-4 sm:space-y-5">

                    <!-- Course Name -->
                    <div>
                        <label for="course_title"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Course Name
                        </label>
                        <input type="text" wire:model="course_title" id="course_title"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('course_title')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                        @error('course_title')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Course Code -->
                    <div>
                        <label for="course_code"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Course Code
                        </label>
                        <input type="text" wire:model="course_code" id="course_code"
                            placeholder="e.g. BSIT, BSCS"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('course_code')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                        @error('course_code')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="course_desc"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Description
                        </label>
                        <textarea wire:model="course_desc" id="course_desc" rows="3"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('course_desc')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror"></textarea>
                        @error('course_desc')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Department
                        </label>
                        <select wire:model="department_id" id="department_id"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('department_id')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                            <option value="">Choose Department</option>
                            @forelse ($this->departments as $department)
                                <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                            @empty
                                <option value="" disabled>No active departments</option>
                            @endforelse
                        </select>
                        @error('department_id')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Course Type -->
                    <div>
                        <label for="course_type"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Course Type
                        </label>
                        <select wire:model="course_type" id="course_type"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                            <option value="non-board">Non-board</option>
                            <option value="board">Board</option>
                        </select>
                        @error('course_type')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Active toggle -->
                    <div class="flex items-center justify-between gap-3 bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 rounded-xl px-4 sm:px-5 py-4">
                        <div class="pr-3 min-w-0">
                            <p class="font-semibold text-sm text-black dark:text-white">Active</p>
                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">
                                Inactive courses won't be selectable for alumni education records.
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
                        <button type="submit" wire:loading.attr="disabled" wire:target="update"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="update">Update Course</span>
                            <span wire:loading wire:target="update">Updating…</span>
                            <svg wire:loading.remove wire:target="update" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('super-admin.courses.view') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>