<div class="min-h-screen flex bg-[#F7F5EF]">

  <!-- ========== LEFT PANEL — BRANDING ========== -->
  <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#123524]">

    <!-- decorative layers -->
    <div class="absolute inset-0">
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#D4A537]/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] bg-[#D4A537]/15 rounded-full blur-3xl"></div>
      <div class="absolute inset-0 opacity-[0.05]"
           style="background-image: radial-gradient(#D4A537 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    <!-- background image with overlay -->
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRC0rZblAbfgOgTt6ujr71K-2jf9gY65zHSUOVdPIFeKy1BENm-131U9vw&s=10"
         alt="Campus"
         class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay">

    <div class="relative z-10 flex flex-col justify-between w-full px-14 py-16">

      <!-- Logo + Name -->
      <a href="/" class="flex items-center gap-x-3">
        <div class="w-12 h-12 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md shrink-0">
          <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
               alt="CSAV Logo"
               class="w-full h-full object-contain">
        </div>
        <span class="text-white font-bold text-lg" style="font-family: 'Fraunces', serif;">
          Colegio de Sta. Ana de Victorias
        </span>
      </a>

      <!-- Middle content -->
      <div class="space-y-8 max-w-md">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 rounded-full backdrop-blur-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
          <span class="text-white/90 text-xs font-bold tracking-[0.2em]">ALUMNI PORTAL</span>
        </div>

        <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight" style="font-family: 'Fraunces', serif;">
          Welcome back to your
          <span class="text-[#D4A537]">alumni family.</span>
        </h1>

        <p class="text-white/70 leading-relaxed">
          Reconnect with fellow graduates, stay updated on events, and continue building on the legacy
          of faith, excellence, and service.
        </p>
      </div>

      <!-- Bottom stats -->
      <div class="flex items-center gap-8">
        <div>
          <div class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">12,000+</div>
          <div class="text-xs text-white/50 font-medium tracking-wide mt-1">ALUMNI MEMBERS</div>
        </div>
        <div class="w-px h-10 bg-white/15"></div>
        <div>
          <div class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">128</div>
          <div class="text-xs text-white/50 font-medium tracking-wide mt-1">GLOBAL CHAPTERS</div>
        </div>
        <div class="w-px h-10 bg-white/15"></div>
        <div>
          <div class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">30+</div>
          <div class="text-xs text-white/50 font-medium tracking-wide mt-1">YEARS STRONG</div>
        </div>
      </div>
    </div>
  </div>
  <!-- ========== END LEFT PANEL ========== -->

  <!-- ========== RIGHT PANEL — FORM ========== -->
  <div class="w-full lg:w-1/2 flex items-center justify-center px-6 sm:px-12 py-16 relative">

    <!-- subtle bg for mobile (no left panel visible) -->
    <div class="absolute inset-0 -z-10 lg:hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>
    </div>

    <div class="w-full max-w-sm">

      <!-- Mobile logo (shown only when left panel is hidden) -->
      <div class="flex lg:hidden justify-center mb-8">
        <div class="w-14 h-14 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md">
          <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
               alt="CSAV Logo"
               class="w-full h-full object-contain">
        </div>
      </div>

      <!-- Header -->
      <div class="mb-9">
        <h3 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Sign In</h3>
        <p class="text-[#123524]/60 mt-2">Enter your credentials to access your account</p>
      </div>

      <!-- Form -->
      <form wire:submit.prevent="login" class="space-y-5">

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-semibold text-[#123524] mb-2">Email Address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 012.25 17.25V6.75zm0 0l9.75 6.75 9.75-6.75" />
              </svg>
            </div>
            <input wire:model.defer="email" type="email" id="email" name="email" placeholder="you@example.com"
              class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>
          </div>
          @error('email')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
          <div class="flex items-center justify-between mb-2">
            <label for="password" class="block text-sm font-semibold text-[#123524]">Password</label>
            <a href="#" class="text-xs font-medium text-[#123524]/60 hover:text-[#D4A537] transition-colors">Forgot password?</a>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
            </div>
            <input wire:model.defer="password" :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="••••••••"
              class="w-full pl-12 pr-11 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>

            <button type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#123524]/40 hover:text-[#123524] transition"
                    tabindex="-1">
              <!-- Eye (show) icon -->
              <svg x-show="!showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <!-- Eye-slash (hide) icon -->
              <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            </button>
          </div>
          @error('password')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Submit -->
        <button type="submit"
          class="group w-full py-3.5 px-6 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
          Sign In
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </button>

        <!-- Divider -->
        <div class="flex items-center gap-3 pt-1">
          <span class="flex-1 h-px bg-[#123524]/10"></span>
          <span class="text-xs text-[#123524]/40 font-medium tracking-wide">OR</span>
          <span class="flex-1 h-px bg-[#123524]/10"></span>
        </div>

        <!-- Register Link -->
        <p class="text-center text-sm text-[#123524]/70">
          Don't have an account?
          <a href="{{ route('register') }}" class="font-semibold text-[#123524] hover:text-[#D4A537] transition-colors">
            Register here
          </a>
        </p>
      </form>
    </div>
  </div>
  <!-- ========== END RIGHT PANEL ========== -->

</div>
