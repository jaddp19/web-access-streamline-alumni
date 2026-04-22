<div>
<!-- ========== HEADER ========== -->
<header class="absolute top-0 inset-x-0 z-50 wrap shrink min-w-full bg-green-700 shadow-lg">
  <nav class="flex items-center justify-between px-6 lg:px-10 py-3">
    <!-- Left Section -->
    <div class="flex items-center gap-x-4">
      <!-- Sidebar Toggle -->
      <button type="button"
        class="p-2 rounded-md text-yellow-400 hover:bg-green-800 focus:outline-hidden transition"
        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
        data-hs-overlay="#hs-pro-sidebar">
        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="3" rx="2" />
          <path d="M15 3v18" />
          <path d="m10 15-3-3 3-3" />
        </svg>
      </button>

      <!-- Brand -->
      <h1 class="text-lg font-bold text-yellow-400 tracking-wide">
        Colegio de Sta. Ana de Victorias
        <span class="text-white font-medium">— SUPER ADMIN</span>
      </h1>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-x-6">
      <!-- Theme Toggle -->
      <div class="flex items-center gap-x-2 bg-green-800 rounded-full px-3 py-1 shadow-inner">
        <button type="button"
          class="size-7 flex justify-center items-center rounded-full text-white hover:bg-yellow-500 transition"
          data-hs-theme-click-value="default" title="Light Mode">
          ☀️
        </button>
        <button type="button"
          class="size-7 flex justify-center items-center rounded-full text-white hover:bg-yellow-500 transition"
          data-hs-theme-click-value="dark" title="Dark Mode">
          🌙
        </button>
        <button type="button"
          class="size-7 flex justify-center items-center rounded-full text-white hover:bg-yellow-500 transition"
          data-hs-theme-click-value="auto" title="System Default">
          ⚙️
        </button>
      </div>

      <!-- Account Dropdown -->
      <div class="hs-dropdown relative">
        <button id="hs-dnad" type="button"
          class="flex items-center gap-x-2 rounded-full px-2 py-1 hover:bg-green-800 transition">
          <img class="size-9 rounded-full border-2 border-yellow-400 shadow-md"
            src="https://images.unsplash.com/photo-1659482633369-9fe69af50bfb?auto=format&fit=facearea&facepad=3&w=80&h=80&q=80"
            alt="Avatar">
          <span class="text-sm text-yellow-400 font-semibold">{{ Auth::user()->name }}</span>
        </button>

        <div class="hs-dropdown-menu hidden absolute right-0 mt-3 w-60 bg-white rounded-lg shadow-xl border border-gray-200">
          <div class="px-4 py-3">
            <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
          </div>
          <div class="border-t px-4 py-2">
            <livewire:auth::logout />
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>
<!-- ========== END HEADER ========== -->
</div>
