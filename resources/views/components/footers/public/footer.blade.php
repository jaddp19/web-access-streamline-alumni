<div class="select-none">
    <!-- ========== FOOTER ========== -->
    <footer class="w-full bg-[#123524] mt-auto relative">

        {{-- Gold divider above footer, matching header --}}
        <div class="h-[2px] bg-gradient-to-r from-transparent via-[#D4A537]/60 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6 py-10">
            <div class="flex flex-col items-center text-center gap-y-4">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-x-3 group">
                    <div class="w-11 h-11 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1 shadow-sm group-hover:ring-[#D4A537] transition shrink-0">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                             alt="CSAV Logo"
                             class="w-full h-full object-contain">
                    </div>
                    <span class="text-lg font-bold text-white group-hover:text-[#D4A537] transition-colors" style="font-family: 'Fraunces', serif;">
                        Colegio de Sta. Ana de Victorias
                    </span>
                </a>
                <!-- End Logo -->

                <!-- Social Links -->
                <div class="flex items-center gap-x-5 mt-1">
                    <a href="https://web.facebook.com/profile.php?id=100057569014854" target="_blank" aria-label="Facebook" class="text-white/90 hover:text-[#D4A537] transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.877h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94z"/>
                        </svg>
                    </a>
                    <a href="#" target="_blank" aria-label="Email" class="text-white/90 hover:text-[#D4A537] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 012.25 17.25V6.75zm0 0l9.75 6.75 9.75-6.75" />
                        </svg>
                    </a>
                </div>
                <!-- End Social Links -->

                {{-- Legal Links --}}
                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 mt-1 text-xs">
                    <a href="{{ route('privacy-policy') }}" target="_blank"
                       class="text-white/60 hover:text-[#D4A537] transition-colors">
                        Privacy Policy
                    </a>
                    <span class="text-white/20">·</span>
                    <a href="{{ route('terms-and-conditions') }}" target="_blank"
                    class="text-white/60 hover:text-[#D4A537] transition-colors">
                        Terms and Conditions
                    </a>
                </div>
                {{-- End Legal Links --}}

                <!-- Copyright -->
                <p class="text-sm text-white/70 mt-1">
                    © 2026 Colegio de Sta. Ana de Victorias — All rights reserved.
                </p>

            </div>
        </div>
    </footer>
    <!-- ========== END FOOTER ========== -->
</div>