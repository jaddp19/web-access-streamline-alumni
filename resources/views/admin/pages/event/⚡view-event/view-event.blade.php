<div x-data="{ modalOpen: @entangle('viewingEventId') }" 
     @keydown.escape.window="$wire.closeEvent()">
     
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

                @can('can_create')
                <a href="{{ route('admin.event.create') }}"
                    class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition py-2.5 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Event
                </a>
                @endcan
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
            
            @can('can_view')
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
            @endcan

            {{-- ========== TABLE ========== --}}
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Event</th>
                            <th class="hidden md:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">When</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Location</th>
                            <th class="hidden sm:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @can('can_view')
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
                                            {{-- ADDED max-w-[130px] HERE --}}
                                            <span class="font-semibold text-[#123524] dark:text-white block truncate max-w-[130px]">
                                                {{ $event->title }}
                                            </span>
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
                                
                                {{-- 3-Dots Dropdown (Visible on both Mobile and Desktop) --}}
                                <td class="px-4 sm:px-6 py-3 text-end relative">
                                    <div x-data="{ open: false }" class="inline-block text-left">
                                        <button @click="open = !open" @click.outside="open = false"
                                            class="p-2 rounded-full text-black/40 hover:text-black dark:text-white/40 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition focus:outline-none">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-transition x-cloak
                                            class="absolute right-0 z-20 mt-1 w-36 origin-top-right rounded-xl bg-white dark:bg-[#242526] shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none divide-y divide-black/5 dark:divide-white/5">
                                            
                                            {{-- View Button --}}
                                            <button type="button" wire:click="openEvent({{ $event->id }})"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                View
                                            </button>

                                            @can('can_update')
                                                <a href="{{ route('admin.event.update', $event->id) }}"
                                                    class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                    Edit
                                                </a>

                                                @if ($event->status !== 'cancelled')
                                                    <button type="button" wire:click="cancelEvent({{ $event->id }})"
                                                        wire:confirm="Cancel '{{ $event->title }}'? Attendees will be notified."
                                                        class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition w-full text-left">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        Cancel
                                                    </button>
                                                @endif
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
                        @endcan
                    </tbody>
                </table>
            </div>

            {{-- ========== FOOTER ========== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                @can('can_view')
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
                 @click="$wire.closeEvent()"></div>

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
                        
                        @if($this->viewingEvent)
                            @php $ev = $this->viewingEvent; @endphp

                            {{-- Header --}}
                            <div class="px-4 py-5 sm:px-6 bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5 flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-[#123524] dark:text-white" id="modal-title" style="font-family: 'Fraunces', serif;">
                                        Event Details
                                    </h2>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide 
                                            {{ $ev->status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : ($ev->status === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                                            {{ $ev->status }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="$wire.closeEvent()" class="relative rounded-md bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-4 py-6 sm:px-6">
                                
                                {{-- Image --}}
                                @if($ev->image)
                                    <div class="mb-6 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img src="{{ $ev->image_url }}" alt="{{ $ev->title }}" class="w-full h-48 sm:h-64 object-cover">
                                    </div>
                                @endif

                                {{-- Title & Desc --}}
                                <h3 class="text-2xl font-bold text-[#123524] dark:text-white truncate">{{ $ev->title }}</h3>
                                <div class="mt-4 prose prose-sm text-black/70 dark:text-white/70">
                                    <p>{{ $ev->description ?? 'No description provided.' }}</p>
                                </div>

                                {{-- Details List --}}
                                <div class="mt-8 border-t border-black/5 dark:border-white/5 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Schedule</p>
                                            <p class="text-black/60 dark:text-white/60">Starts: {{ $ev->starts_at->format('M j, Y g:i A') }}</p>
                                            @if($ev->ends_at)
                                                <p class="text-black/60 dark:text-white/60">Ends: {{ $ev->ends_at->format('M j, Y g:i A') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Location</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $ev->location ?? 'TBA' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Capacity</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $ev->capacity ? $ev->capacity . ' attendees max' : 'Unlimited' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Created By</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $ev->creator?->name ?? 'System' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Metadata (Created/Updated) --}}
                                <div class="mt-8 pt-6 border-t border-black/5 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-black/40 dark:text-white/40">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Created: {{ $ev->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    
                                    @if($ev->updated_at->gt($ev->created_at))
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <span>Updated: {{ $ev->updated_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="bg-[#F7F5EF] dark:bg-[#3A3B3C] px-4 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 border-t border-black/5 dark:border-white/5 rounded-b-2xl">
                                <button type="button" @click="$wire.closeEvent()"
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-white dark:bg-[#242526] px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    Close
                                </button>
                                @can('can_update')
                                <a href="{{ route('admin.event.update', $ev->id) }}" wire:navigate
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-[#123524] dark:bg-[#D4A537] px-4 py-2.5 text-sm font-semibold text-white dark:text-[#123524] shadow-sm hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                    Edit Event
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