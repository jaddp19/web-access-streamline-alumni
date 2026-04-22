<div>
    <div
        class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto rounded-xl">
        <div class="mt-12 max-w-full mx-auto">
            <!-- Card -->
            <div class="flex flex-col border border-white/30 rounded-xl p-4 sm:p-6 lg:p-8 bg-white/10 shadow-2xs">

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('view-role') }}"
                        class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium
            border border-transparent rounded-lg bg-yellow-500 text-white
            hover:bg-yellow-600 focus:outline-hidden">
                        <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 15l-6-6 6-6" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>

                <!-- Title -->
                <h2 class="mb-8 text-xl font-semibold text-white">
                    Create Role & Permission
                </h2>

                <!-- Form -->
                <form wire:submit.prevent='save'>
                    <div class="grid gap-4 lg:gap-6">

                        <!-- Role Name -->
                        <div>
                            <label for="hs-name" class="block mb-2 text-sm font-medium text-white">Role Name</label>
                            <input wire:model.defer='name' type="text" name="hs-name" id="hs-name"
                                class="py-2.5 sm:py-3 px-4 block w-full bg-white/5 border border-white/30 rounded-lg sm:text-sm text-white placeholder:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <!-- Permissions -->
                        <div class="mt-5">
                            <h2 class="mb-1 text-lg font-semibold text-white">
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
                                <label wire:key='{{ $pKey }}' for="hs-checkbox-in-form-{{ $pKey }}"
                                    class="flex items-center p-3 w-full bg-white/10 border border-transparent rounded-lg text-sm hover:bg-white/5 hover:text-white transition">
                                    <input type="checkbox" id="hs-checkbox-in-form-{{ $pKey }}"
                                        wire:model='selectedPermissions' value="{{ $permission->name }}"
                                        class="shrink-0 size-4 rounded-sm text-yellow-500 focus:ring-yellow-400">
                                    <span class="text-sm ms-3 text-white">
                                        {{ Str::title(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                            @empty
                                <p class="text-white">No Permissions Found</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 grid">
                        <button type="submit"
                            class="w-50 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-white/30 bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-hidden">
                            Save
                        </button>
                    </div>
                </form>
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
