<div x-data="{ modalOpen: @entangle('viewingPostId') }" 
     @keydown.escape.window="$wire.closePost()">
     
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
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
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage all announcements and posts</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @can('can_create')
                    <a href="{{ route('super-admin.post.create') }}"
                        class="w-full sm:w-auto justify-center inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-xl bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition py-2.5 px-4 sm:px-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Post
                    </a>
                    @endcan
                </div>
            </div>

            {{-- ========== FLASHES ========== --}}
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                @can('can_view_any')
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">

                    {{-- Search --}}
                    <div class="relative w-full lg:w-80">
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search title, slug, author, or category…"
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

                    {{-- Filters row on mobile, inline on lg --}}
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
                @endcan
            </div>

            {{-- ========== MOBILE CARD LIST ========== --}}
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @can('can_view_any')
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
                                <p class="font-semibold text-[#123524] dark:text-white truncate">
                                    {{ $post->title }}
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
                                        <button type="button" wire:click="openPost({{ $post->id }})"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            View
                                        </button>

                                        @can('can_update')
                                            <a href="{{ route('super-admin.post.update', $post->id) }}"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                Edit
                                            </a>
                                        @endcan
                                        @can('can_delete')
                                            <button type="button" wire:click="deletePost({{ $post->id }})"
                                                wire:confirm="Delete this post? This cannot be undone."
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                Delete
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>

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

                                <span class="text-[11px] text-black/40 dark:text-white/40 truncate">
                                    {{ $post->user->name ?? 'Unknown' }}
                                </span>
                            </div>

                            <p class="mt-1.5 text-[11px] text-black/40 dark:text-white/40">
                                {{ $post->created_at->diffForHumans() }}
                            </p>
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
                            @can('can_create')
                            <a href="{{ route('super-admin.post.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create the first post
                            </a>
                            @endcan
                        @endif
                    </div>
                @endforelse
                @endcan
            </div>

            {{-- ========== TABLE (sm+) ========== --}}
            <div class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10"
                wire:loading.class="opacity-50" wire:target="search,statusFilter,categoryFilter">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 sm:ps-6 py-3 w-16 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Image</th>
                            <th class="px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Title</th>
                            <th class="hidden md:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Author</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Category</th>
                            <th class="hidden sm:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Status</th>
                            <th class="hidden xl:table-cell px-3 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Created</th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @can('can_view_any')
                        @forelse ($this->posts as $post)
                            @php
                                $imgUrl = $post->image
                                    ? (filter_var($post->image, FILTER_VALIDATE_URL)
                                        ? $post->image
                                        : Storage::url($post->image))
                                    : null;
                                $authorAvatar = $post->user?->userProfile?->avatar;
                                $authorAvatarUrl = $authorAvatar
                                    ? (filter_var($authorAvatar, FILTER_VALIDATE_URL)
                                        ? $authorAvatar
                                        : Storage::url($authorAvatar))
                                    : null;
                                $authorInitial = strtoupper(substr($post->user->name ?? '?', 0, 1));
                            @endphp
                            <tr wire:key="post-{{ $post->id }}" class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                {{-- Image --}}
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

                                {{-- Title Static) --}}
                                <td class="px-3 py-3 min-w-[200px]">
                                    <p class="font-semibold text-[#123524] dark:text-white truncate max-w-[240px]">
                                        {{ $post->title }}
                                    </p>
                                </td>

                                {{-- Author --}}
                                <td class="hidden md:table-cell px-3 py-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if ($authorAvatarUrl)
                                            <img src="{{ $authorAvatarUrl }}" alt="{{ $post->user->name }}" loading="lazy"
                                                class="w-7 h-7 rounded-full object-cover shrink-0">
                                        @else
                                            <span class="w-7 h-7 rounded-full bg-[#D4A537] flex items-center justify-center text-[11px] font-bold text-[#123524] shrink-0">
                                                {{ $authorInitial }}
                                            </span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-black/80 dark:text-white/80 truncate">{{ $post->user->name ?? 'Unknown' }}</p>
                                            <p class="text-[10px] text-black/40 dark:text-white/40 capitalize">
                                                {{ $post->user?->roles->first()?->name ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="hidden lg:table-cell px-3 py-3">
                                    <span class="text-black/70 dark:text-white/70 truncate block max-w-[150px]">
                                        {{ $post->category->cat_name ?? '—' }}
                                    </span>
                                </td>

                                {{-- Status --}}
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

                                {{-- Created --}}
                                <td class="hidden xl:table-cell px-3 py-3">
                                    <span class="text-black/50 dark:text-white/50 whitespace-nowrap">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </td>

                                {{-- Actions --}}
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
                                            <button type="button" wire:click="openPost({{ $post->id }})"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                View
                                            </button>

                                            @can('can_update')
                                                <a href="{{ route('super-admin.post.update', $post->id) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('can_delete')
                                                <button type="button" wire:click="deletePost({{ $post->id }})"
                                                    wire:confirm="Delete this post? This cannot be undone."
                                                    class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition w-full text-left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                    Delete
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
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
                                        @can('can_create')
                                        <a href="{{ route('super-admin.post.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Create the first post
                                        </a>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        @endcan
                    </tbody>
                </table>
            </div>

            {{-- ========== FOOTER ========== --}}
            <div class="px-4 sm:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                @can('can_view_any')
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
                 @click="$wire.closePost()"></div>

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
                        
                        @if($this->viewingPost)
                            @php 
                                $post = $this->viewingPost; 
                                $modalImg = $post->image
                                    ? (filter_var($post->image, FILTER_VALIDATE_URL)
                                        ? $post->image
                                        : Storage::url($post->image))
                                    : null;
                            @endphp

                            {{-- Header --}}
                            <div class="px-4 py-5 sm:px-6 bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5 flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-[#123524] dark:text-white" id="modal-title" style="font-family: 'Fraunces', serif;">
                                        Post Details
                                    </h2>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide 
                                            @if ($post->status === 'public') bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400
                                            @elseif ($post->status === 'private') bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400
                                            @else bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $post->status }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="$wire.closePost()" class="relative rounded-md bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-4 py-6 sm:px-6">
                                
                                {{-- Image --}}
                                @if($modalImg)
                                    <div class="mb-6 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img src="{{ $modalImg }}" alt="{{ $post->title }}" class="w-full h-48 sm:h-64 object-cover">
                                    </div>
                                @endif

                                {{-- Title & Desc --}}
                                <h3 class="text-2xl font-bold text-[#123524] dark:text-white">{{ $post->title }}</h3>
                                <div class="mt-4 prose prose-sm text-black/70 dark:text-white/70">
                                    <p>{{ $post->description ?? 'No description provided.' }}</p>
                                </div>

                                {{-- Details List --}}
                                <div class="mt-8 border-t border-black/5 dark:border-white/5 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Author</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $post->user?->name ?? 'System' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Category</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $post->category->cat_name ?? 'None' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Attachments --}}
                                @if (!empty($post->attachments) && is_array($post->attachments))
                                    <div class="mt-8 border-t border-black/5 dark:border-white/5 pt-6">
                                        <h4 class="text-sm font-semibold text-black dark:text-white mb-3">Attachments</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($post->attachments as $file)
                                                <a href="{{ Storage::url($file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                    </svg>
                                                    {{ basename($file) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Metadata --}}
                                <div class="mt-8 pt-6 border-t border-black/5 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-black/40 dark:text-white/40">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Created: {{ $post->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    
                                    @if($post->updated_at->gt($post->created_at))
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <span>Updated: {{ $post->updated_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="bg-[#F7F5EF] dark:bg-[#3A3B3C] px-4 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 border-t border-black/5 dark:border-white/5 rounded-b-2xl">
                                <button type="button" @click="$wire.closePost()"
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-white dark:bg-[#242526] px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    Close
                                </button>
                                @can('can_update')
                                <a href="{{ route('super-admin.post.update', $post->id) }}" wire:navigate
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-[#123524] dark:bg-[#D4A537] px-4 py-2.5 text-sm font-semibold text-white dark:text-[#123524] shadow-sm hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                    Edit Post
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