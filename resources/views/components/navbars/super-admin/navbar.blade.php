<div class="select-none">
    <div id="hs-pro-sidebar"
        class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
               hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
               -translate-x-full lg:-translate-x-full transition-all duration-300 transform
               hidden lg:block fixed inset-y-0 z-60 start-0 lg:end-auto lg:bottom-0
               w-56 bg-[#0f2b1c] border-r border-yellow-500/10"
        role="dialog" tabindex="-1" aria-label="Sidebar">

        <nav class="pt-16 lg:pt-16 p-2.5 h-full flex flex-col overflow-y-auto text-sm [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-white/5 [&::-webkit-scrollbar-thumb]:bg-white/20">

            <button type="button" class="lg:hidden self-end p-1 mb-1 rounded-md text-yellow-500"
                data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>

            <input id="sidebar-search" type="text" placeholder="Search"
                class="w-full py-1.5 px-2.5 mt-2 mb-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder:text-white/40 text-xs focus:outline-hidden focus:border-yellow-500/40">

            @php
                $navGroups = [
                    'Home' => [
                        ['route' => 'super-admin.dashboard', 'label' => 'Dashboard'],
                    ],
                    'Users' => [
                        ['route' => 'super-admin.user.view', 'label' => 'All Users'],   
                    ],
                    'Departments' => [
                        ['route' => 'super-admin.department.view', 'label' => 'Departments'],
                        ['route' => 'super-admin.courses.view', 'label' => 'Courses'],
                    ],
                    'Roles' => [
                        ['route' => 'view-role', 'label' => 'Roles'],
                        ['route' => 'super-admin.assign.view', 'label' => 'Assign Department'],
                        ['route' => 'super-admin.batch.view', 'label' => 'Batch Years'],
                    ],
                    'Requests' => [
                        ['route' => 'super-admin.request.view', 'label' => 'Applications'],
                        ['route' => 'super-admin.email.view', 'label' => 'Email Templates'],
                        ['route' => 'super-admin.verification-queue', 'label' => 'Verification Queue'],
                    ],
                ];
            @endphp

            @foreach ($navGroups as $group => $items)
                <div class="pt-2 mt-2 border-t border-white/10 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2 mb-1 font-bold text-[10px] uppercase text-yellow-500/80">{{ $group }}</span>
                    <ul class="flex flex-col gap-y-0.5">
                        @foreach ($items as $item)
                            @php
                                $params = $item['params'] ?? [];
                                $href = route($item['route'], $params);

                                // Active state: same route AND same roleFilter query param (or both absent).
                                $isActive = request()->routeIs($item['route'])
                                    && (request()->query('roleFilter') === ($params['roleFilter'] ?? null));
                            @endphp
                            <li>
                                <a href="{{ $href }}"
                                    @unless(isset($item['params']))
                                        wire:current="bg-white/10 text-yellow-400"
                                    @endunless
                                    @class([
                                        'flex items-center gap-x-2 py-1.5 px-2 rounded-lg transition-colors',
                                        'bg-white/10 text-yellow-400' => $isActive,
                                        'text-white/80 hover:bg-white/5 hover:text-white' => ! $isActive,
                                    ])>
                                    <span class="size-1.5 rounded-full bg-yellow-500/60 shrink-0"></span>
                                    {{ $item['label'] }}
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
                    link.closest('li').style.display = link.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        });
    </script>
</div>
