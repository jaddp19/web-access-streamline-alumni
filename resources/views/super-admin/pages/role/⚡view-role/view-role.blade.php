<div>
    <!-- Table Section -->
    <div
        class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto  rounded-xl">
        <!-- Card -->
        <div class="flex flex-col rounded-xl border border-white/30 p-4 sm:p-6 lg:p-8 bg-white/10 shadow-2xs">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md">
                <div class="min-w-full inline-block align-middle">
                    <div class="rounded-xl shadow-2xs overflow-hidden">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center">
                            <div>
                                <h2 class="text-xl font-semibold text-neutral-200">
                                    Role & Permissions
                                </h2>
                                <p class="text-sm text-neutral-200">
                                    Manage role and permissions
                                </p>
                                @if (!empty($selectedRoles))
                                    <div class="py-2">
                                        <button x-data
                                            @click="
                                            if (confirm('Are you sure you want to delete ' + {{ count($selectedRoles) }} + ' role(s)?')) {
                                                $wire.deleteSelected()
                                            }
                                        "
                                            id="btn-cancel"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                            Delete Selected ({{ count($selectedRoles) }})
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-yellow-500 text-white shadow-2xs hover:bg-yellow-600 focus:outline-hidden focus:bg-yellow-500"
                                        href="#">
                                        View all
                                    </a>
                                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-hidden focus:bg-yellow-500"
                                        href="{{ route('create-role') }}">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Add Role
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Table -->
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead class="bg-yellow-500">
                                <tr>
                                    <th class="ps-2 sm:ps-6 py-3">
                                        <input type="checkbox" wire:click="toggleSelectAll" @checked($selectAll)
                                            x-data x-init="$watch('$wire.selectedRoles', value => {
                                                const total = {{ $this->totalRolesCount }};
                                                const selected = value.length;
                                                $el.indeterminate = selected > 0 && selected < total;
                                                $el.checked = selected === total;
                                            })"
                                            class="border-gray-300 rounded-sm text-blue-600">
                                    </th>
                                    <th class="ps-2 sm:ps-6 py-3 text-start font-semibold uppercase text-green-700">
                                        Role
                                    </th>
                                    <th
                                        class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-semibold uppercase text-green-700">
                                        Permissions
                                    </th>
                                    <th
                                        class="hidden md:table-cell px-2 sm:px-6 py-3 text-start font-semibold uppercase text-green-700">
                                        Created
                                    </th>
                                    <th class="px-2 sm:px-6 py-3 text-end"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/30">
                                @forelse ($this->roles as $role)
                                    <tr>
                                        <td class="w-4 ps-2 sm:ps-6 py-3 text-center align-middle">
                                            <input type="checkbox" wire:click="toggleRowSelection({{ $role->id }})"
                                                x-data
                                                x-bind:checked="@js($selectedRoles).includes({{ $role->id }})"
                                                class="border-gray-300 rounded-sm text-blue-600 align-middle">
                                        </td>
                                        <td class="px-2 sm:px-6 py-3">
                                            <span class="block font-semibold text-neutral-200">
                                                {{ \Illuminate\Support\Str::ucfirst($role->name) }}
                                            </span>
                                        </td>
                                        <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                            <span class="block text-neutral-200">
                                                {{ ucwords(str_replace('_', ' ', $role->permissions->pluck('name')->implode(', '))) }}
                                            </span>
                                        </td>
                                        <td class="hidden md:table-cell px-2 sm:px-6 py-3">
                                            <span class="text-neutral-200">
                                                {{ $role->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-2 sm:px-6 py-1.5 text-start">
                                            <a href="{{ route('update-role', $role->id) }}"
                                                class="text-neutral-200 hover:underline">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-2 sm:px-6 py-4 text-center text-neutral-200">
                                            No roles found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->

                        <!-- Footer -->
                        <div
                            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-white/30">
                            <div>
                                <p class="text-sm text-neutral-200">
                                    <span class="font-semibold text-neutral-200">
                                        {{ $this->roles->total() }}
                                    </span>
                                    results
                                </p>
                            </div>
                            <div>
                                <div class="inline-flex gap-x-2">
                                    {{-- Prev Button --}}
                                    @if ($this->roles->onFirstPage())
                                        <button disabled
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-medium rounded-md border text-gray-400 cursor-not-allowed">
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15l-6-6 6-6" />
                                            </svg>
                                            Prev
                                        </button>
                                    @else
                                        <button wire:click="previousPage"
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-medium rounded-md hover:shadow-md transition-all duration-200 bg-yellow-500 text-white hover:bg-yellow-600">
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15l-6-6 6-6" />
                                            </svg>
                                            Prev
                                        </button>
                                    @endif

                                    {{-- Next Button --}}
                                    @if ($this->roles->hasMorePages())
                                        <button wire:click="nextPage"
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-0 text-sm font-medium rounded-md hover:shadow-md transition-all duration-200 bg-yellow-500 text-white hover:bg-yellow-600">
                                            Next
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 3l6 6-6 6" />
                                            </svg>
                                        </button>
                                    @else
                                        <button disabled
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-medium rounded-md border text-gray-400 cursor-not-allowed">
                                            Next
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 3l6 6-6 6" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>
