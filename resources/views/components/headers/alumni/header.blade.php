<!-- ========== HEADER ========== -->
<header class="w-full bg-green-800 dark:bg-green-900 shadow-md">
  <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
    <!-- Logo -->
    <a href="/" class="flex items-center gap-x-3">
      <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
           alt="School Logo"
           class="w-12 h-12 rounded-md border-2 border-transparent shadow-sm">
      <span class="text-xl font-bold text-white tracking-wide hover:text-yellow-400 transition-colors">
        Colegio de Sta. Ana de Victorias
      </span>
    </a>
    <!-- End Logo -->

    <!-- Navigation (Desktop) -->
    <ul class="hidden md:flex flex-row items-center gap-x-6 ms-auto text-white font-semibold">
        <li><a href="{{ route('alumni.dashboard') }}" class="text-white hover:text-yellow-400 transition-colors">Home</a></li>
        <li><a href="#" class="text-white hover:text-yellow-400 transition-colors">Messages</a></li>
        <li><a href="{{ route('alumni.profile') }}" class="text-white hover:text-yellow-400 transition-colors">Profile</a></li>
        <li><a href="#" class="text-white hover:text-yellow-400 transition-colors">Settings</a></li>
        <li><livewire:auth::logout /></li>
    </ul>
    <!-- End Navigation -->

    <!-- Actions -->
    <div class="flex items-center gap-x-6">
      <!-- Mobile Menu Toggle -->
      <button class="lg:hidden px-3 py-2 rounded-lg bg-green-700 text-white hover:bg-green-600 transition" id="menu-toggle">
        ☰
      </button>
    </div>
  </nav>

<!-- Mobile Menu -->
<div id="mobile-menu" class="hidden flex-col gap-y-4 px-6 pb-4 lg:hidden">
    <a href="{{ route('alumni.dashboard') }}" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        Home
    </a>

    <a href="#" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle-more-icon lucide-message-circle-more">
            <path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/>
            <path d="M8 12h.01"/>
            <path d="M12 12h.01"/>
            <path d="M16 12h.01"/>
        </svg>
        Messages
    </a>

    <a href="{{ route('alumni.profile') }}" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-pen-icon lucide-user-round-pen">
            <path d="M2 21a8 8 0 0 1 10.821-7.487"/>
            <path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/>
            <circle cx="10" cy="8" r="5"/>
        </svg>
        Profile
    </a>

    <a href="#" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings-icon lucide-settings">
            <path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        Settings
    </a>

    <livewire:auth::logout />
</div>


</header>
<!-- ========== END HEADER ========== -->

<script>
  // Mobile menu toggle
  document.getElementById('menu-toggle').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
  });
</script>

