<div class="max-w-2xl mx-auto p-6 bg-white/10 backdrop-blur-md rounded-2xl border border-white/30 shadow-lg text-white space-y-6">

    <h2 class="text-2xl font-bold">Personal Background</h2>

    <form wire:submit.prevent="saveProfile" class="space-y-4">
        <div>
            <label class="block text-sm mb-1">Batch</label>
            <select wire:model.defer="batch_id"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
                <option value="">-- Select Batch --</option>
                @foreach(\App\Models\Batch::all() as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->batch_year }}</option>
                @endforeach
            </select>
            @error('batch_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Gender</label>
            <select wire:model.defer="gender"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            @error('gender') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Phone Number 1</label>
            <input type="text" wire:model.defer="phone_number_1"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
            @error('phone_number_1') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Phone Number 2</label>
            <input type="text" wire:model.defer="phone_number_2"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white">
        </div>

        <div>
            <label class="block text-sm mb-1">Permanent Address</label>
            <textarea wire:model.defer="permanent_address"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white"></textarea>
            @error('permanent_address') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Current Address</label>
            <textarea wire:model.defer="current_address"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/30 text-white"></textarea>
            @error('current_address') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
            Save Background
        </button>
        <a href="{{ route('alumni.profile') }}"
            class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
            Back
        </a>
    </form>

    @if (session('success'))
        <div class="mt-4 text-green-400 font-semibold">
            {{ session('success') }}
        </div>
    @endif
</div>
