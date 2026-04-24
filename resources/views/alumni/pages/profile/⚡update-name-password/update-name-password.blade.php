<div class="max-w-md mx-auto p-6 bg-white/10 backdrop-blur-md rounded-2xl border border-white/30 shadow-lg text-white space-y-6">

    <h2 class="text-2xl font-bold">Update Profile</h2>

    <form wire:submit.prevent="update" class="space-y-4">
        <div>
            <label class="block text-sm mb-1">Name</label>
            <input type="text" wire:model.defer="name"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
            @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">New Password</label>
            <input type="password" wire:model.defer="password"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
            @error('password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Confirm Password</label>
            <input type="password" wire:model.defer="password_confirmation"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
        </div>

        <button type="submit"
            class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
            Save Changes
        </button>
    </form>

    @if (session('success'))
        <div class="mt-4 text-green-400 font-semibold">
            {{ session('success') }}
        </div>
    @endif
</div>
