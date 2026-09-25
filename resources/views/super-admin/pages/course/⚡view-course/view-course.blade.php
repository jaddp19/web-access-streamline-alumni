<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay -->
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,deleteSelected"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/70 dark:bg-[#242526]/70 pt-24 pointer-events-none">
                <svg class="w-6 h-6 animate-spin text-[#123524] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>

            <!-- ===================== HEADER ===================== -->
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col gap-3 lg:flex-row lg:justify-between lg:items-center border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            All Courses
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage all courses</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('super-admin.course.create') }}"
                        class="w-full sm:w-auto justify-center py-2 px-3.5 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Add Course
                    </a>
                </div>
            </div>

            <!-- ===================== BULK ACTION BAR ===================== -->
            @if ($this->selectedCount > 0)
                <div class="px-4 sm:px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs sm:text-sm text-red-700 dark:text-red-400 font-medium">
                        <span class="font-bold">{{ $this->selectedCount }}</span> course(s) selected
                    </p>
                    <button x-data
                        @click="
                            if (confirm('Are you sure you want to delete {{ $this->selectedCount }} course(s)?')) {
                                $wire.deleteSelected()
                            }
                        "
                        class="w-full sm:w-auto px-4 py-1.5 bg-red-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete Selected
                    </button>
                </div>
            @endif

            <!-- ===================== MOBILE CARD LIST ===================== -->
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @forelse ($this->programs as $program)
                    <div wire:key="mobile-course-{{ $program->id }}" class="p-4 flex items-start gap-3">
                        <input type="checkbox"
                            wire:key="mobile-cb-{{ $program->id }}-{{ $this->isRowSelected($program->id) ? '1' : '0' }}"
                            wire:click="toggleRowSelection({{ $program->id }})"
                            @checked($this->isRowSelected($program->id))
                            class="mt-1.5 rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] shrink-0">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-[10px] font-bold shrink-0">
                                        {{ $program->course_code }}
                                    </div>
                                    <p class="font-semibold text-[#123524] dark:text-white truncate">{{ $program->course_title }}</p>
                                </div>
                                <a href="{{ route('super-admin.course.update', $program->id) }}"
                                    class="shrink-0 text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                                    Edit
                                </a>
                            </div>
                            <p class="text-xs text-black/60 dark:text-white/60 mt-1 truncate">
                                {{ $program->department->dept_name ?? 'N/A' }}
                            </p>
                            <p class="mt-1 text-[11px] text-black/40 dark:text-white/40">
                                {{ $program->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                            </svg>
                        </div>
                        <p class="text-black/40 dark:text-white/40 text-sm">No courses found.</p>
                    </div>
                @endforelse

                @if ($this->programs->count() > 0)
                    <div class="px-4 py-3">
                        <button type="button" wire:click="toggleSelectAllOnPage"
                            class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                            {{ $selectAllOnPage ? 'Deselect this page' : 'Select this page' }}
                        </button>
                    </div>
                @endif
            </div>

            <!-- ===================== TABLE (sm+) ===================== -->
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 w-4">
                                <input type="checkbox"
                                    wire:key="header-course-cb-{{ $this->programs->total() }}"
                                    wire:click="toggleSelectAll"
                                    x-data
                                    x-effect="
                                        const total = {{ $this->programs->total() }};
                                        const selCount = $wire.selectAllFiltered ? total : ($wire.selectedPrograms || []).length;
                                        $el.checked = total > 0 && selCount >= total;
                                        $el.indeterminate = selCount > 0 && selCount < total;
                                    "
                                    title="Select all courses (all pages)"
                                    class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C]">
                            </th>
                            <th class="px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Course Name</th>
                            <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Department</th>
                            <th class="hidden lg:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Created</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->programs as $program)
                            <tr wire:key="row-course-{{ $program->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="w-4 ps-4 sm:ps-6 py-3 text-center align-middle">
                                    <input type="checkbox"
                                        wire:key="row-cb-{{ $program->id }}-{{ $this->isRowSelected($program->id) ? '1' : '0' }}"
                                        wire:click="toggleRowSelection({{ $program->id }})"
                                        @checked($this->isRowSelected($program->id))
                                        class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] align-middle">
                                </td>
                                <td class="px-3 lg:px-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-[10px] font-bold shrink-0">
                                            {{ $program->course_code }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate">{{ $program->course_title }}</span>
                                            <span class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">
                                                {{ $program->department->dept_name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/70 dark:text-white/70 block truncate">{{ $program->department->dept_name ?? 'N/A' }}</span>
                                </td>
                                <td class="hidden lg:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">{{ $program->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <a href="{{ route('super-admin.course.update', $program->id) }}"
                                        class="inline-flex items-center gap-1 text-[#123524] dark:text-[#D4A537] hover:text-[#0d2819] dark:hover:text-[#E5B94A] font-semibold hover:underline whitespace-nowrap">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">No courses found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ===================== FOOTER ===================== -->
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                <p class="text-xs sm:text-sm text-black/60 dark:text-white/60 text-center sm:text-left">
                    Showing
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->programs->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->programs->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->programs->total() }}</span>
                    results
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->programs->onFirstPage())
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15l-6-6 6-6" /></svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15l-6-6 6-6" /></svg>
                            Prev
                        </button>
                    @endif

                    <span class="sm:hidden text-xs font-semibold text-black/60 dark:text-white/60 whitespace-nowrap">
                        {{ $this->programs->currentPage() }} / {{ $this->programs->lastPage() }}
                    </span>

                    @if ($this->programs->hasMorePages())
                        <button wire:click="nextPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 3l6 6-6 6" /></svg>
                        </button>
                    @else
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 3l6 6-6 6" /></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>