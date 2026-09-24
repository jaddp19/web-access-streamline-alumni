<div class="select-none">
    <!-- ========== SIDEBAR ========== -->
    <div id="hs-pro-sidebar"
        class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
               hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
               -translate-x-full lg:-translate-x-full transition-all duration-300 transform
               hidden lg:block fixed inset-y-0 z-60 start-0 lg:end-auto lg:bottom-0
               w-56 bg-white dark:bg-[#18191A] border-r border-gray-100 dark:border-white/5 shadow-sm"
        role="dialog" tabindex="-1" aria-label="Sidebar">

        <nav
            class="pt-16 lg:pt-16 p-2.5 h-full flex flex-col overflow-y-auto text-sm
                   [&::-webkit-scrollbar]:w-1.5
                   [&::-webkit-scrollbar-thumb]:rounded-full
                   [&::-webkit-scrollbar-track]:bg-gray-100
                   [&::-webkit-scrollbar-thumb]:bg-gray-300
                   dark:[&::-webkit-scrollbar-track]:bg-white/5
                   dark:[&::-webkit-scrollbar-thumb]:bg-white/20">

            <!-- Mobile close button -->
            <button type="button"
                class="lg:hidden self-end p-1 mb-1 rounded-md text-gray-600 hover:text-black
                       dark:text-white/60 dark:hover:text-white"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
                data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
                <span class="sr-only">Close Sidebar</span>
            </button>

            <!-- Search -->
            <input id="sidebar-search" type="text" placeholder="Search"
                class="w-full py-1.5 px-2.5 mt-2 mb-2 rounded-lg
                       bg-gray-50 dark:bg-white/5
                       border border-gray-200 dark:border-white/10
                       text-gray-900 dark:text-white
                       placeholder:text-gray-500 dark:placeholder:text-white/50
                       text-xs
                       focus:outline-hidden
                       focus:border-[#123524] dark:focus:border-[#D4A537]
                       focus:ring-1 focus:ring-[#123524]/20 dark:focus:ring-[#D4A537]/20
                       transition">

            @php
                $navGroups = [
                    'Home' => [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ],
                    'Alumni' => [
                        ['route' => 'admin.alumni.view', 'label' => 'View All Alumni', 'icon' => 'users'],
                        ['route' => 'admin.events.view', 'label' => 'Events',          'icon' => 'calendar-star'],
                        ['route' => 'admin.post.view',   'label' => 'Post',            'icon' => 'document'],
                    ],
                    'Reports' => [
                        ['route' => 'admin.reports', 'label' => 'View Reports', 'icon' => 'chart-bar'],
                    ],
                    'Requests' => [
                        ['route' => 'admin.verification-queue', 'label' => 'Verification Queue', 'icon' => 'badge-check'],
                    ],
                ];

                // Heroicon outline paths, keyed by name.
                $icons = [
                    'home'          => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
                    'users'         => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                    'calendar-star' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z',
                    'document'      => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                    'chart-bar'     => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
                    'badge-check'   => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
                ];
            @endphp

            @foreach ($navGroups as $group => $items)
                <div class="pt-2 mt-2 border-t border-gray-100 dark:border-white/5 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2 mb-1 font-bold text-[10px] uppercase tracking-wider
                                 text-gray-600 dark:text-white/60">
                        {{ $group }}
                    </span>
                    <ul class="flex flex-col gap-y-0.5">
                        @foreach ($items as $item)
                            @php
                                $iconPath = $icons[$item['icon']] ?? $icons['home'];
                                $isActive = request()->routeIs($item['route']);
                            @endphp
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    wire:current="bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] ring-1 ring-[#123524]/10 dark:ring-[#D4A537]/20"
                                    @class([
                                        'group flex items-center gap-x-2.5 py-2 px-2.5 rounded-lg transition-all duration-150',

                                        // Active
                                        'bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] font-semibold ring-1 ring-[#123524]/10 dark:ring-[#D4A537]/20' => $isActive,

                                        // Idle
                                        'text-gray-800 dark:text-white/90 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-black dark:hover:text-white' => !$isActive,
                                    ])>

                                    <svg class="size-[18px] shrink-0 transition-colors
                                                {{ $isActive
                                                    ? 'text-[#123524] dark:text-[#D4A537]'
                                                    : 'text-gray-600 dark:text-white/60 group-hover:text-gray-900 dark:group-hover:text-white/90' }}"
                                        fill="none" stroke="currentColor" stroke-width="1.8"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                    </svg>

                                    <span class="truncate text-[13px]">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

        </nav>
    </div>
    <!-- ========== END SIDEBAR ========== -->

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('sidebar-search');
            const links = document.querySelectorAll('#hs-pro-sidebar nav a');

            input?.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase().trim();
                links.forEach(link => {
                    link.closest('li').style.display = link.textContent.toLowerCase().includes(q) ?
                        '' : 'none';
                });
            });
        });
    </script>
</div>