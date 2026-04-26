<div class="max-w-2xl mx-auto p-6 bg-green-50 rounded-2xl shadow-2xl border border-transparent text-black space-y-6">

    <h2 class="text-2xl font-bold">Update Educational Background</h2>

    <form wire:submit.prevent="update" class="space-y-4">
        <!-- Batch -->
        <div>
            <label class="block text-sm mb-1">Batch</label>
            <select wire:model.defer="batch_id"
                class="w-full px-4 py-2 rounded-lg border border-black text-black focus:outline-none">
                <option value="">Select Batch</option>
                @foreach($this->batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->batch_year }}</option>
                @endforeach
            </select>
            @error('batch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Degree Program -->
        <div>
            <label class="block text-sm mb-1">Degree Program</label>
            <select wire:model.defer="degree_program_id"
                class="w-full px-4 py-2 rounded-lg border border-black text-black focus:outline-none">
                <option value="">Select Degree Program</option>
                @foreach($this->degreePrograms as $program)
                    <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>
            @error('degree_program_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Save Button -->
        <button type="submit"
            class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
            Update Background
        </button>
        <a href="{{ route('alumni.profile') }}"
            class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
            Back
        </a>
    </form>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mt-4 text-green-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif
</div>
