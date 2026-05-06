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
        bg-green-100 
        lg:block lg:-translate-x-full lg:end-auto lg:bottom-0"
        role="dialog" tabindex="-1" aria-label="Sidebar">
        <div class="lg:pt-15 relative flex flex-col h-full max-h-full">
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
                        class="flex-1 bg-transparent border-none text-medium text-black dark:text-black" />
                </button>


                <div 
                    class="pt-3 mt-3 flex flex-col border-t border-green-700 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-extrabold text-xs uppercase text-green-700">
                        Home
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-green-700 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-extrabold text-xs uppercase text-green-700">
                        Users
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.user.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000000" viewBox="0 0 256 256">
                                    <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z"></path>
                                </svg>
                                View All Users
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.admin.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                View All Admins
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.alumni.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                               View All Alumni
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-green-700 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-extrabold text-xs uppercase text-green-700">
                       Departments
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.department.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000000" viewBox="0 0 256 256">
                                    <path d="M231.65,194.55,198.46,36.75a16,16,0,0,0-19-12.39L132.65,34.42a16.08,16.08,0,0,0-12.3,19l33.19,157.8A16,16,0,0,0,169.16,224a16.25,16.25,0,0,0,3.38-.36l46.81-10.06A16.09,16.09,0,0,0,231.65,194.55ZM136,50.15c0-.06,0-.09,0-.09l46.8-10,3.33,15.87L139.33,66Zm6.62,31.47,46.82-10.05,3.34,15.9L146,97.53Zm6.64,31.57,46.82-10.06,13.3,63.24-46.82,10.06ZM216,197.94l-46.8,10-3.33-15.87L212.67,182,216,197.85C216,197.91,216,197.94,216,197.94ZM104,32H56A16,16,0,0,0,40,48V208a16,16,0,0,0,16,16h48a16,16,0,0,0,16-16V48A16,16,0,0,0,104,32ZM56,48h48V64H56Zm0,32h48v96H56Zm48,128H56V192h48v16Z"></path>
                                </svg>
                                View All Departments
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.courses.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                View All Courses
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-green-700 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-extrabold text-xs uppercase text-green-700">
                        Assign Roles & Permissions
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('view-role') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-key-icon lucide-user-round-key">
                                    <path d="M19 11v6"/><path d="M19 13h2"/><path d="M2 21a8 8 0 0 1 12.868-6.349"/><circle cx="10" cy="8" r="5"/><circle cx="19" cy="19" r="2"/>
                                </svg>
                                Roles
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.assign.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <polyline points="17 11 19 13 23 9"/>
                                </svg>
                                Assign Department
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.batch.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                                Batch Years
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->
                    
                </div>

                <div
                    class="pt-3 mt-3 flex flex-col border-t border-green-700 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-extrabold text-xs uppercase text-green-700">
                        Requests
                    </span>

                    <!-- List -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.request.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000000" viewBox="0 0 256 256">
                                    <path d="M216,48H40A16,16,0,0,0,24,64V192a16,16,0,0,0,16,16H96v24a8,8,0,0,0,13.66,5.66l56-56A8,8,0,0,0,160,168V160h56a16,16,0,0,0,16-16V64A16,16,0,0,0,216,48ZM40,64H216V80H128a8,8,0,0,0-8,8v72H40ZM180.69,130.34a8.1,8.1,0,1,1-11.38-11.68l14-13.67a8.1,8.1,0,1,1,11.38,11.68Z"></path>
                                </svg>
                                Application Requests
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-black rounded-lg hover:bg-gray-100 focus:outline-hidden"
                                wire:current="bg-gray-100"
                                href="{{ route('super-admin.email.view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000000" viewBox="0 0 256 256">
                                    <path d="M224,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H96v24a8,8,0,0,0,13.66,5.66l56-56A8,8,0,0,0,160,168V160h56a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM40,64H216V80H128a8,8,0,0,0-8,8v72H40ZM180.69,130.34a8.1,8.1,0,1,1-11.38-11.68l14-13.67a8.1,8.1,0,1,1,11.38,11.68Z"></path>
                                </svg>
                                Email Templates
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
