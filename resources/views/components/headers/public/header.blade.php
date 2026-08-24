<!-- ========== HEADER ========== -->
<header class="w-full bg-green-800 dark:bg-green-900 shadow-md select-none">

    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-3.5">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-x-3 group">
            <div class="w-11 h-11 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1 shadow-sm group-hover:ring-[#D4A537] transition shrink-0">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                     alt="CSAV Logo"
                     class="w-full h-full object-contain">
            </div>
            <div class="leading-tight">
                <span class="block text-lg font-bold text-white group-hover:text-[#D4A537] transition-colors" style="font-family: 'Fraunces', serif;">
                    Colegio de Sta. Ana de Victorias
                </span>
            </div>
        </a>
        <!-- End Logo -->

        <!-- Navigation (Desktop) -->
        <div class="hidden lg:flex items-center gap-x-9 font-medium text-sm">
            <a href="/" class="text-white/90 hover:text-[#D4A537] transition-colors">Home</a>
            <a href="/about" class="text-white/90 hover:text-[#D4A537] transition-colors">About</a>
            <a href="/contact" class="text-white/90 hover:text-[#D4A537] transition-colors">Contact</a>
            <a href="/departments" class="text-white/90 hover:text-[#D4A537] transition-colors">Departments</a>
        </div>
        <!-- End Navigation -->

        <!-- Actions (Desktop Buttons) -->
        <div class="hidden lg:flex items-center gap-x-3">
            <a href="{{ route('login') }}"
                class="px-5 py-2.5 rounded-lg bg-[#D4A537] text-[#123524] font-semibold hover:bg-[#C4962E] hover:shadow-md transition-all">
                Log In
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="lg:hidden p-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition" id="menu-toggle" aria-label="Toggle menu">
            <svg id="menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </nav>

    {{-- Gold divider under header --}}
    <div class="h-[2px] bg-gradient-to-r from-transparent via-[#D4A537]/60 to-transparent"></div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden flex-col gap-y-1 px-6 pb-5 pt-2 lg:hidden bg-[#123524]">
        <a href="/" class="text-white/90 hover:text-[#D4A537] hover:bg-white/5 transition-colors flex items-center gap-x-3 px-3 py-2.5 rounded-lg">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Home
        </a>

        <a href="/about" class="text-white/90 hover:text-[#D4A537] hover:bg-white/5 transition-colors flex items-center gap-x-3 px-3 py-2.5 rounded-lg">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            About
        </a>

        <a href="/contact" class="text-white/90 hover:text-[#D4A537] hover:bg-white/5 transition-colors flex items-center gap-x-3 px-3 py-2.5 rounded-lg">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
            </svg>
            Contact
        </a>

        <a href="/departments" class="text-white/90 hover:text-[#D4A537] hover:bg-white/5 transition-colors flex items-center gap-x-3 px-3 py-2.5 rounded-lg">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            Departments
        </a>

        <a href="{{ route('login') }}"
            class="mt-2 inline-block text-center px-4 py-2.5 rounded-lg bg-[#D4A537] text-[#123524] font-semibold hover:bg-[#C4962E] hover:shadow-md transition-all text-sm">
            Log In
        </a>
    </div>

</header>
<!-- ========== END HEADER ========== -->

<script>
    // Mobile menu toggle
    document.getElementById('menu-toggle').addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-icon-open');
        const closeIcon = document.getElementById('menu-icon-close');

        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
        openIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });
</script>
