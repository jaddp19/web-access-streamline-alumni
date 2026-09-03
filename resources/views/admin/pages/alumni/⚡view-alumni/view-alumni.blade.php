<div>
    <!-- Table Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Card -->
        <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm">

            <!-- Header -->
            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-black/5">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Alumni</h2>
                        <p class="text-sm text-black/50">Manage alumni records</p>
                    </div>
                </div>

                <a href="{{ route('admin.alumni.create') }}"
                    class="inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#123524] text-white hover:bg-[#0d2819] transition py-2.5 px-5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Alumni
                </a>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] border-b border-black/5">
                        <tr>
                            <th class="ps-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Name</th>
                            <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Email</th>
                            <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Degree Program</th>
                            <th class="hidden lg:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Department</th>
                            <th class="hidden md:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Batch Year</th>
                            <th class="px-2 sm:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                        @forelse ($this->educationalBackgrounds as $profile)
                            <tr class="hover:bg-black/[0.02] transition-colors">
                                <td class="px-2 sm:px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($profile->avatar)
                                            <img src="{{ Storage::url($profile->avatar) }}" alt="{{ $profile->user->name ?? 'Alumni' }}"
                                                class="w-8 h-8 rounded-full object-cover shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#123524]/10 flex items-center justify-center text-[#123524] text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($profile->user->name ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-semibold text-[#123524]">{{ $profile->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/70">{{ $profile->user->email ?? 'N/A' }}</span>
                                </td>
                                <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/70">
                                        {{ $profile->courses->pluck('course_title')->join(', ') ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/70">
                                        {{ $profile->courses->pluck('department.dept_name')->filter()->unique()->join(', ') ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-2 sm:px-6 py-3">
                                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 text-[#a97f1f] font-semibold">
                                        {{ $profile->batch->batch_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-6 py-3 text-end">
                                    @if ($profile->user)
                                        <a href="{{ route('admin.alumni.update', $profile->user->id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] hover:text-[#0d2819] font-semibold hover:underline">
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 text-sm mb-4">No alumni found.</p>
                                    <a href="{{ route('admin.alumni.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] text-white text-sm font-semibold hover:bg-[#0d2819] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Create the first alumni account
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-black/5">
                <p class="text-sm text-black/60">
                    <span class="font-semibold text-[#123524]">{{ $this->educationalBackgrounds->total() }}</span> results
                </p>

                <div class="inline-flex gap-x-2">
                    @if ($this->educationalBackgrounds->onFirstPage())
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @endif

                    @if ($this->educationalBackgrounds->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @else
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>