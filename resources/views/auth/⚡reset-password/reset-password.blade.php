<div class="min-h-screen flex items-center justify-center bg-[#F7F5EF] px-4 py-12">
    <div class="w-full max-w-md">

        <div class="flex justify-center mb-6">
            <div class="w-14 h-14 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                     alt="CSAV Logo" class="w-full h-full object-contain">
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                Set a new password
            </h1>
            <p class="text-[#123524]/60 mt-2 text-sm">
                Choose a strong password to secure your account.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-[#123524]/5 p-6 sm:p-8">

            @if (session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold rounded-xl p-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="resetPassword" class="space-y-5">

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#123524] mb-2 uppercase tracking-wide">
                        Email Address
                    </label>
                    <input type="email" id="email" wire:model="email"
                        class="w-full px-4 py-3 rounded-xl border text-sm text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                        @error('email') border-red-400 @else border-[#123524]/15 @enderror">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#123524] mb-2 uppercase tracking-wide">
                        New Password
                    </label>
                    <input type="password" id="password" wire:model="password"
                        placeholder="At least 8 characters"
                        class="w-full px-4 py-3 rounded-xl border text-sm text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                        @error('password') border-red-400 @else border-[#123524]/15 @enderror">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#123524] mb-2 uppercase tracking-wide">
                        Confirm New Password
                    </label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation"
                        placeholder="Re-enter your password"
                        class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-sm text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                </div>

                <button type="submit"
                    wire:loading.attr="disabled" wire:target="resetPassword"
                    class="w-full py-3.5 px-6 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                    <span wire:loading wire:target="resetPassword">Resetting…</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">
                    ← Back to sign in
                </a>
            </div>
        </div>
    </div>
</div>