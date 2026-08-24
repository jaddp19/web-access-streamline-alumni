<div class="min-h-screen flex bg-[#F7F5EF]">

  <!-- ========== LEFT PANEL — BRANDING ========== -->
  <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#123524]">

    <div class="absolute inset-0">
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#D4A537]/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] bg-[#D4A537]/15 rounded-full blur-3xl"></div>
      <div class="absolute inset-0 opacity-[0.05]"
           style="background-image: radial-gradient(#D4A537 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRC0rZblAbfgOgTt6ujr71K-2jf9gY65zHSUOVdPIFeKy1BENm-131U9vw&s=10"
         alt="Campus"
         class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay">

    <div class="relative z-10 flex flex-col justify-between w-full px-14 py-16">

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

      <div class="space-y-8 max-w-md">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 rounded-full backdrop-blur-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
          <span class="text-white/90 text-xs font-bold tracking-[0.2em]">JOIN THE ALUMNI PORTAL</span>
        </div>

        <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight" style="font-family: 'Fraunces', serif;">
          Your journey continues,
          <span class="text-[#D4A537]">together.</span>
        </h1>

        <p class="text-white/70 leading-relaxed">
          Create your account to reconnect with classmates, get event updates, and stay part of the
          community you helped build.
        </p>
      </div>

      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-[#D4A537]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
          </div>
          <span class="text-white/80 text-sm">Connect with 12,000+ fellow graduates</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-[#D4A537]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
          </div>
          <span class="text-white/80 text-sm">Get first access to reunions and events</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-[#D4A537]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
          </div>
          <span class="text-white/80 text-sm">Explore scholarships and giving-back opportunities</span>
        </div>
      </div>
    </div>
  </div>
  <!-- ========== END LEFT PANEL ========== -->

  <!-- ========== RIGHT PANEL — FORM ========== -->
  <div class="w-full lg:w-1/2 flex items-center justify-center px-6 sm:px-12 py-16 relative">

    <div class="absolute inset-0 -z-10 lg:hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>
    </div>

    <div class="w-full max-w-sm">

      <div class="flex lg:hidden justify-center mb-8">
        <div class="w-14 h-14 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md">
          <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
               alt="CSAV Logo"
               class="w-full h-full object-contain">
        </div>
      </div>

      <div class="mb-8">
        <h3 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Create Account</h3>
        <p class="text-[#123524]/60 mt-2">Fill in your details to get started</p>
      </div>

      <form wire:submit.prevent="register" class="space-y-5">

        <!-- Full Name -->
        <div>
          <label for="name" class="block text-sm font-semibold text-[#123524] mb-2">Full Name</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
            </div>
            <input wire:model.defer="name" type="text" id="name" name="name" placeholder="Juan Dela Cruz"
              class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>
          </div>
          @error('name')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

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
          <p class="text-xs text-[#123524]/40 mt-1.5">We'll send a verification link to this address.</p>
          @error('email')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- School ID Number -->
        <div>
          <label for="school_id" class="block text-sm font-semibold text-[#123524] mb-2">School ID Number</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
              </svg>
            </div>
            <input wire:model.defer="school_id" type="text" id="school_id" name="school_id" placeholder="e.g. 2021-00123"
              class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>
          </div>
          <p class="text-xs text-[#123524]/40 mt-1.5">Found on your school ID card or diploma.</p>
          @error('school_id')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

       {{-- Password --}}
<div>
    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
        Password <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <input type="password" id="password" wire:model="password"
            placeholder="••••••••"
            class="py-2 px-4 pe-11 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
        <button type="button"
            data-hs-toggle-password='{"target": "#password"}'
            class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 rounded-e-md focus:outline-none focus:text-blue-600 dark:text-neutral-600 dark:focus:text-blue-500">
            {{-- Eye Open --}}
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path class="hs-password-active:hidden" d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                <path class="hs-password-active:hidden" d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                <path class="hs-password-active:hidden" d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                <line class="hs-password-active:hidden" x1="2" x2="22" y1="2" y2="22"/>
                <path class="hidden hs-password-active:block" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle class="hidden hs-password-active:block" cx="12" cy="12" r="3"/>
            </svg>
        </button>
    </div>
    @error('password')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Confirm Password --}}
<div>
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">
        Confirm Password <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <input type="password" id="password_confirmation" wire:model="password_confirmation"
            placeholder="••••••••"
            class="py-2 px-4 pe-11 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
        <button type="button"
            data-hs-toggle-password='{"target": "#password_confirmation"}'
            class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 rounded-e-md focus:outline-none focus:text-blue-600 dark:text-neutral-600 dark:focus:text-blue-500">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path class="hs-password-active:hidden" d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                <path class="hs-password-active:hidden" d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                <path class="hs-password-active:hidden" d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                <line class="hs-password-active:hidden" x1="2" x2="22" y1="2" y2="22"/>
                <path class="hidden hs-password-active:block" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle class="hidden hs-password-active:block" cx="12" cy="12" r="3"/>
            </svg>
        </button>
    </div>
</div>

        <!-- Terms -->
        <label class="flex items-start gap-2.5 text-sm text-[#123524]/70 cursor-pointer">
          <input type="checkbox" required class="mt-0.5 rounded border-[#123524]/30 text-[#123524] focus:ring-[#D4A537]">
          <span>I agree to the <a href="#" class="font-medium text-[#123524] hover:text-[#D4A537] transition-colors">Terms of Service</a> and <a href="#" class="font-medium text-[#123524] hover:text-[#D4A537] transition-colors">Privacy Policy</a></span>
        </label>

        <!-- Submit -->
        <button type="submit"
          class="group w-full py-3.5 px-6 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
          Create Account
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </button>

        <div class="flex items-center gap-3 pt-1">
          <span class="flex-1 h-px bg-[#123524]/10"></span>
          <span class="text-xs text-[#123524]/40 font-medium tracking-wide">OR</span>
          <span class="flex-1 h-px bg-[#123524]/10"></span>
        </div>

        <p class="text-center text-sm text-[#123524]/70">
          Already have an account?
          <a href="{{ route('login') }}" class="font-semibold text-[#123524] hover:text-[#D4A537] transition-colors">
            Log in here
          </a>
        </p>
      </form>
    </div>
  </div>
  <!-- ========== END RIGHT PANEL ========== -->

</div>
