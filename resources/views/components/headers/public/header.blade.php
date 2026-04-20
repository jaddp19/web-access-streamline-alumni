<!-- ========== HEADER ========== -->
<header class="w-full bg-green-800 dark:bg-green-900 shadow-md">
  <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
    <!-- Logo -->
    <a href="/" class="flex items-center gap-x-3">
      <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
           alt="School Logo"
           class="w-12 h-12 rounded-md border-2 border-yellow-400 shadow-sm">
      <span class="text-xl font-bold text-white tracking-wide hover:text-yellow-400 transition-colors">
        Colegio de Sta. Ana de Victorias
      </span>
    </a>
    <!-- End Logo -->

    <!-- Navigation (Desktop) -->
    <div class="hidden lg:flex gap-x-10 font-medium">
      <a href="/" class="text-white hover:text-yellow-400 transition-colors">Home</a>
      <a href="/about" class="text-white hover:text-yellow-400 transition-colors">About</a>
      <a href="/contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
      <a href="/events" class="text-white hover:text-yellow-400 transition-colors">Event</a>
    </div>
    <!-- End Navigation -->

    <!-- Actions -->
    <div class="flex items-center gap-x-6">
      <button class="px-5 py-2 rounded-lg bg-yellow-400 text-green-900 font-semibold hover:bg-yellow-500 hover:shadow-md transition-all">
        Sign In
      </button>
      <!-- Mobile Menu Toggle -->
      <button class="lg:hidden px-3 py-2 rounded-lg bg-green-700 text-white hover:bg-green-600 transition" id="menu-toggle">
        ☰
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden flex-col gap-y-4 px-6 pb-4 lg:hidden">
    <a href="/" class="text-white hover:text-yellow-400 transition-colors">Home</a>
    <a href="/about" class="text-white hover:text-yellow-400 transition-colors">About</a>
    <a href="/contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
    <a href="/events" class="text-white hover:text-yellow-400 transition-colors">Event</a>
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
