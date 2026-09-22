<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== HEADER BANNER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-tight"
                        style="font-family: 'Fraunces', serif;">Notifications</h1>
                    <p class="text-xs text-black/50 dark:text-white/50">Stay updated with your community</p>
                </div>
            </div>

            {{-- Dynamic unread badge --}}
            <div class="hidden sm:flex items-center gap-2">
                @if ($this->unreadCount > 0)
                    <div
                        class="px-4 py-1.5 rounded-full bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] text-xs font-semibold flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        {{ $this->unreadCount }} unread
                    </div>
                @else
                    <div
                        class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        All caught up
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========== TWO-COLUMN LAYOUT ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="lg:col-span-3 space-y-4">
            {{-- Profile quick card --}}
            <div
                class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24 border border-transparent dark:border-white/5">
                <div class="h-16 bg-gradient-to-r from-[#123524] to-[#1C6B45]"></div>
                <div class="px-4 pb-4 text-center -mt-10">
                    @php
                        $authUser = auth()->user();
                        $profile = $authUser?->userProfile;

                        $rawAvatar = $profile?->avatar;
                        $avatarUrl = $rawAvatar
                            ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                                ? $rawAvatar
                                : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                            : null;
                        $initial = strtoupper(substr($authUser->name ?? '?', 0, 1));
                    @endphp

                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $this->alumni->name }}"
                            class="w-20 h-20 mx-auto rounded-full object-cover ring-4 ring-white dark:ring-[#242526] shadow-md shrink-0 bg-[#D4A537]"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span style="display: none;"
                            class="w-20 h-20 mx-auto items-center justify-center text-3xl font-bold text-[#0f2b1c] bg-yellow-500 rounded-full ring-4 ring-white dark:ring-[#242526] shadow-md shrink-0">
                            {{ $initial }}
                        </span>
                    @else
                        <span
                            class="w-20 h-20 mx-auto flex items-center justify-center text-3xl font-bold text-[#0f2b1c] bg-yellow-500 rounded-full ring-4 ring-white dark:ring-[#242526] shadow-md shrink-0">
                            {{ $initial }}
                        </span>
                    @endif

                    <p class="font-bold text-black dark:text-white mt-2" style="font-family: 'Fraunces', serif;">
                        {{ $this->alumni->name }}</p>
                    <p class="text-xs text-black/50 dark:text-white/50">Alumni Member</p>
                </div>
            </div>
        </aside>

        {{-- ===== MAIN COLUMN — NOTIFICATIONS ===== --}}
        <main class="lg:col-span-9 space-y-4">

            {{-- Filter tabs --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-2 flex items-center gap-1 overflow-x-auto">
                <button type="button" wire:click="setFilter('all')"
                    class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition
                        {{ $filter === 'all'
                            ? 'bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524]'
                            : 'text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]' }}">
                    All
                </button>
                <button type="button" wire:click="setFilter('unread')"
                    class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition
                        {{ $filter === 'unread'
                            ? 'bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524]'
                            : 'text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]' }}">
                    Unread
                    @if ($this->unreadCount > 0)
                        <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-red-500 text-white font-bold">
                            {{ $this->unreadCount }}
                        </span>
                    @endif
                </button>

                <div class="ml-auto pr-1 shrink-0">
                    @if ($this->unreadCount > 0)
                        <button type="button" wire:click="markAllAsRead"
                            class="text-xs font-semibold text-[#1877F2] dark:text-[#D4A537] hover:underline whitespace-nowrap">
                            Mark all as read
                        </button>
                    @endif
                </div>
            </div>

            {{-- Flash --}}
            @if (session('success'))
                <div
                    class="flex items-start gap-2.5 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Notifications list --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden">
                @forelse ($this->notifications as $post)
                    @php
                        $isUnread = $post->created_at->gt($this->lastSeenAt);
                        $authorRole = $post->user?->roles->first()?->name;

                        // Pick icon based on category slug
                        $catSlug = strtolower($post->category?->cat_slug ?? 'announcement');

                        $icon = match (true) {
                            str_contains($catSlug, 'event') => [
                                'bg' => 'bg-[#D4A537]/15',
                                'text' => 'text-[#a97f1f] dark:text-[#D4A537]',
                                'path' =>
                                    'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
                            ],
                            str_contains($catSlug, 'career') => [
                                'bg' => 'bg-emerald-500/10',
                                'text' => 'text-emerald-600 dark:text-emerald-400',
                                'path' =>
                                    'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z',
                            ],
                            str_contains($catSlug, 'system') => [
                                'bg' => 'bg-[#123524]/10 dark:bg-[#D4A537]/15',
                                'text' => 'text-[#123524] dark:text-[#D4A537]',
                                'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            default => [
                                'bg' => 'bg-[#1877F2]/10',
                                'text' => 'text-[#1877F2]',
                                'path' =>
                                    'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46',
                            ],
                        };
                    @endphp

                    <a href="{{ route('alumni.view-notification', $post) }}"
                        wire:navigate
                        class="flex items-start gap-3 px-4 py-4 border-b border-black/5 dark:border-white/5 last:border-b-0 transition
                            hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]
                            {{ $isUnread ? 'bg-[#1877F2]/[0.03] dark:bg-[#1877F2]/[0.06]' : '' }}">

                        {{-- Type icon --}}
                        <div
                            class="w-11 h-11 rounded-full {{ $icon['bg'] }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 {{ $icon['text'] }}" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon['path'] }}" />
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-black dark:text-white leading-snug">
                                <span class="font-semibold">{{ $post->user->name ?? 'CSAV Admin' }}</span>
                                @if ($authorRole)
                                    <span
                                        class="inline-flex items-center align-middle whitespace-nowrap text-[10px] mx-1 px-1.5 py-0.5 rounded bg-black/5 dark:bg-white/10 text-black/60 dark:text-white/60 font-semibold uppercase tracking-wide leading-none">
                                        {{ $authorRole }}
                                    </span>
                                @endif
                                <span class="text-black/70 dark:text-white/70">published a new post</span>
                            </p>

                            <p class="text-sm font-bold text-black dark:text-white mt-1 truncate"
                                style="font-family: 'Fraunces', serif;">
                                {{ $post->title }}
                            </p>

                            @if ($post->description)
                                <p class="text-xs text-black/60 dark:text-white/60 mt-0.5 line-clamp-2">
                                    {{ $post->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                @if ($post->category)
                                    <span
                                        class="text-[10px] px-2 py-0.5 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 text-[#123524] dark:text-[#D4A537] font-semibold uppercase tracking-wide">
                                        {{ $post->category->cat_name }}
                                    </span>
                                @endif
                                <span class="text-xs text-black/50 dark:text-white/50">
                                    {{ $post->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Unread dot --}}
                        @if ($isUnread)
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1877F2] shrink-0 mt-2" title="Unread"></span>
                        @endif
                    </a>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-3 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center">
                            <svg class="w-8 h-8 text-black/40 dark:text-white/40" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </div>
                        <p class="font-bold text-black dark:text-white text-lg" style="font-family: 'Fraunces', serif;">
                            {{ $filter === 'unread' ? 'All caught up!' : 'No notifications yet' }}
                        </p>
                        <p class="text-black/50 dark:text-white/50 text-sm mt-1 max-w-xs mx-auto">
                            {{ $filter === 'unread'
                                ? "You've read all your notifications."
                                : 'Announcements from the registrar and program heads will appear here.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($this->notifications->hasPages())
                <div class="flex items-center justify-between pt-2">
                    <p class="text-xs text-black/50 dark:text-white/50">
                        Showing {{ $this->notifications->firstItem() }}–{{ $this->notifications->lastItem() }}
                        of {{ $this->notifications->total() }}
                    </p>

                    <div class="inline-flex gap-2">
                        @if ($this->notifications->onFirstPage())
                            <button disabled
                                class="px-4 py-2 text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                                Prev
                            </button>
                        @else
                            <button wire:click="previousPage"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                Prev
                            </button>
                        @endif

                        @if ($this->notifications->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                Next
                            </button>
                        @else
                            <button disabled
                                class="px-4 py-2 text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            @endif

        </main>
    </div>
</div>
