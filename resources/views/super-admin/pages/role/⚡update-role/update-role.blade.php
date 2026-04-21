<div>
  <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="mt-12 max-w-full mx-auto">
      <!-- Card -->
      <div class="flex flex-col border border-green-700 rounded-xl p-4 sm:p-6 lg:p-8 bg-gray-700 dark:bg-black shadow-2xs">

        <!-- Back Button -->
        <div class="mb-4">
          <a href="{{ route('admin.role.view') }}" id="no-border"
            class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium
              border border-neutral-700 rounded-lg bg-gray-600 text-neutral-200
              hover:bg-green-700 hover:text-white focus:outline-hidden focus:bg-green-600">
            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 15l-6-6 6-6" />
            </svg>
            <span>Back</span>
          </a>
        </div>

        <!-- Title -->
        <h2 class="mb-8 text-xl font-semibold text-green-600">
          Update Role & Permission
        </h2>

        <!-- Form -->
        <form wire:submit.prevent='save'>
          <div class="grid gap-4 lg:gap-6">
            
            <!-- Role Name -->
            <div>
              <label for="hs-name"
                class="block mb-2 text-sm font-medium text-neutral-200">Role Name</label>
              <input wire:model.defer='name' type="text" name="hs-name" id="hs-name"
                class="py-2.5 sm:py-3 px-4 block w-full bg-gray-600 border-neutral-600 rounded-lg sm:text-sm text-neutral-200 placeholder:text-neutral-400 focus:border-green-600 focus:ring-green-600">
            </div>

            <!-- Permissions -->
            <div class="mt-5">
              <h2 class="mb-1 text-lg font-semibold text-green-600">
                Assign Permissions
              </h2>

              @error('selectedPermissions')
                <div>
                  <span class="mt-2 text-sm text-red-600">{{ $message }}</span>
                </div>
              @enderror
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-2">
              @forelse ($this->permissions as $pKey => $permission)
                <label wire:key='{{ $pKey }}' for="hs-checkbox-in-form-{{ $pKey }}" id="no-border"
                  class="flex items-center p-3 w-full bg-gray-600 border border-neutral-600 rounded-lg text-sm hover:bg-green-700 hover:text-white transition">
                  <input type="checkbox"
                    id="hs-checkbox-in-form-{{ $pKey }}" wire:model='selectedPermissions'
                    value="{{ $permission->name }}"
                    class="shrink-0 size-4 bg-transparent border-neutral-500 rounded-sm text-green-600 focus:ring-green-600">
                  <span class="text-sm ms-3 text-neutral-200">
                    {{ Str::title(str_replace('_', ' ', $permission->name)) }}
                  </span>
                </label>
              @empty
                <p class="text-neutral-400">No Permissions Found</p>
              @endforelse
            </div>
          </div>

          <!-- Update Button -->
          <div class="mt-6 grid">
            <button type="submit" id="no-border"
              class="w-50 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-green-700 text-white hover:bg-green-600 focus:outline-hidden focus:bg-green-600">
              Update
            </button>
          </div>
        </form>
      </div>
      <!-- End Card -->
    </div>
  </div>
</div>
