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

            @php
                $__authUser = auth()->user();
                $__rawAvatar = $__authUser?->userProfile?->avatar;
                $__avatarUrl = $__rawAvatar
                    ? (filter_var($__rawAvatar, FILTER_VALIDATE_URL) ? $__rawAvatar : \Illuminate\Support\Facades\Storage::url($__rawAvatar))
                    : null;
                $__initial = strtoupper(substr($__authUser->name ?? '?', 0, 1));
            @endphp

            <!-- Account Dropdown -->
            <div class="ms-auto shrink-0 hs-dropdown inline-flex [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative text-start">
                <button id="hs-dnad" type="button"
                    class="flex items-center gap-x-2 pl-2 pr-3 py-1.5 rounded-full hover:bg-white/5 transition-colors focus:outline-hidden"
                    aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">

                    {{-- Avatar (image or initial) --}}
                    @if ($__avatarUrl)
                        <img src="{{ $__avatarUrl }}"
                             alt="{{ $__authUser->name }}"
                             class="w-9 h-9 rounded-full object-cover ring-2 ring-yellow-500/40 shrink-0"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span style="display: none;"
                              class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $__initial }}
                        </span>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center text-base font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $__initial }}
                        </span>
                    @endif

                    <span class="hidden md:flex flex-col items-start leading-tight min-w-0">
                        <span class="text-white text-xs font-semibold truncate max-w-32">{{ $__authUser->name }}</span>
                        <span class="text-white/50 text-[10px]">Registrar</span>
                    </span>
                    <svg class="hidden md:block size-3.5 text-white/50 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-64 transition-[opacity,margin] duration opacity-0 hidden z-20
                            bg-[#12331f] border border-yellow-500/20 rounded-xl shadow-2xl overflow-hidden"
                    role="menu" aria-orientation="vertical" aria-labelledby="hs-dnad">

                    {{-- Profile header --}}
                    <div class="flex items-center gap-x-3 py-3 px-4 bg-white/5">
                        @if ($__avatarUrl)
                            <img src="{{ $__avatarUrl }}"
                                 alt="{{ $__authUser->name }}"
                                 class="w-10 h-10 rounded-full object-cover ring-2 ring-yellow-500/40 shrink-0"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span style="display: none;"
                                  class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                                {{ $__initial }}
                            </span>
                        @else
                            <span class="w-10 h-10 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                                {{ $__initial }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <p class="font-bold text-white text-sm truncate">{{ $__authUser->name }}</p>
                            <p class="text-xs text-white/60 truncate">{{ $__authUser->email }}</p>
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="p-2 border-t border-white/10">
                        <a href="{{ route('super-admin.settings') }}"
                           class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5 transition-colors text-sm font-medium">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Settings</span>
                        </a>
                    </div>

                    {{-- Logout --}}
                    <div class="p-2 border-t border-white/10">
                        <livewire:auth::logout />
                    </div>
                </div>
            </div>

        </div>
    </nav>
</header>
<!-- ========== END HEADER ========== -->