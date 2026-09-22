<div>
    <div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay -->
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,deletePost,toggleStatus"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/70 dark:bg-[#242526]/70 pt-24 pointer-events-none">
                <svg class="w-6 h-6 animate-spin text-[#123524] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>

            {{-- ========== HEADER ========== --}}
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 lg:gap-4 border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Posts
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Your announcements and posts</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.post.create') }}"
                        class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-xl bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition py-2.5 px-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Post
                    </a>
                </div>
            </div>

            {{-- ========== FLASHES ========== --}}
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ========== FILTERS ========== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-black/5 dark:border-white/5">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">

                    {{-- Search --}}
                    <div class="relative w-full lg:w-80">
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search title, slug, or category…"
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

                    {{-- Filters row --}}
                    <div class="grid grid-cols-2 lg:flex lg:items-center gap-2">
                        <select wire:model.live="statusFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-40">
                            <option value="all">All Statuses</option>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                            <option value="draft">Draft</option>
                        </select>

                        <select wire:model.live="categoryFilter"
                            class="px-3 py-2 text-xs sm:text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] w-full lg:w-48">
                            <option value="all">All Categories</option>
                            @foreach ($this->categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
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

            {{-- ========== MOBILE CARD LIST ========== --}}
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @forelse ($this->posts as $post)
                    @php
                        $imgUrl = $post->image
                            ? (filter_var($post->image, FILTER_VALIDATE_URL)
                                ? $post->image
                                : Storage::url($post->image))
                            : null;
                    @endphp
                    <div wire:key="mobile-post-{{ $post->id }}" class="p-4 flex items-start gap-3">
                        @if ($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $post->title }}" loading="lazy"
                                class="w-14 h-14 rounded-lg object-cover shrink-0 bg-[#F7F5EF] dark:bg-[#3A3B3C]">
                        @else
                            <div class="w-14 h-14 rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-[#123524] dark:text-white truncate">{{ $post->title }}</p>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('admin.post.update', $post->id) }}"
                                        class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                                        Edit
                                    </a>
                                    <span class="text-black/20 dark:text-white/20">|</span>
                                    <button type="button"
                                        wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="Delete this post? This cannot be undone."
                                        class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <p class="text-[11px] text-black/40 dark:text-white/40 font-mono truncate mt-0.5">{{ $post->slug }}</p>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <button type="button" wire:click="toggleStatus({{ $post->id }})"
                                    class="text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide
                                        @if ($post->status === 'public') bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400
                                        @elseif ($post->status === 'private') bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400
                                        @else bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50 @endif">
                                    {{ $post->status }}
                                </button>

                                @if ($post->category)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 text-[#a97f1f] dark:text-[#D4A537] font-semibold uppercase tracking-wide">
                                        {{ $post->category->cat_name }}
                                    </span>
                                @endif

                                <span class="text-[11px] text-black/40 dark:text-white/40">
                                    {{ $post->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                            </svg>
                        </div>

                        @if ($this->hasFilters)
                            <p class="text-black/60 dark:text-white/60 text-sm mb-1">No posts match your filters.</p>
                            <button type="button" wire:click="clearFilters"
                                class="text-[#1877F2] dark:text-[#D4A537] text-sm font-semibold hover:underline mt-1">
                                Clear filters
                            </button>
                        @else
                            <p class="text-black/40 dark:text-white/40 text-sm mb-3">No posts yet.</p>
                            <a href="{{ route('admin.post.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create the first post
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- ========== TABLE (sm+) ========== --}}
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10"
                wire:loading.class="opacity-50" wire:target="search,statusFilter,categoryFilter">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 w-16 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Image</th>
                            <th class="px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Title</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Category</th>
                            <th class="hidden sm:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Status</th>
                            <th class="hidden xl:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Created</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->posts as $post)
                            @php
                                $imgUrl = $post->image
                                    ? (filter_var($post->image, FILTER_VALIDATE_URL)
                                        ? $post->image
                                        : Storage::url($post->image))
                                    : null;
                            @endphp
                            <tr wire:key="post-{{ $post->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="ps-4 sm:ps-6 py-3">
                                    @if ($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="{{ $post->title }}"
                                            class="w-12 h-12 rounded-lg object-cover bg-[#F7F5EF] dark:bg-[#3A3B3C]"
                                            loading="lazy">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-3 py-3 min-w-[200px]">
                                    <p class="font-semibold text-[#123524] dark:text-white truncate max-w-[240px]">{{ $post->title }}</p>
                                    <p class="text-[11px] text-black/40 dark:text-white/40 font-mono truncate max-w-[240px]">{{ $post->slug }}</p>
                                </td>

                                <td class="hidden lg:table-cell px-3 py-3">
                                    <span class="text-black/70 dark:text-white/70 truncate block max-w-[150px]">
                                        {{ $post->category->cat_name ?? '—' }}
                                    </span>
                                </td>

                                <td class="hidden sm:table-cell px-3 py-3">
                                    <button type="button" wire:click="toggleStatus({{ $post->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $post->id }})"
                                        title="Click to toggle public/draft"
                                        class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide transition disabled:opacity-50
                                            @if ($post->status === 'public') bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-500/25
                                            @elseif ($post->status === 'private') bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400
                                            @else bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50 hover:bg-black/10 dark:hover:bg-white/15 @endif">
                                        {{ $post->status }}
                                    </button>
                                </td>

                                <td class="hidden xl:table-cell px-3 py-3">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">{{ $post->created_at->diffForHumans() }}</span>
                                </td>

                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.post.update', $post->id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] dark:text-[#D4A537] hover:text-[#0d2819] dark:hover:text-[#E5B94A] font-semibold hover:underline whitespace-nowrap">
                                            Edit
                                        </a>
                                        <button type="button"
                                            wire:click="deletePost({{ $post->id }})"
                                            wire:confirm="Delete this post? This cannot be undone."
                                            class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-semibold hover:underline whitespace-nowrap">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                        </svg>
                                    </div>

                                    @if ($this->hasFilters)
                                        <p class="text-black/60 dark:text-white/60 text-sm mb-1">No posts match your filters.</p>
                                        <button type="button" wire:click="clearFilters"
                                            class="text-[#1877F2] dark:text-[#D4A537] text-sm font-semibold hover:underline mt-2">
                                            Clear filters
                                        </button>
                                    @else
                                        <p class="text-black/40 dark:text-white/40 text-sm mb-4">No posts yet.</p>
                                        <a href="{{ route('admin.post.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Create the first post
                                        </a>
                                    @endif
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
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->posts->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#123524] dark:text-white">{{ $this->posts->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->posts->total() }}</span>
                    result{{ $this->posts->total() === 1 ? '' : 's' }}
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    @if ($this->posts->onFirstPage())
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
                        {{ $this->posts->currentPage() }} / {{ $this->posts->lastPage() }}
                    </span>

                    @if ($this->posts->hasMorePages())
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