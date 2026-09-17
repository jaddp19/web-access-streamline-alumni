@php
    $__authUser = auth()->user();
    $__initial = strtoupper(substr($__authUser->name ?? '?', 0, 1));
@endphp

<!-- ========== FACEBOOK-STYLE HEADER ========== -->
<header class="w-full bg-gradient-to-r from-[#0f2b1c] via-green-800 to-[#0f2b1c] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-50 select-none">
  <nav class="max-w-[1100px] mx-auto flex items-center justify-between px-4 py-2 gap-4">

    <!-- Logo + Search -->
    <div class="flex items-center gap-3 min-w-0">
      <a href="{{ route('alumni.dashboard') }}" class="shrink-0">
        <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
             alt="School Logo"
             class="w-10 h-10 rounded-full ring-1 ring-[#D4A537]/50 object-cover">
      </a>
    </div>

    <!-- Center Navigation (Desktop) -->
    <ul class="hidden lg:flex flex-row items-center justify-center flex-1 gap-1 text-white/60 dark:text-white/60">

        <li>
            <a href="{{ route('alumni.dashboard') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition relative group
               {{ request()->routeIs('alumni.dashboard') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </a>
        </li>
        <li>
            <a href="{{ route('alumni.message') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition relative group
               {{ request()->routeIs('alumni.message') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </a>
        </li>
        <li>
            <a href="{{ route('alumni.settings') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 transition
               {{ request()->routeIs('alumni.settings') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>
        </li>
    </ul>

    <!-- Desktop: Profile Dropdown (avatar trigger) -->
    <div class="hidden lg:block relative" x-data="{ open: false }" @click.outside="open = false">
        <button @click="open = !open"
                class="flex items-center gap-2 p-1.5 rounded-full hover:bg-black/5 transition"
                :aria-expanded="open">
            <span class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                {{ $__initial }}
            </span>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak
             class="absolute right-0 mt-2 w-64 bg-[#12331f] border border-yellow-500/20 rounded-xl shadow-2xl overflow-hidden z-50"
             role="menu">

            <div class="flex items-center gap-x-3 py-3 px-4 bg-white/5">
                <span class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                    {{ $__initial }}
                </span>
                <div class="min-w-0">
                    <p class="font-bold text-white text-sm truncate">{{ $__authUser->name ?? 'Alumni' }}</p>
                    <p class="text-xs text-white/60 truncate">{{ $__authUser->email ?? '' }}</p>
                </div>
            </div>

            {{-- Theme toggle --}}
            <div class="p-2 border-t border-white/10"
                 x-data="{
                    theme: localStorage.getItem('theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'),
                    toggle() {
                        this.theme = this.theme === 'dark' ? 'light' : 'dark';
                        localStorage.setItem('theme', this.theme);
                        document.documentElement.classList.toggle('dark', this.theme === 'dark');
                    }
                 }">
                <button type="button"
                        @click="toggle()"
                        class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5 transition-colors text-sm font-medium">
                    <span class="flex items-center gap-3">
                        {{-- Sun icon (visible in light mode) --}}
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        {{-- Moon icon (visible in dark mode) --}}
                        <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <span x-text="theme === 'dark' ? 'Dark mode' : 'Light mode'">Dark mode</span>
                    </span>

                    {{-- Toggle track --}}
                    <span class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors"
                          :class="theme === 'dark' ? 'bg-yellow-500' : 'bg-white/20'">
                        <span class="absolute top-0.5 left-0.5 inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform"
                              :class="theme === 'dark' ? 'translate-x-4' : 'translate-x-0'"></span>
                    </span>
                </button>
            </div>

            <div class="p-2 border-t border-white/10">
                <livewire:auth::logout />
            </div>
        </div>
    </div>

    <!-- Mobile: Menu Toggle only -->
    <div class="flex lg:hidden items-center">
        <button class="p-2 rounded-full bg-[#F0F2F5] dark:bg-[#3a3b3c] hover:bg-[#E4E6EB] dark:hover:bg-[#4e4f50] transition" id="menu-toggle">
            <svg class="w-5 h-5 text-black dark:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="4" x2="20" y1="6" y2="6"/>
                <line x1="4" x2="20" y1="12" y2="12"/>
                <line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden flex-col gap-y-1 px-4 pb-3 lg:hidden bg-white dark:bg-[#1a1a1a] border-t border-black/10 dark:border-white/5">

      {{-- Profile header inside mobile menu --}}
      <a href="{{ route('alumni.profile') }}"
         class="flex items-center gap-x-3 py-4 px-3 rounded-lg hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors mt-2">
          <span class="w-11 h-11 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
              {{ $__initial }}
          </span>
          <div class="min-w-0">
              <p class="font-bold text-black dark:text-white text-sm truncate">{{ $__authUser->name ?? 'Alumni' }}</p>
              <p class="text-xs text-black/50 dark:text-white/50 truncate">{{ $__authUser->email ?? '' }}</p>
          </div>
      </a>

      <div class="my-1 border-t border-black/10 dark:border-white/10"></div>

      <a href="{{ route('alumni.dashboard') }}" class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
          <span class="font-medium">Home</span>
      </a>
      <a href="{{ route('alumni.settings') }}" class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          <span class="font-medium">Settings</span>
      </a>

      <a type="button" href="{{ route('alumni.message') }}" class="w-full flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors" title="Notifications">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
          <span class="font-medium">Notifications</span>
      </a>

      {{-- Theme toggle (mobile) --}}
      <div x-data="{
            theme: localStorage.getItem('theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'),
            toggle() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', this.theme);
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
            }
         }">
          <button type="button"
                  @click="toggle()"
                  class="w-full flex items-center justify-between gap-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors font-medium">
              <span class="flex items-center gap-3">
                  <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                  </svg>
                  <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                  </svg>
                  <span x-text="theme === 'dark' ? 'Dark mode' : 'Light mode'">Dark mode</span>
              </span>
              <span class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors"
                    :class="theme === 'dark' ? 'bg-yellow-500' : 'bg-black/20 dark:bg-white/20'">
                  <span class="absolute top-0.5 left-0.5 inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform"
                        :class="theme === 'dark' ? 'translate-x-4' : 'translate-x-0'"></span>
              </span>
          </button>
      </div>

      

      <div class="my-1 border-t border-black/10 dark:border-white/10"></div>

      <div class="[&>*]:w-full [&_button]:flex [&_button]:items-center [&_button]:gap-x-3 [&_button]:px-3 [&_button]:py-3 [&_button]:rounded-lg [&_button]:text-red-600 dark:[&_button]:text-red-400 [&_button]:hover:bg-red-50 dark:[&_button]:hover:bg-red-500/10 [&_button]:transition-colors [&_button]:font-medium">
          <livewire:auth::logout />
      </div>
  </div>

</header>
<!-- ========== END HEADER ========== -->

<script>
  document.getElementById('menu-toggle')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
  });

  // Restore theme on page load (before Alpine kicks in)
  (function () {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  })();
</script>