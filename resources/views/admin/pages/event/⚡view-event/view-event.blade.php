<div>
    <div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14">
        <div class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            {{-- ========== HEADER ========== --}}
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#D4A537]/15 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#a97f1f] dark:text-[#E5B94A] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Events
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">
                            {{ $this->isProgramHead
                                ? 'Manage the events you have created'
                                : 'Manage all alumni events' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.event.create') }}"
                    class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition py-2.5 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Event
                </a>
            </div>

            {{-- ========== FLASHES ========== --}}
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 px-3 sm:px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ========== FILTERS ========== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-black/5 dark:border-white/5">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                    <div class="relative w-full lg:w-72">
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search title or location…"
                            class="w-full py-2 pl-9 pr-8 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400 dark:text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
                        </svg>
                    </div>

                    <div class="grid grid-cols-2 lg:flex gap-2">
                        <select wire:model.live="statusFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] w-full lg:w-36">
                            <option value="all">All statuses</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>

                        <select wire:model.live="timeFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] w-full lg:w-36">
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past</option>
                            <option value="all">All time</option>
                        </select>
                    </div>

                    @if ($this->hasFilters)
                        <button type="button" wire:click="clearFilters"
                            class="text-xs font-semibold text-[#1877F2] dark:text-[#D4A537] hover:underline lg:ml-auto">
                            Clear filters
                        </button>
                    @endif
                </div>
            </div>

            {{-- ========== TABLE ========== --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Event</th>
                            <th class="hidden md:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">When</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Location</th>
                            <th class="hidden sm:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Status</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->events as $event)
                            @php
                                $statusColor = match ($event->status) {
                                    'published' => 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
                                    'cancelled' => 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400',
                                    'completed' => 'bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400',
                                    default     => 'bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50',
                                };
                            @endphp
                            <tr wire:key="event-{{ $event->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="ps-4 sm:ps-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if ($event->image)
                                            <img src="{{ $event->image_url }}" alt="" loading="lazy"
                                                class="w-10 h-10 rounded-lg object-cover shrink-0 bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate">{{ $event->title }}</span>
                                            <span class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">
                                                {{ $event->starts_at->format('M d, Y · g:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 py-3">
                                    <span class="text-black/70 dark:text-white/70 whitespace-nowrap">
                                        {{ $event->starts_at->format('M d, Y · g:i A') }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell px-3 py-3">
                                    <span class="text-black/70 dark:text-white/70 truncate block max-w-[180px]">
                                        {{ $event->location ?? '—' }}
                                    </span>
                                </td>
                                <td class="hidden sm:table-cell px-3 py-3">
                                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide {{ $statusColor }}">
                                        {{ $event->status }}
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.event.update', $event->id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] dark:text-[#D4A537] hover:underline font-semibold whitespace-nowrap">
                                            Edit
                                        </a>
                                        <button type="button"
                                            wire:click="deleteEvent({{ $event->id }})"
                                            wire:confirm="Delete this event? This cannot be undone."
                                            class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 hover:underline font-semibold whitespace-nowrap">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">
                                        {{ $this->hasFilters
                                            ? 'No events match your filters.'
                                            : ($this->isProgramHead
                                                ? "You haven't created any events yet."
                                                : 'No events yet.') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ========== FOOTER ========== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                <p class="text-xs sm:text-sm text-black/60 dark:text-white/60 text-center sm:text-left">
                    Showing
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->events->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->events->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->events->total() }}</span>
                    {{ Str::plural('event', $this->events->total()) }}
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->events->onFirstPage())
                        <button disabled class="flex-1 sm:flex-none px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage" class="flex-1 sm:flex-none px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Prev
                        </button>
                    @endif

                    <span class="sm:hidden text-xs font-semibold text-black/60 dark:text-white/60 whitespace-nowrap">
                        {{ $this->events->currentPage() }} / {{ $this->events->lastPage() }}
                    </span>

                    @if ($this->events->hasMorePages())
                        <button wire:click="nextPage" class="flex-1 sm:flex-none px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Next
                        </button>
                    @else
                        <button disabled class="flex-1 sm:flex-none px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            Next
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>