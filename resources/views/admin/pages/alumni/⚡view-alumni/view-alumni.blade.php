<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm">

            <!-- Header -->
            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-black/5">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Alumni</h2>
                        <p class="text-sm text-black/50">Manage alumni records</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Export button --}}
                    <button type="button" wire:click="exportCsv"
                        wire:loading.attr="disabled"
                        wire:target="exportCsv"
                        class="inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-[#123524] hover:bg-black/5 transition py-2.5 px-4 disabled:opacity-50">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <path d="M7 10l5 5 5-5" />
                            <path d="M12 15V3" />
                        </svg>
                        <span wire:loading.remove wire:target="exportCsv">Export</span>
                        <span wire:loading wire:target="exportCsv">Exporting…</span>
                    </button>

                    {{-- Create button --}}
                    <a href="{{ route('admin.alumni.create') }}"
                        class="inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#123524] text-white hover:bg-[#0d2819] transition py-2.5 px-5 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Alumni
                    </a>
                </div>
            </div>
            <!-- End Header -->

            {{-- Search bar --}}
            <div class="px-4 sm:px-6 py-3 border-b border-black/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search name, email, school ID, course, batch…"
                        class="w-full py-2 pl-9 pr-8 text-sm rounded-lg bg-[#F7F5EF] border border-black/10 text-black placeholder:text-gray-400 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
                    </svg>

                    @if ($search !== '')
                        <button type="button" wire:click="$set('search', '')"
                            class="absolute right-2 top-2 p-0.5 rounded text-black/40 hover:text-black/70 transition"
                            title="Clear search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                @if ($search !== '')
                    <p class="text-xs text-black/50">
                        <span class="font-semibold text-[#123524]">{{ $this->alumni->total() }}</span>
                        result(s) for "<span class="font-semibold">{{ $search }}</span>"
                    </p>
                @endif
            </div>
            {{-- End search bar --}}

            <!-- Table -->
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10"
                wire:loading.class="opacity-50" wire:target="search">
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
                        @forelse ($this->alumni as $profile)
                            <tr wire:key="alumni-{{ $profile->id }}" class="hover:bg-black/[0.02] transition-colors">
                                <td class="px-2 sm:px-6 py-3">
                                    <div class="flex items-center gap-3">
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

                                        @if ($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}"
                                                class="w-8 h-8 rounded-full object-cover shrink-0 bg-[#123524]/10"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-8 h-8 rounded-full bg-[#123524]/10 items-center justify-center text-[#123524] text-xs font-bold shrink-0">
                                                {{ $initials }}
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#123524]/10 flex items-center justify-center text-[#123524] text-xs font-bold shrink-0">
                                                {{ $initials }}
                                            </div>
                                        @endif

                                        <span class="font-semibold text-[#123524] truncate">{{ $displayName }}</span>
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
                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.alumni.view-single', $profile->user_id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] hover:text-[#0d2819] font-semibold hover:underline">
                                            View
                                        </a>
                                        <a href="{{ route('admin.alumni.update', $profile->user_id) }}"
                                            class="inline-flex items-center gap-1 text-black/50 hover:text-[#123524] font-semibold hover:underline">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                        </svg>
                                    </div>

                                    @if ($search !== '')
                                        <p class="text-black/60 text-sm mb-1">
                                            No alumni found matching "<span class="font-semibold">{{ $search }}</span>".
                                        </p>
                                        <button type="button" wire:click="$set('search', '')"
                                            class="text-[#1877F2] text-sm font-semibold hover:underline mt-2">
                                            Clear search
                                        </button>
                                    @else
                                        <p class="text-black/40 text-sm mb-4">No alumni found.</p>
                                        <a href="{{ route('admin.alumni.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] text-white text-sm font-semibold hover:bg-[#0d2819] transition">
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
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-black/5">
                <p class="text-sm text-black/60">
                    <span class="font-semibold text-[#123524]">{{ $this->alumni->total() }}</span> results
                </p>

                <div class="inline-flex gap-x-2">
                    @if ($this->alumni->onFirstPage())
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

                    @if ($this->alumni->hasMorePages())
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
    </div>
</div>