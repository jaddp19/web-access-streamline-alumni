<footer class="mt-auto w-full bg-[#123524] dark:bg-[#0B1F16] border-t border-[#D4A537]/20 select-none">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start text-center md:text-left">

      {{-- Brand Section --}}
      <div class="space-y-4">
        <a href="/" aria-label="Colegio de Sta. Ana de Victorias"
           class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-3 group">
          <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
               alt="School Logo"
               class="w-12 h-12 rounded-full ring-2 ring-[#D4A537] object-cover shrink-0">
          <span class="font-bold text-xl text-white group-hover:text-[#D4A537] transition-colors text-center md:text-left leading-tight"
                style="font-family: 'Fraunces', serif;">
            Colegio de Sta. Ana de Victorias
          </span>
        </a>
        <p class="text-sm text-white/60 leading-relaxed max-w-xs mx-auto md:mx-0">
          Empowering alumni to connect, collaborate, and continue their legacy of excellence within our growing community.
        </p>
      </div>

      {{-- Legal / Support Section --}}
      <div class="space-y-4 text-center md:text-right">
        <h4 class="text-white font-bold uppercase tracking-wider text-xs">Support & Legal</h4>
        <div class="flex flex-col items-center md:items-end gap-y-2 text-sm text-white/70">
          <a href="{{ route('privacy-policy') }}" target="_blank" 
            class="hover:text-[#D4A537] transition-colors">Privacy Policy</a>
          <a href="{{ route('terms-and-conditions') }} "target="_blank"
            class="hover:text-[#D4A537] transition-colors">
              Terms and Conditions
          </a>
          <a href="#" class="hover:text-[#D4A537] transition-colors">Contact Administration</a>
        </div>
        <div class="pt-4 border-t border-white/10">
          <p class="text-xs text-white/40">
            &copy; {{ date('Y') }} Colegio de Sta. Ana de Victorias. <br class="hidden sm:block"> All rights reserved.
          </p>
        </div>
      </div>

    </div>
  </div>
</footer>