<!-- ========== HEADER ========== -->
<header
    class="fixed top-0 inset-x-0 z-48 lg:z-61 w-full h-16 select-none
           bg-gradient-to-r from-[#0f2b1c] via-green-800 to-[#0f2b1c]
           border-b border-yellow-500/20 shadow-md">
    <nav class="px-4 sm:px-6 h-full flex items-center w-full mx-auto">
        <div class="w-full flex items-center gap-x-3 min-w-0">

            <!-- Sidebar Toggle -->
            <button type="button"
                class="shrink-0 p-2 inline-flex items-center justify-center rounded-lg text-white/80 hover:text-yellow-400 hover:bg-white/5 transition-colors focus:outline-hidden"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
                data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M15 3v18" />
                    <path d="m10 15-3-3 3-3" />
                </svg>
                <span class="sr-only">Sidebar Toggle</span>
            </button>

            <div class="w-px h-6 bg-white/10 mx-1 shrink-0 hidden sm:block"></div>

            <!-- Logo -->
            <a href="#" class="flex items-center gap-x-3 min-w-0 group">
                <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
                    alt="School Logo"
                    class="w-10 h-10 shrink-0 rounded-lg ring-2 ring-yellow-500/30 group-hover:ring-yellow-400 transition-all shadow-sm">
                <span class="hidden sm:block truncate text-sm md:text-base font-bold text-white tracking-wide group-hover:text-yellow-400 transition-colors">
                    Colegio de Sta. Ana de Victorias
                </span>
            </a>

            <!-- Account Dropdown -->
            <div class="ms-auto shrink-0 hs-dropdown inline-flex [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative text-start">
                <button id="hs-dnad" type="button"
                    class="flex items-center gap-x-2 pl-2 pr-3 py-1.5 rounded-full hover:bg-white/5 transition-colors focus:outline-hidden"
                    aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                    <span class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden md:flex flex-col items-start leading-tight min-w-0">
                        <span class="text-white text-xs font-semibold truncate max-w-32">{{ Auth::user()->name }}</span>
                        <span class="text-white/50 text-[10px]">Admin</span>
                    </span>
                    <svg class="hidden md:block size-3.5 text-white/50 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-64 transition-[opacity,margin] duration opacity-0 hidden z-20
                            bg-[#12331f] border border-yellow-500/20 rounded-xl shadow-2xl overflow-hidden"
                    role="menu" aria-orientation="vertical" aria-labelledby="hs-dnad">

                    <div class="flex items-center gap-x-3 py-3 px-4 bg-white/5">
                        <span class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-white text-sm truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-white/60 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="p-2 border-t border-white/10">
                        <livewire:auth::logout />
                    </div>
                </div>
            </div>

        </div>
    </nav>
</header>
<!-- ========== END HEADER ========== -->
