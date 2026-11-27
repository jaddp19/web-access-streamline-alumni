<div x-data="{ modalOpen: @entangle('viewingAssignmentId') }" 
     @keydown.escape.window="$wire.closeView()">
     
    <!-- Table Section -->
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <!-- Card -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            All Department Heads
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage all department heads</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @can('can_create')
                    <a href="{{ route('super-admin.assign.create') }}"
                        class="w-full sm:w-auto justify-center py-2 px-3.5 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Assign Department Head
                    </a>
                    @endcan
                </div>
            </div>

            <!-- ===================== BULK ACTION BAR ===================== -->
            @if ($this->selectedCount > 0)
                <div class="px-4 sm:px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs sm:text-sm text-red-700 dark:text-red-400 font-medium">
                        <span class="font-bold">{{ $this->selectedCount }}</span> assignment(s) selected
                    </p>
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        @if (! $selectAllFiltered)
                            <button type="button" wire:click="selectAllMatching"
                                class="w-full sm:w-auto px-4 py-1.5 bg-white dark:bg-[#3A3B3C] border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                                Select all {{ $this->totalProgramHeadsCount }}
                            </button>
                        @endif
                        <button x-data
                            @click="
                                if (confirm('Are you sure you want to remove {{ $this->selectedCount }} program head assignment(s)?')) {
                                    $wire.deleteSelected()
                                }
                            "
                            class="w-full sm:w-auto px-4 py-1.5 bg-red-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                            Remove Selected
                        </button>
                    </div>
                </div>
            @endif

            <!-- ===================== MOBILE CARD LIST (< sm) ===================== -->
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @can('can_view_any')
                @forelse ($this->programHeads as $row)
                    @php
                        $rowKey = $row->id . '-' . $row->program_head_id;
                        $isSelected = $selectAllFiltered
                            ? ! in_array($rowKey, $excludedProgramHeads, true)
                            : in_array($rowKey, $selectedProgramHeads, true);
                    @endphp
                    <div wire:key="mobile-row-{{ $row->id }}" class="p-4 flex items-start gap-3">
                        @can('can_delete')
                        <input type="checkbox"
                            wire:key="mobile-ph-cb-{{ $row->id }}-{{ $isSelected ? '1' : '0' }}"
                            wire:click="toggleRowSelection('{{ $rowKey }}')"
                            @checked($isSelected)
                            class="mt-1.5 rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] shrink-0">
                        @endcan
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-[#123524] dark:text-white truncate">
                                    {{ $row->programHead?->name ?? '—' }}
                                </p>
                                
                                {{-- 3-Dots Dropdown (Mobile) --}}
                                <div class="shrink-0 relative" x-data="{ open: false }">
                                    <button @click="open = !open" @click.outside="open = false"
                                        class="p-1 -mr-1 rounded-lg text-black/40 hover:text-black dark:text-white/40 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition focus:outline-none">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition x-cloak
                                        class="absolute right-0 top-full z-20 mt-1 w-32 origin-top-right rounded-xl bg-white dark:bg-[#242526] shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none divide-y divide-black/5 dark:divide-white/5">
                                        
                                        {{-- View Button --}}
                                        <button type="button" wire:click="openView({{ $row->id }})"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            View
                                        </button>

                                        @can('can_update')
                                            <a href="{{ route('super-admin.assign.update', $row->id) }}"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5 truncate">
                                {{ $row->dept_name ?? '—' }}
                            </p>

                            <p class="mt-1.5 text-[11px] text-black/40 dark:text-white/40">
                                Assigned {{ $row->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <p class="text-black/40 dark:text-white/40 text-sm">No program heads assigned yet.</p>
                    </div>
                @endforelse

                @can('can_delete')
                @if ($this->programHeads->count() > 0)
                    <div class="px-4 py-3">
                        <button type="button" wire:click="toggleSelectAll"
                            class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                            {{ $this->allSelected ? 'Deselect all' : 'Select all assignments' }}
                        </button>
                    </div>
                @endif
                @endcan
                @endcan
            </div>

            <!-- ===================== TABLE (sm and up) ===================== -->
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 w-4">
                                @can('can_delete')
                                <input type="checkbox"
                                    wire:key="header-ph-cb-{{ $this->totalProgramHeadsCount }}"
                                    wire:click="toggleSelectAll"
                                    x-data
                                    x-effect="
                                        const total = {{ $this->totalProgramHeadsCount }};
                                        const allFiltered = $wire.selectAllFiltered === true;
                                        const selCount = allFiltered
                                            ? total - ($wire.excludedProgramHeads || []).length
                                            : ($wire.selectedProgramHeads || []).length;
                                        $el.checked = total > 0 && selCount >= total;
                                        $el.indeterminate = selCount > 0 && selCount < total;
                                    "
                                    title="Select all assignments (all pages)"
                                    class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C]">
                                @endcan
                            </th>
                            <th class="px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Program Head
                            </th>
                            <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Department
                            </th>
                            <th class="hidden lg:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Assigned
                            </th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @can('can_view_any')
                        @forelse ($this->programHeads as $row)
                            @php
                                $rowKey = $row->id . '-' . $row->program_head_id;
                                $isSelected = $selectAllFiltered
                                    ? ! in_array($rowKey, $excludedProgramHeads, true)
                                    : in_array($rowKey, $selectedProgramHeads, true);
                            @endphp
                            <tr wire:key="row-{{ $row->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="w-4 ps-4 sm:ps-6 py-3 text-center align-middle">
                                    @can('can_delete')
                                    <input type="checkbox"
                                        wire:key="table-ph-cb-{{ $row->id }}-{{ $isSelected ? '1' : '0' }}"
                                        wire:click="toggleRowSelection('{{ $rowKey }}')"
                                        @checked($isSelected)
                                        class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] align-middle">
                                    @endcan
                                </td>
                                <td class="px-3 lg:px-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($row->programHead?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate">
                                                {{ $row->programHead?->name ?? '—' }}
                                            </span>
                                            <span class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">
                                                {{ $row->dept_name ?? '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/70 dark:text-white/70 block truncate">{{ $row->dept_name ?? '—' }}</span>
                                </td>
                                <td class="hidden lg:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">{{ $row->updated_at->diffForHumans() }}</span>
                                </td>
                                
                                {{-- 3-Dots Dropdown (Desktop) --}}
                                <td class="px-3 lg:px-6 py-3 text-end relative">
                                    <div x-data="{ open: false }" class="inline-block text-left">
                                        <button @click="open = !open" @click.outside="open = false"
                                            class="p-1.5 rounded-lg text-black/40 hover:text-black dark:text-white/40 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition focus:outline-none">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition x-cloak
                                            class="absolute right-0 z-20 mt-1 w-32 origin-top-right rounded-xl bg-white dark:bg-[#242526] shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none divide-y divide-black/5 dark:divide-white/5">
                                            
                                            {{-- View Button --}}
                                            <button type="button" wire:click="openView({{ $row->id }})"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                View
                                            </button>

                                            @can('can_update')
                                                <a href="{{ route('super-admin.assign.update', $row->id) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                    Edit
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">No program heads assigned yet.</p>
                                </td>
                            </tr>
                        @endforelse
                        @endcan
                    </tbody>
                </table>
            </div>

            <!-- ===================== FOOTER ===================== -->
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                @can('can_view_any')
                <p class="text-xs sm:text-sm text-black/60 dark:text-white/60 text-center sm:text-left">
                    Showing
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->firstItem }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->lastItem }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->totalProgramHeadsCount }}</span>
                    results
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->programHeads->onFirstPage())
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @endif

                    <span class="sm:hidden text-xs font-semibold text-black/60 dark:text-white/60 whitespace-nowrap">
                        {{ $this->programHeads->currentPage() }} / {{ $this->lastPage }}
                    </span>

                    @if ($this->programHeads->hasMorePages())
                        <button wire:click="nextPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @else
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @endif
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- ================= CENTERED SINGLE VIEW MODAL ================= --}}
    <template x-teleport="body">
        <div x-show="modalOpen" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            
            {{-- Backdrop --}}
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 @click="$wire.closeView()"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    
                    {{-- Modal Panel --}}
                    <div x-show="modalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#242526] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-black/5 dark:border-white/5">
                        
                        @if($this->viewingAssignment)
                            @php $assignment = $this->viewingAssignment; @endphp

                            {{-- Header --}}
                            <div class="px-4 py-5 sm:px-6 bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5 flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-[#123524] dark:text-white" id="modal-title" style="font-family: 'Fraunces', serif;">
                                        Assignment Details
                                    </h2>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">
                                            Active
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="$wire.closeView()" class="relative rounded-md bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-4 py-6 sm:px-6">
                                
                                {{-- Profile Header --}}
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-16 h-16 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-2xl font-bold shrink-0">
                                        {{ strtoupper(substr($assignment->programHead?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-[#123524] dark:text-white truncate">{{ $assignment->programHead?->name ?? '—' }}</h3>
                                        <p class="text-sm text-black/60 dark:text-white/60">{{ $assignment->programHead?->email ?? 'No email' }}</p>
                                    </div>
                                </div>

                                {{-- Details List --}}
                                <div class="border-t border-black/5 dark:border-white/5 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Department</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $assignment->dept_name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Assigned On</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $assignment->updated_at->format('M j, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="bg-[#F7F5EF] dark:bg-[#3A3B3C] px-4 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 border-t border-black/5 dark:border-white/5 rounded-b-2xl">
                                <button type="button" @click="$wire.closeView()"
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-white dark:bg-[#242526] px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    Close
                                </button>
                                @can('can_update')
                                <a href="{{ route('super-admin.assign.update', $assignment->id) }}" wire:navigate
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-[#123524] dark:bg-[#D4A537] px-4 py-2.5 text-sm font-semibold text-white dark:text-[#123524] shadow-sm hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                    Edit Assignment
                                </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>