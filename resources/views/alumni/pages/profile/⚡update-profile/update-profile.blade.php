<div class="max-w-2xl mx-auto p-6 bg-green-50 rounded-2xl border border-transparent shadow-2xl text-black space-y-6">

    <h2 class="text-2xl font-bold">Personal Information</h2>

    <form wire:submit.prevent="saveProfile" class="space-y-4">

        <div>
            <label class="block text-sm mb-1">Name</label>
            <input type="text" wire:model.defer="name"
                class="w-full px-4 py-2 rounded-lg border border-black text-black">
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" wire:model.defer="email"
                class="w-full px-4 py-2 rounded-lg border border-black text-black">
        </div>


        <div>
            <label class="block text-sm mb-1">Gender</label>
            <select wire:model.defer="gender"
                class="w-full px-4 py-2 rounded-lg border border-black text-black focus:outline-none">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Phone Number 1</label>
            <input type="number" wire:model.defer="phone_number_1"
                class="w-full px-4 py-2 rounded-lg border border-black text-black placeholder:text-black focus:outline-none"
                placeholder="Enter 10-digit number"
                inputmode="numeric" pattern="[0-9]*" min="0" step="1">
            @error('phone_number_1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Phone Number 2</label>
            <input type="number" wire:model.defer="phone_number_2"
                class="w-full px-4 py-2 rounded-lg border border-black text-black placeholder:text-black focus:outline-none"
                placeholder="Optional 10-digit number"
                inputmode="numeric" pattern="[0-9]*" min="0" step="1">
            @error('phone_number_2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Permanent Address</label>
            <textarea wire:model.defer="permanent_address"
                class="w-full px-4 py-2 rounded-lg border border-black text-black placeholder:text-black focus:outline-none"></textarea>
            @error('permanent_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Current Address</label>
            <textarea wire:model.defer="current_address"
                class="w-full px-4 py-2 rounded-lg border border-black text-black placeholder:text-black focus:outline-none"></textarea>
            @error('current_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
            Save Background
        </button>
        <a href="{{ route('alumni.profile') }}"
            class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
            Back
        </a>
    </form>

    @if (session('success'))
        <div class="mt-4 text-green-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif
</div>
