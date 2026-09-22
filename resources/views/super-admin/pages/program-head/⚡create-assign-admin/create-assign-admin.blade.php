<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- ===================== BACK ===================== -->
            <div class="mb-4 sm:mb-5">
                <a href="{{ route('super-admin.assign.view') }}"
                    class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to program heads
                </a>
            </div>

            <!-- ===================== CARD ===================== -->
            <div class="rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

                <!-- Header -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 sm:gap-4 border-b border-black/5 dark:border-white/5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Assign Program Head
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Give a program head oversight of a department</p>
                    </div>
                </div>

                <!-- ===================== FLASHES ===================== -->
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

                <!-- ===================== FORM ===================== -->
                <form wire:submit="create" class="p-4 sm:p-6 space-y-4 sm:space-y-5">

                    <!-- User -->
                    <div>
                        <label for="user_id"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Select User
                        </label>
                        <select wire:model.live="user_id" id="user_id"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('user_id')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                            <option value="">Choose User</option>
                            @forelse ($this->users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                            @empty
                                <option value="" disabled>No program head users found</option>
                            @endforelse
                        </select>
                        @error('user_id')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Select Department
                        </label>
                        <select wire:model.live="department_id" id="department_id"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('department_id')
                                border-red-400 dark:border-red-500/50
                            @else
                                border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                            <option value="">Choose Department</option>
                            @foreach ($this->departments as $department)
                                <option value="{{ $department['id'] }}">{{ $department['dept_name'] }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ===================== REASSIGN WARNING ===================== -->
                    @if ($this->isReassigning)
                        <div class="rounded-xl border border-amber-300 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 p-3 sm:p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-700 dark:text-amber-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs sm:text-sm font-semibold text-amber-800 dark:text-amber-400">
                                        This department already has a program head.
                                    </p>
                                    <p class="text-xs text-amber-700 dark:text-amber-500 mt-0.5">
                                        Currently assigned:
                                        <span class="font-semibold">{{ $this->currentDepartmentHeadName }}</span>
                                    </p>

                                    <label class="mt-3 flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model.live="confirmReassign"
                                            class="mt-0.5 shrink-0 size-4 rounded text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#242526] border-black/30 dark:border-white/30">
                                        <span class="text-xs text-amber-800 dark:text-amber-400">
                                            Yes, replace the current program head with the selected user.
                                        </span>
                                    </label>
                                    @error('confirmReassign')
                                        <span class="block mt-1 text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ===================== INFO ===================== -->
                    <div class="rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] p-3 sm:p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#123524]/10 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                        <p class="text-xs text-black/60 dark:text-white/60">
                            The selected user must already hold the <span class="font-semibold">Program Head</span> role.
                            Assigning a department replaces any existing program head for that department.
                        </p>
                    </div>

                    <!-- ===================== ACTIONS ===================== -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                        <button type="submit" wire:loading.attr="disabled" wire:target="create"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="create">Assign</span>
                            <span wire:loading wire:target="create">Assigning…</span>
                            <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('super-admin.assign.view') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>