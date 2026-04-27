<div class="min-h-screen flex items-center justify-center relative py-20">

  <div class="relative w-full max-w-md mx-auto px-6 py-10 shadow-2xl bg-green-90 backdrop-blur-md rounded-2xl border border-white/30">
    <!-- Header -->
    <div class="text-center mb-8">
      <h3 class="text-3xl font-bold text-green-700">Register</h3>
      <p class="text-black">Create your alumni account</p>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="register" class="space-y-6">
      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-black mb-2">Full Name</label>
        <input wire:model.defer="name" type="text" id="name" name="name"
          class="w-full px-4 py-3 rounded-lg border border-black text-black placeholder-black focus:outline-none"
          required>
        @error('name')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-black mb-2">Email Address (Real Email Address)</label>
        <input wire:model.defer="email" type="email" id="email" name="email"
          class="w-full px-4 py-3 rounded-lg border border-black text-black placeholder-black focus:outline-none"
          required>
        @error('email')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-black mb-2">Password</label>
        <input wire:model.defer="password" type="password" id="password" name="password"
          class="w-full px-4 py-3 rounded-lg border border-black text-black placeholder-black focus:outline-none"
          required>
        @error('password')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-black mb-2">Confirm Password</label>
        <input wire:model.defer="password_confirmation" type="password" id="password_confirmation" name="password_confirmation"
          class="w-full px-4 py-3 rounded-lg border border-black text-black placeholder-black focus:outline-none"
          required>
        @error('password_confirmation')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Submit -->
      <button type="submit"
        class="w-full py-3 px-6 bg-green-700 text-white font-semibold rounded-lg hover:bg-green-600 transition">
        Register
      </button>

      <!-- Login Link -->
      <p class="mt-4 text-center text-sm text-black">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-green-700 hover:text-green-600 transition">
          Log in here
        </a>
      </p>
    </form>
  </div>
</div>
