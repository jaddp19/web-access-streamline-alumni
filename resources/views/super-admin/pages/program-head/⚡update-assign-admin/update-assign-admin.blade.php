<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- Back -->
            <div class="mb-4 sm:mb-5">
                <a href="{{ route('super-admin.assign.view') }}"
                    class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to Program Heads
                </a>
            </div>

            <!-- Card -->
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
                            Update Program Head
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Change the program head of this department</p>
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

                    <!-- Currently assigned -->
                    @if ($this->currentHeadName)
                        <div class="rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] p-3 sm:p-4 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#123524]/10 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] sm:text-xs text-black/50 dark:text-white/50 uppercase tracking-wide font-semibold">
                                    Currently Assigned
                                </p>
                                <p class="text-sm font-semibold text-[#123524] dark:text-white mt-0.5 truncate">
                                    {{ $this->currentHeadName }}
                                </p>
                            </div>
                        </div>
                    @endif

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

                        {{-- Informational notice: user will be moved from another department --}}
                        @if ($this->willBeMovedFrom)
                            <div class="mt-2 rounded-lg border border-blue-300 dark:border-blue-500/40 bg-blue-50 dark:bg-blue-500/10 p-2.5 text-xs text-blue-800 dark:text-blue-300 flex items-start gap-2">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                <span>
                                    This user is currently assigned to
                                    <span class="font-semibold">{{ $this->willBeMovedFrom['dept_name'] }}</span>
                                    and will be moved to this department.
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id"
                            class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Select Department
                        </label>
                        <select wire:model="department_id" id="department_id"
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

                    <!-- Info -->
                    <div class="rounded-xl bg-[#F1EFE7] dark:bg-[#3A3B3C] p-3 sm:p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#123524]/10 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#123524] dark:text-[#D4A537] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                        <p class="text-xs text-black/60 dark:text-white/60">
                            The selected user must already hold the <span class="font-semibold">Program Head</span> role.
                            Each user can only head one department — assigning them here will move them from any previous one.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                        <button type="submit"
                            wire:loading.attr="disabled" wire:target="update"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="update">Update</span>
                            <span wire:loading wire:target="update">Updating…</span>
                            <svg wire:loading.remove wire:target="update" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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