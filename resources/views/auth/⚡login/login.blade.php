<div class="min-h-screen bg-gradient-to-br from-green-900 to-emerald-800 flex items-center justify-center relative">
  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/40"></div>

  <div class="relative w-full max-w-md mx-auto px-6 py-10 bg-white/10 backdrop-blur-md rounded-2xl border border-white/30 shadow-lg">
    <!-- Header -->
    <div class="text-center mb-8">
      <h3 class="text-3xl font-bold text-white">Log In</h3>
      <p class="text-green-100">Access your alumni account</p>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="login" class="space-y-6">
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-green-100 mb-2">Email Address</label>
        <input wire:model.defer="email" type="email" id="email" name="email"
          class="w-full px-4 py-3 rounded-lg border border-white/30 bg-white/5 text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-green-400"
          required>
        @error('email')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-green-100 mb-2">Password</label>
        <input wire:model.defer="password" type="password" id="password" name="password"
          class="w-full px-4 py-3 rounded-lg border border-white/30 bg-white/5 text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-green-400"
          required>
        @error('password')
          <span class="text-red-400 text-sm">{{ $message }}</span>
        @enderror
      </div>

      <!-- Submit -->
      <button type="submit"
        class="w-full py-3 px-6 bg-yellow-400 text-green-900 font-semibold rounded-lg hover:bg-yellow-500 transition">
        Log In
      </button>
    </form>
  </div>
</div>
