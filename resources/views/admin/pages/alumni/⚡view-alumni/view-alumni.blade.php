<div>
    <div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay -->
            <div wire:loading.flex wire:target="search,nextPage,previousPage,gotoPage,exportCsv"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/70 dark:bg-[#242526]/70 pt-24 pointer-events-none">
                <svg class="w-6 h-6 animate-spin text-[#123524] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>

            <!-- ===================== HEADER ===================== -->
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4 border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Alumni
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage alumni records</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2">
                    <button type="button" wire:click="exportCsv"
                        wire:loading.attr="disabled" wire:target="exportCsv"
                        class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-4 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <path d="M7 10l5 5 5-5" />
                            <path d="M12 15V3" />
                        </svg>
                        <span wire:loading.remove wire:target="exportCsv">Export</span>
                        <span wire:loading wire:target="exportCsv">Exporting…</span>
                    </button>

                    <a href="{{ route('admin.alumni.create') }}"
                        class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-xl bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition py-2.5 px-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Alumni
                    </a>
                </div>
            </div>

            <!-- ===================== SEARCH ===================== -->
            <div class="px-4 sm:px-6 py-3 border-b border-black/5 dark:border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search name, email, school ID…"
                        class="w-full py-2 pl-9 pr-8 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400 dark:text-white/40"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
                    </svg>

                    @if ($search !== '')
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-2 top-2 p-0.5 rounded text-black/40 dark:text-white/40 hover:text-black/70 dark:hover:text-white transition"
                            title="Clear search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                @if ($search !== '')
                    <p class="text-xs text-black/50 dark:text-white/50 truncate">
                        <span class="font-semibold text-[#123524] dark:text-white">{{ $this->alumni->total() }}</span>
                        result(s) for "<span class="font-semibold">{{ $search }}</span>"
                    </p>
                @endif
            </div>

            <!-- ===================== MOBILE CARD LIST ===================== -->
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @forelse ($this->alumni as $profile)
                    @php
                        $displayName = $profile->user?->name ?? 'Alumni';
                        $initials = \Illuminate\Support\Str::of($displayName)
                            ->trim()->explode(' ')->filter()->take(1)
                            ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                            ->implode('') ?: '?';
                        $rawAvatar = $profile->avatar;
                        $avatarUrl = $rawAvatar
                            ? (filter_var($rawAvatar, FILTER_VALIDATE_URL) ? $rawAvatar : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                            : null;
                    @endphp

                    <div wire:key="mobile-alumni-{{ $profile->id }}" class="p-4 flex items-start gap-3">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" loading="lazy"
                                class="w-10 h-10 rounded-full object-cover shrink-0 bg-[#123524]/10 dark:bg-white/10"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display: none;"
                                class="w-10 h-10 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                {{ $initials }}
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                {{ $initials }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-[#123524] dark:text-white truncate">{{ $displayName }}</p>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('admin.alumni.view-single', $profile->user_id) }}"
                                        class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                                        View
                                    </a>
                                    <span class="text-black/20 dark:text-white/20">|</span>
                                    <a href="{{ route('admin.alumni.update', $profile->user_id) }}"
                                        class="text-xs font-semibold text-black/50 dark:text-white/50 hover:underline">
                                        Edit
                                    </a>
                                </div>
                            </div>

                            <p class="text-xs text-black/60 dark:text-white/60 truncate">{{ $profile->user->email ?? 'N/A' }}</p>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                @if ($profile->courses->count())
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 text-[#a97f1f] dark:text-[#E5B94A] font-semibold">
                                        {{ $profile->courses->pluck('course_title')->filter()->first() }}
                                    </span>
                                @endif
                                @if ($profile->batch)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#123524]/5 dark:bg-white/10 text-[#123524]/60 dark:text-white/60 font-semibold">
                                        Batch {{ $profile->batch->batch_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                            </svg>
                        </div>

                        @if ($search !== '')
                            <p class="text-black/60 dark:text-white/60 text-sm mb-1">
                                No alumni found matching "<span class="font-semibold">{{ $search }}</span>".
                            </p>
                            <button type="button" wire:click="$set('search', '')"
                                class="text-[#1877F2] dark:text-[#D4A537] text-sm font-semibold hover:underline mt-2">
                                Clear search
                            </button>
                        @else
                            <p class="text-black/40 dark:text-white/40 text-sm mb-4">No alumni found.</p>
                            <a href="{{ route('admin.alumni.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create the first alumni account
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- ===================== TABLE (sm+) ===================== -->
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10"
                wire:loading.class="opacity-50" wire:target="search">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Name</th>
                            <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Email</th>
                            <th class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Course</th>
                            <th class="hidden lg:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Batch Year</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->alumni as $profile)
                            @php
                                $displayName = $profile->user?->name ?? 'Alumni';
                                $initials = \Illuminate\Support\Str::of($displayName)
                                    ->trim()->explode(' ')->filter()->take(1)
                                    ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                                    ->implode('') ?: '?';
                                $rawAvatar = $profile->avatar;
                                $avatarUrl = $rawAvatar
                                    ? (filter_var($rawAvatar, FILTER_VALIDATE_URL) ? $rawAvatar : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                                    : null;
                            @endphp

                            <tr wire:key="alumni-{{ $profile->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="px-3 lg:px-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if ($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" loading="lazy"
                                                class="w-8 h-8 rounded-full object-cover shrink-0 bg-[#123524]/10 dark:bg-white/10"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div style="display: none;"
                                                class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                                {{ $initials }}
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate">{{ $displayName }}</span>
                                            <span class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">
                                                {{ $profile->user->email ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/70 dark:text-white/70 block truncate">{{ $profile->user->email ?? 'N/A' }}</span>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3">
                                    <span class="text-black/70 dark:text-white/70 block truncate max-w-[200px]">
                                        {{ $profile->courses->pluck('course_title')->filter()->join(', ') ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell px-3 lg:px-6 py-3">
                                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold whitespace-nowrap">
                                        {{ $profile->batch->batch_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.alumni.view-single', $profile->user_id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] dark:text-[#D4A537] hover:text-[#0d2819] dark:hover:text-[#E5B94A] font-semibold hover:underline whitespace-nowrap">
                                            View
                                        </a>
                                        <a href="{{ route('admin.alumni.update', $profile->user_id) }}"
                                            class="inline-flex items-center gap-1 text-black/50 dark:text-white/50 hover:text-[#123524] dark:hover:text-[#D4A537] font-semibold hover:underline whitespace-nowrap">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                        </svg>
                                    </div>

                                    @if ($search !== '')
                                        <p class="text-black/60 dark:text-white/60 text-sm mb-1">
                                            No alumni found matching "<span class="font-semibold">{{ $search }}</span>".
                                        </p>
                                        <button type="button" wire:click="$set('search', '')"
                                            class="text-[#1877F2] dark:text-[#D4A537] text-sm font-semibold hover:underline mt-2">
                                            Clear search
                                        </button>
                                    @else
                                        <p class="text-black/40 dark:text-white/40 text-sm mb-4">No alumni found.</p>
                                        <a href="{{ route('admin.alumni.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Create the first alumni account
                                        </a>
                                    @endif
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
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->alumni->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->alumni->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->alumni->total() }}</span>
                    results
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->alumni->onFirstPage())
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
                        {{ $this->alumni->currentPage() }} / {{ $this->alumni->lastPage() }}
                    </span>

                    @if ($this->alumni->hasMorePages())
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