<div>
    <!-- Table Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Card -->
        <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm">

            <!-- Header -->
            <div class="px-6 py-5 grid gap-3 md:flex md:justify-between md:items-center border-b border-black/5">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">All Departments</h2>
                        <p class="text-sm text-black/50">Manage all departments</p>
                    </div>
                </div>

                <div class="flex items-center gap-x-2">
                    <a href="{{ route('super-admin.department.view') }}"
                        class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-[#123524]/15 text-[#123524] hover:bg-[#123524]/5 transition">
                        View all
                    </a>
                    <a href="{{ route('super-admin.department.create') }}"
                        class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Add Department
                    </a>
                </div>
            </div>
            <!-- End Header -->

            <!-- Bulk action bar (only when rows selected) -->
            @if(!empty($selectedDepartments))
                <div class="px-6 py-3 bg-red-50 border-b border-red-100 flex items-center justify-between">
                    <p class="text-sm text-red-700 font-medium">{{ count($selectedDepartments) }} department(s) selected</p>
                    <button
                        x-data
                        @click="
                            if (confirm('Are you sure you want to delete ' + {{ count($selectedDepartments) }} + ' department(s)?')) {
                                $wire.deleteSelected()
                            }
                        "
                        class="px-4 py-1.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete Selected
                    </button>
                </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] border-b border-black/5">
                        <tr>
                            <th class="ps-6 py-3 w-4">
                                <input type="checkbox"
                                    wire:click="toggleSelectAll"
                                    @checked($selectAll)
                                    x-data
                                    x-init="$watch('$wire.selectedDepartments', value => {
                                        const total = {{ $this->totalDepartmentsCount }};
                                        const selected = value.length;
                                        $el.indeterminate = selected > 0 && selected < total;
                                        $el.checked = selected === total;
                                    })"
                                    class="rounded border-black/20 text-[#123524] focus:ring-[#123524]">
                            </th>
                            <th class="px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Name</th>
                            <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Code</th>
                            <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Status</th>
                            <th class="hidden md:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 text-[11px]">Created</th>
                            <th class="px-2 sm:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5">
                        @forelse ($this->departments as $department)
                            <tr class="hover:bg-black/[0.02] transition-colors">
                                <td class="w-4 ps-6 py-3 text-center align-middle">
                                    <input type="checkbox"
                                        wire:click="toggleRowSelection({{ $department->id }})"
                                        x-data
                                        x-bind:checked="@js($selectedDepartments).includes({{ $department->id }})"
                                        class="rounded border-black/20 text-[#123524] focus:ring-[#123524] align-middle">
                                </td>
                                <td class="px-2 sm:px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($department->dept_logo)
                                        <img src="{{ Storage::url($department->dept_logo) }}" alt="{{ $department->dept_name }}"
                                            class="w-8 h-8 rounded-full object-cover shrink-0">
                                        @else
                                        <div class="w-8 h-8 rounded-full bg-[#123524]/10 flex items-center justify-center text-[#123524] text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($department->dept_name, 0, 1)) }}
                                        </div>
                                        @endif
                                        <span class="font-semibold text-[#123524]">{{ $department->dept_name }}</span>
                                        @if($department->dept_desc)
                                        <span class="block text-xs text-black/50 truncate max-w-[220px]">{{ $department->dept_desc }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/70 font-mono text-xs">{{ $department->dept_code }}</span>
                                </td>
                                <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                    @if ($department->is_active)
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold uppercase tracking-wide">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-black/5 text-black/40 font-semibold uppercase tracking-wide">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/50">{{ $department->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-2 sm:px-6 py-3 text-end">
                                    <a href="{{ route('super-admin.department.update', $department->id) }}"
                                        class="inline-flex items-center gap-1 text-[#123524] hover:text-[#0d2819] font-semibold hover:underline">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 text-sm">No departments found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-black/5">
                <p class="text-sm text-black/60">
                    <span class="font-semibold text-[#123524]">{{ $this->departments->total() }}</span> results
                </p>

                <div class="inline-flex gap-x-2">
                    {{-- Prev Button --}}
                    @if($this->departments->onFirstPage())
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @endif

                    {{-- Next Button --}}
                    @if($this->departments->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @else
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 text-black/30 cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>
