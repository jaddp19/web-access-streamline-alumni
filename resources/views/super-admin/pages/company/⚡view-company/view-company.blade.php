<div x-data="{ modalOpen: @entangle('viewingCompanyId') }" 
     @keydown.escape.window="$wire.closeView()">
     
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay -->
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,deleteSelected,deleteCompany,clearSelection"
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
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            All Companies
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage all companies</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @can('can_create')
                    <a href="{{ route('super-admin.company.create') }}"
                        class="w-full sm:w-auto justify-center py-2 px-3.5 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Add Company
                    </a>
                    @endcan
                </div>
            </div>

            <!-- ===================== BULK ACTION BAR ===================== -->
            @if ($this->selectedCount > 0)
                <div class="px-4 sm:px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-xs sm:text-sm text-red-700 dark:text-red-400 font-medium">
                        <span class="font-bold">{{ $this->selectedCount }}</span> company(ies) selected
                    </div>

                    <button
                        wire:click="deleteSelected"
                        wire:confirm="Are you sure you want to delete {{ $this->selectedCount }} company(ies)?"
                        class="w-full sm:w-auto px-4 py-1.5 bg-red-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete Selected
                    </button>
                </div>
            @endif

            <!-- ===================== MOBILE CARD LIST ===================== -->
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @can('can_view_any')
                @forelse ($this->companies as $company)
                    <div wire:key="mobile-company-{{ $company->id }}" class="p-4 flex items-start gap-3">
                        @can('can_delete')
                        <input type="checkbox"
                            wire:key="mobile-company-cb-{{ $company->id }}-{{ $this->isRowSelected($company->id) ? '1' : '0' }}"
                            wire:click="toggleRowSelection({{ $company->id }})"
                            @checked($this->isRowSelected($company->id))
                            class="mt-1.5 rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] shrink-0">
                        @endcan
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-9 h-9 rounded-full bg-[#123524]/10 dark:bg-white/10 flex items-center justify-center text-[#123524] dark:text-white text-xs font-bold shrink-0 overflow-hidden">
                                        @if ($company->company_logo)
                                            <img src="{{ filter_var($company->company_logo, FILTER_VALIDATE_URL)
                                                ? $company->company_logo
                                                : Storage::url($company->company_logo) }}"
                                                alt="{{ $company->company_name }}" loading="lazy"
                                                class="w-full h-full object-contain">
                                        @else
                                            {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <p class="font-semibold text-[#123524] dark:text-white truncate">{{ $company->company_name }}</p>
                                </div>
                                
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
                                        <button type="button" wire:click="openView({{ $company->id }})"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            View
                                        </button>

                                        @can('can_update')
                                            <a href="{{ route('super-admin.company.update', $company->id) }}"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            @if ($company->company_address)
                                <p class="text-xs text-black/60 dark:text-white/60 mt-1 line-clamp-2">
                                    {{ $company->company_address }}
                                </p>
                            @endif

                            <p class="mt-1.5 text-[11px] text-black/40 dark:text-white/40">
                                {{ $company->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <p class="text-black/40 dark:text-white/40 text-sm">No companies found.</p>
                    </div>
                @endforelse
                @endcan

                @can('can_delete')
                @if ($this->companies->count() > 0)
                    <div class="px-4 py-3 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <button type="button" wire:click="toggleSelectAll"
                            class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                            {{ $selectAllFiltered ? 'Deselect all' : 'Select all companies' }}
                        </button>

                        @if ($this->selectedCount > 0 && ! $selectAllFiltered && $this->selectedCount < $this->totalCompaniesCount)
                            <button type="button" wire:click="selectAllMatching"
                                class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                                Select all {{ $this->totalCompaniesCount }}
                            </button>
                        @endif

                        @if ($this->selectedCount > 0)
                            <button type="button" wire:click="clearSelection"
                                class="text-xs font-semibold text-black/50 dark:text-white/50 hover:underline">
                                Clear selection
                            </button>
                        @endif
                    </div>
                @endif
                @endcan
            </div>

            <!-- ===================== TABLE (sm+) ===================== -->
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 w-4">
                                @can('can_delete')
                                <input type="checkbox"
                                    wire:key="header-company-cb-{{ $this->totalCompaniesCount }}"
                                    wire:click="toggleSelectAll"
                                    x-data
                                    x-effect="
                                        const total = {{ $this->totalCompaniesCount }};
                                        const allFiltered = $wire.selectAllFiltered === true;
                                        const selCount = allFiltered ? total : ($wire.selectedCompanies || []).length;
                                        $el.checked = total > 0 && selCount >= total;
                                        $el.indeterminate = selCount > 0 && selCount < total;
                                    "
                                    title="Select all companies (all pages)"
                                    class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C]">
                                @endcan
                            </th>
                            <th class="px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Company Name</th>
                            <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Address</th>
                            <th class="hidden lg:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Created</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @can('can_view_any')
                        @forelse ($this->companies as $company)
                            <tr wire:key="row-company-{{ $company->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="w-4 ps-4 sm:ps-6 py-3 text-center align-middle">
                                    @can('can_delete')
                                    <input type="checkbox"
                                        wire:key="row-company-cb-{{ $company->id }}-{{ $this->isRowSelected($company->id) ? '1' : '0' }}"
                                        wire:click="toggleRowSelection({{ $company->id }})"
                                        @checked($this->isRowSelected($company->id))
                                        class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] align-middle">
                                    @endcan
                                </td>
                                <td class="px-3 lg:px-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-white/10 flex items-center justify-center text-[#123524] dark:text-white text-xs font-bold shrink-0 overflow-hidden">
                                            @if ($company->company_logo)
                                                <img src="{{ filter_var($company->company_logo, FILTER_VALIDATE_URL)
                                                    ? $company->company_logo
                                                    : Storage::url($company->company_logo) }}"
                                                    alt="{{ $company->company_name }}" loading="lazy"
                                                    class="w-full h-full object-contain">
                                            @else
                                                {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate">{{ $company->company_name }}</span>
                                            <span class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">
                                                {{ $company->company_address ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/70 dark:text-white/70 block truncate max-w-[280px]">{{ $company->company_address ?? 'N/A' }}</span>
                                </td>
                                <td class="hidden lg:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">{{ $company->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 text-end relative">
                                    {{-- 3-Dots Dropdown (Desktop) --}}
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
                                            <button type="button" wire:click="openView({{ $company->id }})"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                View
                                            </button>

                                            @can('can_update')
                                                <a href="{{ route('super-admin.company.update', $company->id) }}"
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
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">No companies found.</p>
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
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->companies->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->companies->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->companies->total() }}</span>
                    results
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->companies->onFirstPage())
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
                        {{ $this->companies->currentPage() }} / {{ $this->companies->lastPage() }}
                    </span>

                    @if ($this->companies->hasMorePages())
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
                        
                        @if($this->viewingCompany)
                            @php $company = $this->viewingCompany; @endphp

                            {{-- Header --}}
                            <div class="px-4 py-5 sm:px-6 bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5 flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-[#123524] dark:text-white" id="modal-title" style="font-family: 'Fraunces', serif;">
                                        Company Details
                                    </h2>
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
                                    <div class="w-16 h-16 rounded-xl bg-[#123524]/10 dark:bg-white/10 flex items-center justify-center text-[#123524] dark:text-white text-xl font-bold shrink-0 overflow-hidden">
                                        @if ($company->company_logo)
                                            <img src="{{ filter_var($company->company_logo, FILTER_VALIDATE_URL)
                                                ? $company->company_logo
                                                : Storage::url($company->company_logo) }}"
                                                alt="{{ $company->company_name }}"
                                                class="w-full h-full object-contain">
                                        @else
                                            {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold text-[#123524] dark:text-white truncate">{{ $company->company_name }}</h3>
                                        <p class="text-sm text-black/60 dark:text-white/60">{{ $company->company_address ?? 'No address provided' }}</p>
                                    </div>
                                </div>

                                {{-- Description --}}
                                @if ($company->company_desc)
                                    <div class="mb-6 prose prose-sm text-black/70 dark:text-white/70">
                                        <p>{{ $company->company_desc }}</p>
                                    </div>
                                @endif

                                {{-- Details List --}}
                                <div class="border-t border-black/5 dark:border-white/5 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Total Alumni</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $company->work_histories_count }} alumni worked here</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Created On</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $company->created_at->format('M j, Y g:i A') }}</p>
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
                                <a href="{{ route('super-admin.company.update', $company->id) }}" wire:navigate
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-[#123524] dark:bg-[#D4A537] px-4 py-2.5 text-sm font-semibold text-white dark:text-[#123524] shadow-sm hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                    Edit Company
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