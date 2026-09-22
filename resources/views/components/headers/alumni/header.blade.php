@php
    $__authUser = auth()->user();
    $__initial = strtoupper(substr($__authUser->name ?? '?', 0, 1));

    $__rawAvatar = $__authUser?->userProfile?->avatar;
    $__avatarUrl = $__rawAvatar
        ? (filter_var($__rawAvatar, FILTER_VALIDATE_URL)
            ? $__rawAvatar
            : \Illuminate\Support\Facades\Storage::url($__rawAvatar))
        : null;

    // Badge counts — cached 20s inside the helper
    $__badges = \App\Support\BadgeCounts::forCurrentUser();
@endphp

<!-- ========== FACEBOOK-STYLE HEADER ========== -->
<header
    id="alumni-header"
    data-badges-messages="{{ $__badges['messages'] }}"
    data-badges-notifications="{{ $__badges['notifications'] }}"
    class="w-full bg-gradient-to-r from-[#0f2b1c] via-green-800 to-[#0f2b1c] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-50 select-none">
    <nav class="max-w-[1100px] mx-auto flex items-center justify-between px-4 py-2 gap-4">

        <!-- Logo -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('alumni.dashboard') }}" class="shrink-0">
                <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
                    alt="School Logo" class="w-10 h-10 rounded-full ring-1 ring-[#D4A537]/50 object-cover">
            </a>
        </div>

        <!-- Center Navigation (Desktop) -->
        <ul class="hidden lg:flex flex-row items-center justify-center flex-1 gap-1 text-white/60 dark:text-white/60">

            {{-- Home --}}
            <li>
                <a href="{{ route('alumni.dashboard') }}"
                    class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition relative group
               {{ request()->routeIs('alumni.dashboard') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </a>
            </li>

            {{-- Message --}}
            <li>
                <a href="{{ route('alumni.message') }}"
                    class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition relative group
               {{ request()->routeIs('alumni.message') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                    <span data-badge="messages"
                          class="absolute top-1.5 right-5 hidden items-center justify-center
                                 min-w-[18px] h-[18px] px-1 rounded-full
                                 bg-[#E41E3F] text-white text-[10px] font-bold leading-none
                                 ring-2 ring-[#0f2b1c] pointer-events-none">
                    </span>
                </a>
            </li>

            {{-- Notifications --}}
            <li>
                <a href="{{ route('alumni.notification') }}"
                    class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition relative group
               {{ request()->routeIs('alumni.notification') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span data-badge="notifications"
                          class="absolute top-1.5 right-5 hidden items-center justify-center
                                 min-w-[18px] h-[18px] px-1 rounded-full
                                 bg-[#E41E3F] text-white text-[10px] font-bold leading-none
                                 ring-2 ring-[#0f2b1c] pointer-events-none">
                    </span>
                </a>
            </li>
        </ul>

        <!-- Desktop: Profile Dropdown (avatar trigger) -->
        <div class="hidden lg:block relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-black/5 transition"
                :aria-expanded="open">
                @if ($__avatarUrl)
                    <img src="{{ $__avatarUrl }}" alt="{{ $__authUser->name }}"
                        class="w-9 h-9 rounded-full object-cover ring-1 ring-yellow-500/40 shrink-0"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span style="display: none;"
                        class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                        {{ $__initial }}
                    </span>
                @else
                    <span
                        class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                        {{ $__initial }}
                    </span>
                @endif
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" x-cloak
                class="absolute right-0 mt-2 w-64 bg-[#12331f] border border-yellow-500/20 rounded-xl shadow-2xl overflow-hidden z-50"
                role="menu">

                <div class="flex items-center gap-x-3 py-3 px-4 bg-white/5">
                    @if ($__avatarUrl)
                        <img src="{{ $__avatarUrl }}" alt="{{ $__authUser->name }}"
                            class="w-10 h-10 rounded-full object-cover ring-1 ring-yellow-500/40 shrink-0"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span style="display: none;"
                            class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $__initial }}
                        </span>
                    @else
                        <span
                            class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $__initial }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-white text-sm truncate">{{ $__authUser->name ?? 'Alumni' }}</p>
                        <p class="text-xs text-white/60 truncate">{{ $__authUser->email ?? '' }}</p>
                    </div>
                </div>

                {{-- My Profile --}}
                <div class="p-2 border-t border-white/10">
                    <a href="{{ route('alumni.profile') }}"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>My Profile</span>
                    </a>
                </div>

                {{-- Settings --}}
                <div class="p-2 border-t border-white/10">
                    <a href="{{ route('alumni.settings') }}"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                </div>

                <div class="p-2 border-t border-white/10">
                    <livewire:auth::logout />
                </div>
            </div>
        </div>

        <!-- Mobile: Menu Toggle -->
        <div class="flex lg:hidden items-center">
            <button
                class="p-2 rounded-full bg-green-700 hover:bg-green-600 active:bg-green-800 transition ring-1 ring-white/10"
                id="menu-toggle">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <line x1="4" x2="20" y1="6" y2="6" />
                    <line x1="4" x2="20" y1="12" y2="12" />
                    <line x1="4" x2="20" y1="18" y2="18" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="hidden flex-col gap-y-1 px-4 pb-3 lg:hidden bg-white dark:bg-[#1a1a1a] border-t border-black/10 dark:border-white/5">

        {{-- Profile header inside mobile menu --}}
        <a href="{{ route('alumni.profile') }}"
            class="flex items-center gap-x-3 py-4 px-3 rounded-lg hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors mt-2">
            @if ($__avatarUrl)
                <img src="{{ $__avatarUrl }}" alt="{{ $__authUser->name }}"
                    class="w-11 h-11 rounded-full object-cover ring-2 ring-yellow-500/40 shrink-0"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span style="display: none;"
                    class="w-11 h-11 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                    {{ $__initial }}
                </span>
            @else
                <span
                    class="w-11 h-11 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                    {{ $__initial }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="font-bold text-black dark:text-white text-sm truncate">{{ $__authUser->name ?? 'Alumni' }}</p>
                <p class="text-xs text-black/50 dark:text-white/50 truncate">{{ $__authUser->email ?? '' }}</p>
            </div>
        </a>

        <div class="my-1 border-t border-black/10 dark:border-white/10"></div>

        {{-- Home --}}
        <a href="{{ route('alumni.dashboard') }}"
            class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="font-medium">Home</span>
        </a>

        {{-- My Profile --}}
        <a href="{{ route('alumni.profile') }}"
            class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">My Profile</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('alumni.settings') }}"
            class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="font-medium">Settings</span>
        </a>

        {{-- Message --}}
        <a href="{{ route('alumni.message') }}"
            class="w-full flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors"
            title="Messages">
            <div class="relative shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
                <span data-badge="messages"
                      class="absolute -top-1.5 -right-2 hidden items-center justify-center
                             min-w-[16px] h-4 px-1 rounded-full
                             bg-[#E41E3F] text-white text-[9px] font-bold leading-none">
                </span>
            </div>
            <span class="font-medium">Messages</span>
        </a>

        {{-- Notifications --}}
        <a href="{{ route('alumni.notification') }}"
            class="w-full flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors"
            title="Notifications">
            <div class="relative shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span data-badge="notifications"
                      class="absolute -top-1.5 -right-2 hidden items-center justify-center
                             min-w-[16px] h-4 px-1 rounded-full
                             bg-[#E41E3F] text-white text-[9px] font-bold leading-none">
                </span>
            </div>
            <span class="font-medium">Notifications</span>
        </a>

        {{-- Logout --}}
        <div class="
            [&>*]:w-full
            [&_button]:flex [&_button]:items-center [&_button]:justify-start [&_button]:gap-x-3
            [&_button]:w-full
            [&_button]:px-3 [&_button]:py-3 [&_button]:rounded-lg
            [&_button]:text-base [&_button]:font-medium
            [&_button]:text-red-600 dark:[&_button]:text-red-400
            [&_button]:hover:bg-red-50 dark:[&_button]:hover:bg-red-500/10
            [&_button]:transition-colors
            [&_svg]:w-6 [&_svg]:h-6 [&_svg]:shrink-0
            [&_span]:truncate
        ">
            <livewire:auth::logout />
        </div>
    </div>

</header>
<!-- ========== END HEADER ========== -->

<script>
    // ============= MOBILE MENU TOGGLE =============
    document.getElementById('menu-toggle')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
    });

    // ============= THEME RESTORE =============
    (function() {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();

    // ============= BADGE COUNTS =============
    (function () {
        function paintCounts(counts) {
            document.querySelectorAll('[data-badge]').forEach(el => {
                const n = counts[el.dataset.badge] ?? 0;

                if (n > 0) {
                    el.textContent = n > 99 ? '99+' : String(n);
                    el.style.display = 'inline-flex';
                } else {
                    el.style.display = 'none';
                    el.textContent = '';
                }
            });
        }

        function readInitial() {
            const header = document.getElementById('alumni-header');
            if (!header) return;

            paintCounts({
                messages:      parseInt(header.dataset.badgesMessages || '0', 10),
                notifications: parseInt(header.dataset.badgesNotifications || '0', 10),
            });
        }

        async function refreshCounts() {
            try {
                const res = await fetch('/alumni/badges.json', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                paintCounts(await res.json());
            } catch (e) {
                // Silent — badges are cosmetic
            }
        }

        document.addEventListener('DOMContentLoaded', readInitial);
        document.addEventListener('livewire:navigated', readInitial);
        window.addEventListener('badges:refresh', refreshCounts);

        // Catch browser bfcache restores (native Back/Forward button) —
        // these skip DOMContentLoaded entirely, so without this the page
        // reappears frozen with whatever counts it had before navigating away.
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                refreshCounts();
            }
        });

        // Poll every 30s, only while tab is visible
        let intervalId = null;

        function startPolling() {
            if (intervalId) return;
            intervalId = setInterval(refreshCounts, 30000);
        }
        function stopPolling() {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        }

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                refreshCounts();
                startPolling();
            } else {
                stopPolling();
            }
        });

        startPolling();
    })();
</script>