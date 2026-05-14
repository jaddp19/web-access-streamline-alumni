<!-- ========== HEADER ========== -->
<header
    class="fixed top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-48 lg:z-61 w-full bg-green-800 text-sm py-2.5">
    <nav class="px-4 sm:px-5.5 flex basis-full items-center w-full mx-auto">
        <div class="w-full flex items-center gap-x-1.5">
            <ul class="flex items-center gap-1.5">
                <li class="inline-flex items-center relative text-yellow-400 pe-1.5 last:pe-0 last:after:hidden after:absolute after:top-1/2 after:end-0 after:inline-block after:w-px after:h-3.5 after:bg-gray-300 after:rounded-full after:-translate-y-1/2 after:rotate-12 dark:after:bg-neutral-700">          
                    <!-- Sidebar Toggle -->
                    <button type="button"
                        class="p-1.5 size-7.5 inline-flex items-center gap-x-1 text-xs rounded-md border border-transparent text-white hover:text-gray-200 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden"
                        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
                        data-hs-overlay="#hs-pro-sidebar">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M15 3v18" />
                            <path d="m10 15-3-3 3-3" />
                        </svg>
                        <span class="sr-only">Sidebar Toggle</span>
                    </button>
                    <!-- End Sidebar Toggle -->
                </li>
            </ul>
            <!-- Logo -->
            <a href="#" class="flex items-center gap-x-3">
            <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
                alt="School Logo"
                class="w-12 h-12 rounded-md border-2 border-transparent shadow-sm">
            <span class="text-xs sm:text-lg font-bold text-white tracking-wide hover:text-yellow-400 transition-colors">
                Colegio de Sta. Ana de Victorias
            </span>
            </a>
            <!-- End Logo -->

            <ul class="flex flex-row items-center gap-x-3 ms-auto">
                <li class="inline-flex items-center gap-1.5 relative text-yellow-500 pe-3 last:pe-0 last:after:hidden after:absolute after:top-1/2 after:end-0 after:inline-block after:w-px after:h-3.5 after:rounded-full after:-translate-y-1/2 after:rotate-12">
                    <!-- Account Dropdown -->
                    <div
                        class="hs-dropdown inline-flex [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative text-start">
                        <button id="hs-dnad" type="button"
                            class="w-12 h-12 flex items-center justify-center text-2xl font-bold text-white bg-green-700 rounded-full hover:bg-yellow-500 focus:outline-hidden focus:bg-yellow-500"
                            aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </button>


                        <!-- Account Dropdown -->
                        <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 hidden z-20 bg-green-700 border border-yellow-500 rounded-xl shadow-xl"
                            role="menu" aria-orientation="vertical" aria-labelledby="hs-dnad">
                            <div class="py-2 px-3.5">
                                <span class="font-extrabold text-white">
                                    {{ Auth::user()->name }}
                                </span>
                                <p class="text-sm text-white">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                                
                            <div class="p-1 border-t">
                                <livewire:auth::logout />
                            </div>
                        </div>
                        <!-- End Account Dropdown -->
                    </div>
                    <!-- End Account Dropdown -->
                </li>
            </ul>
        </div>
    </nav>
</header>
<!-- ========== END HEADER ========== -->   
