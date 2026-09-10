<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('super-admin.courses.view') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to courses
                </a>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-black/5 bg-white shadow-sm">

                <!-- Header -->
                <div class="px-6 py-5 flex items-center gap-4 border-b border-black/5">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Update Course</h2>
                        <p class="text-sm text-black/50">Editing {{ $course->course_title }}</p>
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="update" class="p-6 space-y-5">

                    <!-- Course Name -->
                    <div>
                        <label for="course_title" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Course Name</label>
                        <input type="text" wire:model.defer="course_title" id="course_title"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('course_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="course_desc" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Description</label>
                        <textarea wire:model.defer="course_desc" id="course_desc" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"></textarea>
                        @error('course_desc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Course Code -->
                    <div>
                        <label for="course_code" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Course Code</label>
                        <input type="text" wire:model.defer="course_code" id="course_code"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('course_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Department</label>
                        <select wire:model.defer="department_id" id="department_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                            <option value="">Choose Department</option>
                            @foreach ($this->departments as $department)
                                <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Course Type -->
                    <div>
                        <label for="course_type" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Course Type</label>
                        <select wire:model.defer="course_type" id="course_type"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                            <option value="non-board">Non-board</option>
                            <option value="board">Board</option>
                        </select>
                        @error('course_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Active toggle -->
                    <div class="flex items-center justify-between bg-[#F7F5EF] border border-black/10 rounded-xl px-5 py-4">
                        <div class="pr-4">
                            <p class="font-semibold text-sm text-black">Active</p>
                            <p class="text-xs text-black/60 mt-0.5">Inactive courses won't be selectable for alumni education records.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model.defer="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-black/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3 pt-2 border-t border-black/5">
                        <button type="submit"
                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                            Update Course
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('super-admin.courses.view') }}"
                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
