<div class="select-none">
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

            <button type="button"
                class="lg:hidden self-end p-1 mb-1 rounded-md text-gray-600 hover:text-black
                       dark:text-white/60 dark:hover:text-white"
                data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>

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
                    'Overview' => [
                        ['route' => 'super-admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ],
                    'User Management' => [
                        ['route' => 'super-admin.user.view',      'label' => 'All Users',          'icon' => 'users'],
                        ['route' => 'view-role',                  'label' => 'Roles',              'icon' => 'shield'],
                        ['route' => 'super-admin.assign.view',    'label' => 'Assign Department',  'icon' => 'user-plus'],
                        ['route' => 'super-admin.audit-logs',     'label' => 'Audit Logs',         'icon' => 'clipboard'],
                        ['route' => 'super-admin.duplicates-view','label' => 'View Duplications',  'icon' => 'clipboard'],
                    ],
                    'Academics' => [
                        ['route' => 'super-admin.department.view', 'label' => 'Departments', 'icon' => 'building'],
                        ['route' => 'super-admin.courses.view',    'label' => 'Courses',     'icon' => 'cap'],
                        ['route' => 'super-admin.batch.view',      'label' => 'Batch Years', 'icon' => 'calendar'],
                        ['route' => 'super-admin.post.view',       'label' => 'Posts',       'icon' => 'document'],
                        ['route' => 'super-admin.events.view',     'label' => 'Events',      'icon' => 'calendar-star'],
                    ],
                    'Workplace' => [
                        ['route' => 'super-admin.company.view', 'label' => 'Company', 'icon' => 'briefcase'],
                    ],
                    'Reports' => [
                        ['route' => 'super-admin.reports', 'label' => 'View Reports', 'icon' => 'chart-bar'],
                    ],
                    'Requests' => [
                        ['route' => 'super-admin.email.view',         'label' => 'Email Templates',    'icon' => 'mail'],
                        ['route' => 'super-admin.verification-queue', 'label' => 'Verification Queue', 'icon' => 'badge-check'],
                    ],
                ];

                // Heroicon outline paths, keyed by name.
                $icons = [
                    'home'          => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
                    'users'         => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                    'shield'        => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                    'user-plus'     => 'M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z',
                    'clipboard'     => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
                    'building'      => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
                    'cap'           => 'M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
                    'calendar'      => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
                    'document'      => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                    'calendar-star' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z',
                    'briefcase'     => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z',
                    'mail'          => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75',
                    'badge-check'   => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
                    'chart-bar'     => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
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
                                $params = $item['params'] ?? [];
                                $href = route($item['route'], $params);
                                $iconPath = $icons[$item['icon']] ?? $icons['home'];

                                $isActive =
                                    request()->routeIs($item['route']) &&
                                    request()->query('roleFilter') === ($params['roleFilter'] ?? null);
                            @endphp
                            <li>
                                <a href="{{ $href }}"
                                    @unless (isset($item['params']))
                                        wire:current="bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] ring-1 ring-[#123524]/10 dark:ring-[#D4A537]/20"
                                    @endunless
                                    @class([
                                        'group flex items-center gap-x-2.5 py-2 px-2.5 rounded-lg transition-all duration-150',
                                        'bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] font-semibold ring-1 ring-[#123524]/10 dark:ring-[#D4A537]/20' => $isActive,
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