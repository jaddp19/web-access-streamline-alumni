<div>
    <!-- ========== MAIN SIDEBAR - SUPER ADMIN DASHBOARD (Preline UI) ========== -->
    <div id="hs-pro-sidebar"
         class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
                hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
                -translate-x-full transition-all duration-300 transform
                w-72
                hidden
                fixed inset-y-0 z-60 start-0
                bg-emerald-700 border-r border-emerald-800
                lg:block lg:-translate-x-full lg:end-auto lg:bottom-0"
         role="dialog"
         tabindex="-1"
         aria-label="Super Admin Sidebar">

        <div class="flex flex-col h-full max-h-full absolute">

            <!-- SIDEBAR HEADER -->
            <div class="px-6 pt-6 pb-4 border-b border-emerald-800 flex items-center gap-x-3">
                <!-- Logo / Brand -->
                <div class="flex items-center gap-x-2">
                    <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center text-emerald-700 font-bold text-xl shadow-inner">
                        👑
                    </div>
                    <div>
                        <span class="font-semibold text-white text-xl tracking-tight">SuperAdmin</span>
                    </div>
                </div>

                <!-- Mobile Close Button -->
                <button type="button"
                        class="lg:hidden ms-auto p-2 rounded-xl text-emerald-200 hover:bg-emerald-600 hover:text-white focus:outline-hidden"
                        aria-haspopup="dialog"
                        aria-expanded="false"
                        aria-controls="hs-pro-sidebar"
                        data-hs-overlay="#hs-pro-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6h12v12" />
                    </svg>
                </button>
            </div>

            <!-- SEARCH BAR -->
            <div class="px-4 pt-6 pb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 01-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input id="sidebar-search"
                           type="text"
                           placeholder="Search menu..."
                           class="w-full bg-emerald-600 border border-emerald-500 focus:border-emerald-400 text-white placeholder-emerald-300 rounded-2xl py-3 ps-11 pe-4 text-sm focus:ring-0 outline-none transition-colors">
                </div>
            </div>

            <!-- NAVIGATION -->
            <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                <div class="space-y-8">

                    <!-- HOME SECTION -->
                    <div>
                        <span class="block px-3 mb-2 text-xs font-semibold uppercase tracking-widest text-emerald-300">
                            Home
                        </span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('super-admin.dashboard') }}"
                                   wire:current="bg-emerald-600 text-white shadow-sm"
                                   class="group flex items-center gap-x-3 px-4 py-3 text-sm font-medium text-emerald-100 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- USERS SECTION -->
                    <div>
                        <span class="block px-3 mb-2 text-xs font-semibold uppercase tracking-widest text-emerald-300">
                            Users
                        </span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('admin.user.view') }}"
                                   wire:current="bg-emerald-600 text-white shadow-sm"
                                   class="group flex items-center gap-x-3 px-4 py-3 text-sm font-medium text-emerald-100 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 01-5.356-1.857M17 20H7m5-2v-2c0-.656-.126-1.284-.356-1.852M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.284.356-1.852m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>View All Users</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.admin.view') }}"
                                   wire:current="bg-emerald-600 text-white shadow-sm"
                                   class="group flex items-center gap-x-3 px-4 py-3 text-sm font-medium text-emerald-100 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M5 19.5l-1.5-1.5M19.5 19.5l1.5-1.5M12 19.5V12" />
                                    </svg>
                                    <span>View Faculty Members</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.alumni.view') }}"
                                   wire:current="bg-emerald-600 text-white shadow-sm"
                                   class="group flex items-center gap-x-3 px-4 py-3 text-sm font-medium text-emerald-100 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18 9.246 18 10.832 18.477 12 19.253zm0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18 14.754 18 13.168 18.477 12 19.253z" />
                                    </svg>
                                    <span>View All Alumni</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- ROLES & PERMISSIONS -->
                    <div>
                        <span class="block px-3 mb-2 text-xs font-semibold uppercase tracking-widest text-emerald-300">
                            Roles &amp; Permissions
                        </span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('view-role') }}"
                                   wire:current="bg-emerald-600 text-white shadow-sm"
                                   class="group flex items-center gap-x-3 px-4 py-3 text-sm font-medium text-emerald-100 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 8.944 11.922.42.095.858.143 1.295.143a3 3 0 01.497-.042c.748.11 1.5.11 2.248 0 .248-.042.496-.042.497-.042z" />
                                    </svg>
                                    <span>Roles</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </nav>

            <!-- SIDEBAR FOOTER (optional) -->
            <div class="p-4 border-t border-emerald-800 text-xs text-emerald-400 flex items-center justify-between">
                <div class="flex items-center gap-x-2">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span>Online</span>
                </div>
                <span class="text-emerald-500">v1.0 • Super Admin</span>
            </div>

        </div>
    </div>
    <!-- ========== END MAIN SIDEBAR ========== -->

    <!-- Custom scrollbar styling -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgb(16 185 129);
            border-radius: 20px;
        }
    </style>

    <!-- Live Search Script (filters menu items instantly) -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('sidebar-search');
            const links = document.querySelectorAll('#hs-pro-sidebar nav a');

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();

                links.forEach(link => {
                    const text = link.textContent.toLowerCase().trim();
                    if (text.includes(query)) {
                        link.parentElement.style.display = 'block';
                    } else {
                        link.parentElement.style.display = 'none';
                    }
                });
            });
        });
    </script>
</div>
