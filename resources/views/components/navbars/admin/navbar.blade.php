<div class="select-none">
    <!-- ========== SIDEBAR ========== -->
    <div id="hs-pro-sidebar"
        class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
               hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
               -translate-x-full lg:-translate-x-full transition-all duration-300 transform
               hidden lg:block fixed inset-y-0 z-60 start-0 lg:end-auto lg:bottom-0
               w-56 bg-[#0f2b1c] border-r border-yellow-500/10"
        role="dialog" tabindex="-1" aria-label="Sidebar">

        <nav class="pt-16 lg:pt-16 p-2.5 h-full flex flex-col overflow-y-auto text-sm [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-white/5 [&::-webkit-scrollbar-thumb]:bg-white/20">

            <!-- Mobile close button -->
            <button type="button" class="lg:hidden self-end p-1 mb-1 rounded-md text-yellow-500"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar" data-hs-overlay="#hs-pro-sidebar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
                <span class="sr-only">Close Sidebar</span>
            </button>

            <!-- Search -->
            <input id="sidebar-search" type="text" placeholder="Search"
                class="w-full py-1.5 px-2.5 mt-2 mb-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder:text-white/40 text-xs focus:outline-hidden focus:border-yellow-500/40">

            @php
                $navGroups = [
                    'Home' => [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                    ],
                    'Alumni' => [
                        ['route' => 'admin.alumni.view', 'label' => 'View All Alumni'],
                    ],
                ];
            @endphp

            @foreach ($navGroups as $group => $items)
                <div class="pt-2 mt-2 border-t border-white/10 first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2 mb-1 font-bold text-[10px] uppercase text-yellow-500/80">{{ $group }}</span>
                    <ul class="flex flex-col gap-y-0.5">
                        @foreach ($items as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" wire:current="bg-white/10 text-yellow-400"
                                    class="flex items-center gap-x-2 py-1.5 px-2 rounded-lg text-white/80 hover:bg-white/5 hover:text-white transition-colors">
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
    <!-- ========== END SIDEBAR ========== -->

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
