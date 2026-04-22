<div>
    <!-- ========== MAIN SIDEBAR ========== -->
    <!-- Sidebar -->
    <div id="hs-pro-sidebar"
        class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
        hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
        -translate-x-full transition-all duration-300 transform
        w-60
        hidden
        fixed inset-y-0 z-60 start-0
        bg-green-600    
        lg:block lg:-translate-x-full lg:end-auto lg:bottom-0"
        role="dialog" tabindex="-1" aria-label="Sidebar">
        <div class="lg:pt-13 relative flex flex-col h-full max-h-full">
            <!-- Body -->
            <nav
                class="p-3 size-full flex flex-col overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-200 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="lg:hidden mb-2 flex items-center justify-between">

                    <!-- Sidebar Toggle -->
                    <button type="button"
                        class="p-1.5 size-7.5 inline-flex items-center gap-x-1 text-xs rounded-md text-green-700isabled:pointer-events-none focus:outline-hidden dark:text-neutral-500"
                        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
                        data-hs-overlay="#hs-pro-sidebar">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                        <span class="sr-only">Sidebar Toggle</span>
                    </button>
                    <!-- End Sidebar Toggle -->
                </div>

                <button type="button"
                    class="p-1.5 ps-2.5 w-full inline-flex items-center gap-x-2 text-sm rounded-lg bg-white border border-gray-200 text-gray-600 shadow-xs hover:border-gray-300 focus:outline-hidden focus:border-gray-300 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-neutral-600 dark:focus:border-neutral-600"
                    aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-cmsssm"
                    data-hs-overlay="#hs-pro-cmsssm">

                    <!-- Fixed alignment -->
                    <input type="text"
                        placeholder="Search"
                        class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-black  dark:text-white" />
                </button>


                <div 
                    class="pt-3 mt-3 flex flex-col border-t border-gray-200 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-yellow-500">
                        Home
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-yellow-500 focus:outline-hidden focus:bg-bg-yellow-400"
                                wire:current="bg-yellow-500"
                                href="{{ route('admin.dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-gray-200 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-yellow-500">
                        Users
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-yellow-500 focus:outline-hidden focus:bg-bg-yellow-400"
                                wire:current="bg-yellow-500"
                                href="#">
                                View All Alumnis
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-gray-200 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-yellow-500">
                        Assign Roles & Permissions
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-yellow-500 focus:outline-hidden focus:bg-yellow-400"
                                wire:current="bg-yellow-500"
                                href="#">
                                Roles
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>
            </nav>
            <!-- End Body -->
        </div>
    </div>
    <!-- End Sidebar -->
    <!-- ========== END MAIN SIDEBAR ========== -->
    <script>
        document.addEventListener("DOMContentLoaded", function ()
        {
            const searchInput = document.querySelector('#hs-pro-sidebar input[type="text"]');
            const links = document.querySelectorAll('#hs-pro-sidebar nav a');

            searchInput.addEventListener("input", function ()
            {
                const query = this.value.toLowerCase().trim();

                links.forEach(link =>
                {
                    const text = link.textContent.toLowerCase();
                    if (text.includes(query))
                    {
                        link.parentElement.style.display = "";
                    } else {
                        link.parentElement.style.display = "none";
                    }
                });
            });
        });
    </script>
</div>
