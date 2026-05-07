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
    <div class="hidden lg:flex gap-x-10 font-medium">
      <a href="/" class="text-white hover:text-yellow-400 transition-colors">Home</a>
      <a href="/about" class="text-white hover:text-yellow-400 transition-colors">About</a>
      <a href="/contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
      <a href="/departments" class="text-white hover:text-yellow-400 transition-colors">Departments</a>
      
    </div>
    <!-- End Navigation -->

    <!-- Actions (Desktop Buttons) -->
    <div class="hidden lg:flex items-center gap-x-3">
      <a href="{{ route('login') }}" 
        class="px-5 py-2 rounded-lg bg-yellow-400 text-green-900 font-semibold hover:bg-yellow-500 hover:shadow-md transition-all">
          Log In
      </a>
    </div>

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
  <a href="/" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
    </svg>
    Home
  </a>

  <a href="/about" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.633c-2.425 0-4.843.483-7.031 1.366A2.25 2.25 0 0 0 4.816 14.25c1.051-.668 2.087-1.184 3.138-1.59C8.205 12.567 8.455 12.5 8.707 12.5h.75a2.25 2.25 0 0 0 2.243-2.096c-.078-.205-.173-.405-.288-.602C11.567 9.567 10.692 9 9.75 9zM12 18c-2.485 0-4.5-2.015-4.5-4.5S9.515 9 12 9s4.5 2.015 4.5 4.5S14.485 18 12 18z" />
    </svg>
    About
  </a>

  <a href="/contact" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
    </svg>
    Contact
  </a>

  <a href="/departments" class="text-white hover:text-yellow-400 transition-colors flex items-center gap-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
    </svg>
    Departments
  </a>

  <div>
    <a href="{{ route('login') }}" 
      class="inline-block px-3 py-1.5 rounded-md bg-yellow-400 text-green-900 font-semibold 
              hover:bg-yellow-500 hover:shadow-md transition-all text-sm w-auto">
        Log In
    </a>
  </div>
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
