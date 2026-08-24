<footer class="mt-auto w-full bg-gradient-to-r from-[#123524] to-[#1C6B45] dark:from-[#0B1F16] dark:to-[#123524] border-t border-white/10 select-none">
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex flex-col items-center gap-y-4 text-center">

      <a href="/" aria-label="Colegio de Sta. Ana de Victorias" class="flex items-center gap-x-3 group">
        <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
             alt="School Logo"
             class="w-10 h-10 rounded-full ring-2 ring-[#D4A537]/50 object-cover">
        <span class="font-bold text-lg text-white group-hover:text-[#D4A537] transition-colors" style="font-family: 'Fraunces', serif;">
          Colegio de Sta. Ana de Victorias
        </span>
      </a>

      <div class="w-16 h-px bg-[#D4A537]/40"></div>

      <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-white/70">
        <a href="{{ route('alumni.dashboard') }}" class="hover:text-[#D4A537] transition-colors">Home</a>
        <a href="{{ route('alumni.profile') }}" class="hover:text-[#D4A537] transition-colors">Profile</a>
        <a href="{{ route('alumni.message') }}" class="hover:text-[#D4A537] transition-colors">Messages</a>
        <a href="{{ route('alumni.settings') }}" class="hover:text-[#D4A537] transition-colors">Settings</a>
      </div>

      <p class="text-xs sm:text-sm text-white/50">
        &copy; {{ date('Y') }} Colegio de Sta. Ana de Victorias. All rights reserved.
      </p>

    </div>
  </div>
</footer>
