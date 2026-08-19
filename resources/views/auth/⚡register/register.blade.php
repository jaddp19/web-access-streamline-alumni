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

      <!-- Why join list -->
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
      <div class="mb-8">
        <h3 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Create Account</h3>
        <p class="text-[#123524]/60 mt-2">Fill in your details to get started</p>
      </div>

      <!-- Form -->
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

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-semibold text-[#123524] mb-2">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
            </div>
            <input wire:model.defer="password" type="password" id="password" name="password" placeholder="••••••••"
              class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>
          </div>
          @error('password')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div>
          <label for="password_confirmation" class="block text-sm font-semibold text-[#123524] mb-2">Confirm Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
              </svg>
            </div>
            <input wire:model.defer="password_confirmation" type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••"
              class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
              required>
          </div>
          @error('password_confirmation')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Gender -->
        <div>
          <label class="block text-sm font-semibold text-[#123524] mb-2">Gender</label>
          <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-[#123524]/80 cursor-pointer">
              <input type="radio" wire:model="gender" value="male" class="text-[#123524] focus:ring-[#D4A537]">
              Male
            </label>
            <label class="flex items-center gap-2 text-sm text-[#123524]/80 cursor-pointer">
              <input type="radio" wire:model="gender" value="female" class="text-[#123524] focus:ring-[#D4A537]">
              Female
            </label>
          </div>
          @error('gender')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Phone Numbers -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="phone_number_1" class="block text-sm font-semibold text-[#123524] mb-2">Phone Number</label>
            <input wire:model.defer="phone_number_1" type="text" id="phone_number_1" placeholder="09171234567"
              class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
            @error('phone_number_1')
              <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
          </div>
          <div>
            <label for="phone_number_2" class="block text-sm font-semibold text-[#123524] mb-2">Alt. Number</label>
            <input wire:model.defer="phone_number_2" type="text" id="phone_number_2" placeholder="Optional"
              class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
            @error('phone_number_2')
              <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- Permanent Address -->
        <div>
          <label for="permanent_address" class="block text-sm font-semibold text-[#123524] mb-2">Permanent Address</label>
          <input wire:model.defer="permanent_address" type="text" id="permanent_address" placeholder="Street, Barangay, City, Province"
            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
          @error('permanent_address')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Current Address -->
        <div>
          <label for="current_address" class="block text-sm font-semibold text-[#123524] mb-2">Current Address</label>
          <input wire:model.defer="current_address" type="text" id="current_address" placeholder="Street, Barangay, City, Province"
            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
          @error('current_address')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Degree Program -->
        <div>
          <label for="degree_program_id" class="block text-sm font-semibold text-[#123524] mb-2">Degree Program</label>
          <select wire:model.defer="degree_program_id" id="degree_program_id"
            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
            <option value="">Select your program</option>
            @foreach ($degreePrograms as $program)
              <option value="{{ $program->id }}">{{ $program->program_name }}</option>
            @endforeach
          </select>
          @error('degree_program_id')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Graduation Batch -->
        <div>
          <label for="batch_id" class="block text-sm font-semibold text-[#123524] mb-2">Graduation Batch</label>
          <select wire:model.defer="batch_id" id="batch_id"
            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
            <option value="">Select your batch year</option>
            @foreach ($batches as $batch)
              <option value="{{ $batch->id }}">{{ $batch->batch_year }}</option>
            @endforeach
          </select>
          @error('batch_id')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Proof of Alumni Status -->
        <div>
          <label for="proof_document" class="block text-sm font-semibold text-[#123524] mb-2">
            Proof of Alumni Status
          </label>
          <p class="text-xs text-[#123524]/50 mb-2">Upload a photo of your school ID, diploma, or yearbook page.</p>

          <label for="proof_document"
            class="flex flex-col items-center justify-center w-full px-4 py-6 rounded-xl border-2 border-dashed border-[#123524]/20 hover:border-[#D4A537]/60 cursor-pointer transition text-center">
            <svg class="w-6 h-6 text-[#123524]/40 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
            </svg>
            <span class="text-sm text-[#123524]/60">
              @if ($proof_document)
                {{ $proof_document->getClientOriginalName() }}
              @else
                Click to upload an image
              @endif
            </span>
            <input wire:model="proof_document" type="file" id="proof_document" accept="image/*" class="hidden">
          </label>

          <div wire:loading wire:target="proof_document" class="text-xs text-[#123524]/50 mt-1">Uploading...</div>

          @error('proof_document')
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
          @enderror
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

        <!-- Divider -->
        <div class="flex items-center gap-3 pt-1">
          <span class="flex-1 h-px bg-[#123524]/10"></span>
          <span class="text-xs text-[#123524]/40 font-medium tracking-wide">OR</span>
          <span class="flex-1 h-px bg-[#123524]/10"></span>
        </div>

        <!-- Login Link -->
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
