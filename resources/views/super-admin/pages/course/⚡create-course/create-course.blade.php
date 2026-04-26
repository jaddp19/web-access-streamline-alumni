<div>
  <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto rounded-xl">
    <div class="mt-12 max-w-full mx-auto">
      <!-- Card -->
      <div class="flex flex-col border border-transparent rounded-xl p-4 sm:p-6 lg:p-8 bg-green-50 shadow-2xl">

        <!-- Back Button -->
        <div class="mb-4">
          <a href="{{ route('super-admin.courses.view') }}"
            class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium
              border border-transparent rounded-lg bg-green-700 text-white
              hover:bg-green-600 focus:outline-hidden">
            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 15l-6-6 6-6" />
            </svg>
            <span>Back</span>
          </a>
        </div>

        <!-- Title -->
        <h2 class="mb-8 text-xl font-semibold text-black">
          Assign Program Head
        </h2>

        <!-- Form -->
        <form wire:submit.prevent='create'>
          <div class="grid gap-4 lg:gap-6">
            
            <!-- User -->
            <div>
              <label for="program_name" class="block mb-2 text-sm font-medium text-black">Course Name</label>
              <input type="text" wire:model.defer="program_name"
                class="w-full px-4 py-2 rounded-lg border border-black text-black placeholder:text-black focus:outline-none">
                @error('program_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Department -->
            <div>
              <label for="department_id" class="block mb-2 text-sm font-medium text-black">Select Department</label>
              <select wire:model.defer='department_id' id="department_id"
                  class="py-2.5 sm:py-3 px-4 block w-full rounded-lg border border-black 
                      bg-green-50 text-black focus:outline-none">
                  <option value=""> Choose Department </option>
                  @foreach($this->departments as $department)
                  <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                  @endforeach
              </select>
              @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Create Button -->
            <div class="mt-6 grid">
              <button type="submit"
                class="w-50 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-700 text-white hover:bg-green-600 focus:outline-hidden">
                Assign
              </button>
            </div>
          </div>
        </form>
      </div>
      <!-- End Card -->
    </div>
  </div>
</div>
