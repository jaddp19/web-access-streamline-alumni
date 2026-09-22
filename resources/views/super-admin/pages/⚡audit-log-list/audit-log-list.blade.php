<div>
    <div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay -->
            <div wire:loading.flex wire:target="search,nextPage,previousPage,gotoPage"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/70 dark:bg-[#242526]/70 pt-24 pointer-events-none">
                <svg class="w-6 h-6 animate-spin text-[#123524] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>

            {{-- ===================== HEADER ===================== --}}
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4 border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#D4A537]/15 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#a97f1f] dark:text-[#E5B94A] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Activity Log
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">
                            Audit trail of all system changes
                        </p>
                    </div>
                </div>
            </div>

            {{-- ===================== FILTERS ===================== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-black/5 dark:border-white/5">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">

                    {{-- Search --}}
                    <div class="relative w-full lg:w-72">
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search user, type, or IP…"
                            class="w-full py-2 pl-9 pr-8 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537]">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-2.5 h-4 w-4 text-gray-400 dark:text-white/40"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
                        </svg>
                        @if ($search !== '')
                            <button type="button" wire:click="$set('search', '')"
                                class="absolute right-2 top-2 p-0.5 rounded text-black/40 dark:text-white/40 hover:text-black/70 dark:hover:text-white"
                                title="Clear">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Filters grid --}}
                    <div class="grid grid-cols-2 lg:flex lg:items-center gap-2">

                        {{-- Action --}}
                        <select wire:model.live="actionFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-36">
                            <option value="all">All actions</option>
                            <option value="created">Created</option>
                            <option value="updated">Updated</option>
                            <option value="deleted">Deleted</option>
                        </select>

                        {{-- Type --}}
                        <select wire:model.live="typeFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-36">
                            <option value="all">All types</option>
                            @foreach ($this->availableTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        {{-- User --}}
                        <select wire:model.live="userFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-44">
                            <option value="all">All users</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                            @endforeach
                        </select>

                        {{-- Date range --}}
                        <select wire:model.live="dateRange"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-32">
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
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

            {{-- ===================== MOBILE CARD LIST ===================== --}}
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @forelse ($this->logs as $log)
                    @php
                        $actionColor = match ($log->action) {
                            'created'  => 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
                            'updated'  => 'bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400',
                            'deleted'  => 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400',
                            default    => 'bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50',
                        };
                    @endphp
                    <div wire:key="mobile-log-{{ $log->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <p class="font-semibold text-sm text-[#123524] dark:text-white truncate">
                                {{ $log->user_name ?? 'System' }}
                            </p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide shrink-0 {{ $actionColor }}">
                                {{ $log->action }}
                            </span>
                        </div>

                        <p class="text-xs text-black/60 dark:text-white/60">
                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                        </p>

                        @if ($log->changes)
                            <div class="mt-2 rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] p-2 text-[11px] text-black/70 dark:text-white/70 space-y-1">
                                @foreach (array_slice($log->changes, 0, 3, true) as $field => $change)
                                    <div class="flex items-start gap-1.5">
                                        <span class="font-semibold shrink-0">{{ $field }}:</span>
                                        <span class="truncate">
                                            @if (is_array($change))
                                                {{ \Illuminate\Support\Str::limit((string) ($change['old'] ?? '—'), 20) }}
                                                →
                                                {{ \Illuminate\Support\Str::limit((string) ($change['new'] ?? '—'), 20) }}
                                            @else
                                                {{ \Illuminate\Support\Str::limit((string) $change, 40) }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                                @if (count($log->changes) > 3)
                                    <p class="text-[10px] italic opacity-60">
                                        +{{ count($log->changes) - 3 }} more field(s)
                                    </p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-2 flex items-center justify-between text-[11px] text-black/40 dark:text-white/40">
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                            @if ($log->ip_address)
                                <span class="font-mono">{{ $log->ip_address }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-black/40 dark:text-white/40 text-sm">
                            {{ $this->hasFilters ? 'No activity matches your filters.' : 'No activity recorded yet.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- ===================== TABLE (sm+) ===================== --}}
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">User</th>
                            <th class="px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Action</th>
                            <th class="px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Target</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Changes</th>
                            <th class="hidden xl:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">IP</th>
                            <th class="px-3 py-3 text-end font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">When</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->logs as $log)
                            @php
                                $actionColor = match ($log->action) {
                                    'created'  => 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400',
                                    'updated'  => 'bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400',
                                    'deleted'  => 'bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400',
                                    default    => 'bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50',
                                };
                            @endphp
                            <tr wire:key="log-{{ $log->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="ps-4 sm:ps-6 py-3">
                                    <span class="font-semibold text-[#123524] dark:text-white truncate block max-w-[140px]">
                                        {{ $log->user_name ?? 'System' }}
                                    </span>
                                </td>

                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide {{ $actionColor }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-3 py-3">
                                    <span class="text-black/70 dark:text-white/70">
                                        {{ class_basename($log->auditable_type) }}
                                        <span class="font-mono text-[11px] text-black/40 dark:text-white/40">
                                            #{{ $log->auditable_id }}
                                        </span>
                                    </span>
                                </td>

                                <td class="hidden lg:table-cell px-3 py-3 max-w-md">
                                    @if ($log->changes)
                                        <div class="text-[11px] text-black/70 dark:text-white/70 space-y-0.5">
                                            @foreach (array_slice($log->changes, 0, 2, true) as $field => $change)
                                                <div class="flex items-start gap-1.5 truncate">
                                                    <span class="font-semibold shrink-0">{{ $field }}:</span>
                                                    <span class="truncate">
                                                        @if (is_array($change))
                                                            {{ \Illuminate\Support\Str::limit((string) ($change['old'] ?? '—'), 20) }}
                                                            →
                                                            {{ \Illuminate\Support\Str::limit((string) ($change['new'] ?? '—'), 20) }}
                                                        @else
                                                            {{ \Illuminate\Support\Str::limit((string) $change, 40) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if (count($log->changes) > 2)
                                                <p class="text-[10px] italic opacity-60">
                                                    +{{ count($log->changes) - 2 }} more field(s)
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-black/30 dark:text-white/30 italic">—</span>
                                    @endif
                                </td>

                                <td class="hidden xl:table-cell px-3 py-3">
                                    <span class="font-mono text-[11px] text-black/50 dark:text-white/50">
                                        {{ $log->ip_address ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-3 py-3 text-end">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">
                                        {{ $this->hasFilters ? 'No activity matches your filters.' : 'No activity recorded yet.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===================== FOOTER ===================== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                <p class="text-xs sm:text-sm text-black/60 dark:text-white/60 text-center sm:text-left">
                    Showing
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->logs->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->logs->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->logs->total() }}</span>
                    {{ Str::plural('entry', $this->logs->total()) }}
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->logs->onFirstPage())
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
                        {{ $this->logs->currentPage() }} / {{ $this->logs->lastPage() }}
                    </span>

                    @if ($this->logs->hasMorePages())
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