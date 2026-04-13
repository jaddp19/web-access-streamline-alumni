<!-- ========== HEADER ========== -->
<header
    class="fixed top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-48 lg:z-61 w-full bg-green-600 text-sm py-2.5">
    <nav class="px-4 sm:px-5.5 flex basis-full items-center w-full mx-auto">
        <div class="w-full flex items-center gap-x-1.5">
            <h2 class="font-semibold text-yellow-500 text-center">Colegio de Sta. Ana de Victorias - Alumni</h2>
            <ul class="flex flex-row items-center gap-x-3 ms-auto">
                <li class="inline-flex items-center gap-1.5 relative text-yellow-500 pe-3 last:pe-0 last:after:hidden after:absolute after:top-1/2 after:end-0 after:inline-block after:w-px after:h-3.5 after:bg-gray-300 after:rounded-full after:-translate-y-1/2 after:rotate-12">
                    <div class="h-8">
                        <!-- Account Dropdown -->
                        <div
                            class="hs-dropdown inline-flex [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative text-start">
                            <button id="hs-dnad" type="button"
                                class="p-0.5 inline-flex shrink-0 items-center gap-x-3 text-start rounded-full hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 dark:hover:bg-neutral-800 dark:hover:text-neutral-200 dark:focus:bg-neutral-800 dark:focus:text-neutral-200 dark:text-neutral-500"
                                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                <img class="shrink-0 size-7 rounded-full"
                                    src="https://images.unsplash.com/photo-1659482633369-9fe69af50bfb?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=3&w=320&h=320&q=80"
                                    alt="Avatar">
                            </button>

                            <!-- Account Dropdown -->
                            <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 hidden z-20 bg-white border border-gray-200 rounded-xl shadow-xl dark:bg-neutral-900 dark:border-neutral-700"
                                role="menu" aria-orientation="vertical" aria-labelledby="hs-dnad">
                                <div class="py-2 px-3.5">
                                    {{-- <span class="font-medium text-gray-800 dark:text-neutral-300">
                                        {{ Auth::user()->name }}
                                    </span>
                                    <p class="text-sm text-gray-500 dark:text-neutral-500">
                                        {{ Auth::user()->email }}
                                    </p> --}}
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200 dark:border-neutral-800">
                                    <!-- Switch/Toggle -->
                                    <div class="flex flex-wrap justify-between items-center gap-2">
                                        <span
                                            class="flex-1 cursor-pointer text-sm text-gray-600 dark:text-neutral-400">Theme</span>
                                        <div
                                            class="p-0.5 inline-flex cursor-pointer bg-gray-100 rounded-full dark:bg-neutral-800">
                                            <button type="button"
                                                class="size-7 flex justify-center items-center bg-white shadow-sm text-gray-800 rounded-full dark:text-neutral-200 hs-auto-mode-active:bg-transparent hs-auto-mode-active:shadow-none hs-dark-mode-active:bg-transparent hs-dark-mode-active:shadow-none"
                                                data-hs-theme-click-value="default">
                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="4" />
                                                    <path d="M12 3v1" />
                                                    <path d="M12 20v1" />
                                                    <path d="M3 12h1" />
                                                    <path d="M20 12h1" />
                                                    <path d="m18.364 5.636-.707.707" />
                                                    <path d="m6.343 17.657-.707.707" />
                                                    <path d="m5.636 5.636.707.707" />
                                                    <path d="m17.657 17.657.707.707" />
                                                </svg>
                                                <span class="sr-only">Default (Light)</span>
                                            </button>
                                            <button type="button"
                                                class="size-7 flex justify-center items-center text-gray-800 rounded-full dark:text-neutral-200 hs-dark-mode-active:bg-white hs-dark-mode-active:shadow-sm hs-dark-mode-active:text-neutral-800"
                                                data-hs-theme-click-value="dark">
                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                                                </svg>
                                                <span class="sr-only">Dark</span>
                                            </button>
                                            <button type="button"
                                                class="size-7 flex justify-center items-center text-gray-800 rounded-full dark:text-neutral-200 hs-auto-light-mode-active:bg-white hs-auto-dark-mode-active:bg-red-800 hs-auto-mode-active:shadow-sm"
                                                data-hs-theme-click-value="auto">
                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="3" rx="2" />
                                                    <line x1="8" x2="16" y1="21" y2="21" />
                                                    <line x1="12" x2="12" y1="17" y2="21" />
                                                </svg>
                                                <span class="sr-only">Auto (System)</span>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- End Switch/Toggle -->
                                </div>
                                <div class="p-1 border-t border-gray-200 dark:border-neutral-800">
                                    {{-- <livewire:auth::logout /> --}}
                                </div>
                            </div>
                            <!-- End Account Dropdown -->
                        </div>
                        <!-- End Account Dropdown -->
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!-- ========== END HEADER ========== -->   
