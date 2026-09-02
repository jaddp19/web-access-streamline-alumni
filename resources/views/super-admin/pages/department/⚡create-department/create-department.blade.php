<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('super-admin.department.view') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to departments
                </a>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-black/5 bg-white shadow-sm">

                <!-- Header -->
                <div class="px-6 py-5 flex items-center gap-4 border-b border-black/5">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Create Department</h2>
                        <p class="text-sm text-black/50">Add a new department to the directory</p>
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="create" class="p-6 space-y-5">

                    <!-- Name -->
                    <div>
                        <label for="dept_name" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Department Name</label>
                        <input wire:model.defer="dept_name" type="text" id="dept_name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('dept_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="dept_code" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Department Code</label>
                        <input wire:model.defer="dept_code" type="text" id="dept_code" placeholder="e.g. CS, BSIT"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('dept_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="dept_desc" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Description</label>
                        <textarea wire:model.defer="dept_desc" id="dept_desc" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"></textarea>
                        @error('dept_desc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label for="dept_logo" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Department Logo</label>
                        <input wire:model="dept_logo" type="file" id="dept_logo" accept="image/*"
                            class="w-full text-sm text-black/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#123524] file:text-white hover:file:bg-[#0d2819] file:cursor-pointer cursor-pointer">
                        <div wire:loading wire:target="dept_logo" class="text-xs text-black/50 mt-1">Uploading...</div>
                        @if ($dept_logo)
                            <img src="{{ $dept_logo->temporaryUrl() }}" alt="Logo preview" class="mt-3 w-16 h-16 rounded-xl object-cover border border-black/10">
                        @endif
                        @error('dept_logo') <span class="block text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Active toggle -->
                    <div class="flex items-center justify-between bg-[#F7F5EF] border border-black/10 rounded-xl px-5 py-4">
                        <div class="pr-4">
                            <p class="font-semibold text-sm text-black">Active</p>
                            <p class="text-xs text-black/60 mt-0.5">Inactive departments won't be selectable for new courses or alumni records.</p>
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
                            Create Department
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('super-admin.department.view') }}"
                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
