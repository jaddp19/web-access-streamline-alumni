<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-3xl mx-auto">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('view-role') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524] hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to roles
                </a>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-black/5 bg-white shadow-sm">

                <!-- Header -->
                <div class="px-6 py-5 flex items-center gap-4 border-b border-black/5">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Create Role &amp; Permissions</h2>
                        <p class="text-sm text-black/50">Define a role and assign what it can access</p>
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="save" class="p-6 space-y-6">

                    <!-- Role Name -->
                    <div>
                        <label for="name" class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Role Name</label>
                        <input wire:model.defer="name" type="text" id="name"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F7F5EF] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Permissions -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-black/70 uppercase tracking-wide">Assign Permissions</h3>
                            @if (!empty($selectedPermissions))
                                <span class="text-xs font-semibold text-[#123524] bg-[#123524]/10 px-2 py-0.5 rounded-full">
                                    {{ count($selectedPermissions) }} selected
                                </span>
                            @endif
                        </div>
                        @error('selectedPermissions') <span class="block text-red-500 text-sm mb-3">{{ $message }}</span> @enderror

                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @forelse ($this->permissions as $pKey => $permission)
                                <label wire:key="{{ $pKey }}" for="permission-{{ $pKey }}"
                                    class="flex items-center p-3 w-full bg-[#F7F5EF] border border-black/5 rounded-lg text-sm hover:bg-[#F0EEE4] transition cursor-pointer">
                                    <input type="checkbox" id="permission-{{ $pKey }}"
                                        wire:model="selectedPermissions" value="{{ $permission->name }}"
                                        class="shrink-0 size-4 rounded-sm border-black/20 text-[#123524] focus:ring-[#123524]">
                                    <span class="text-sm ms-3 text-black">
                                        {{ Str::title(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                </label>
                            @empty
                                <p class="text-black/40 text-sm italic col-span-full">No permissions found.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3 pt-2 border-t border-black/5">
                        <button type="submit"
                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                            Create Role
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                        <a href="{{ route('view-role') }}"
                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
