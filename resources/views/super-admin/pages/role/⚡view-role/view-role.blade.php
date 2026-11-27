<div x-data="{ modalOpen: @entangle('viewingRoleId') }" 
     @keydown.escape.window="$wire.closeView()">
     
    <!-- Table Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Card -->
        <div class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm">

            <!-- Header -->
            <div class="px-6 py-5 grid gap-3 md:flex md:justify-between md:items-center border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524] dark:text-white" style="font-family: 'Fraunces', serif;">Roles &amp; Permissions</h2>
                        <p class="text-sm text-black/50 dark:text-white/50">Manage roles and permissions</p>
                    </div>
                </div>

                <div class="flex items-center gap-x-2">
                    <a href="{{ route('create-role') }}"
                        class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" /><path d="M12 5v14" />
                        </svg>
                        Add Role
                    </a>
                </div>
            </div>
            <!-- End Header -->

            <!-- Flash messages -->
            @if (session('success'))
                <div class="px-6 py-3 bg-green-50 dark:bg-green-500/10 border-b border-green-100 dark:border-green-500/20">
                    <p class="text-sm text-green-700 dark:text-green-400 font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20">
                    <p class="text-sm text-red-700 dark:text-red-400 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Bulk action bar (only when rows selected) -->
            @if (count($selectedRoles))
                <div class="px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20 flex items-center justify-between">
                    <p class="text-sm text-red-700 dark:text-red-400 font-medium">
                        {{ count($selectedRoles) }} role(s) selected
                    </p>
                    <button
                        wire:click="deleteSelected"
                        wire:confirm="Are you sure you want to delete {{ count($selectedRoles) }} role(s)?"
                        class="px-4 py-1.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete Selected
                    </button>
                </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-6 py-3 w-4">
                                <input type="checkbox"
                                    wire:key="header-role-cb-{{ $this->totalRolesCount }}"
                                    wire:click="toggleSelectAll"
                                    x-data
                                    x-effect="
                                        const total = {{ $this->totalRolesCount }};
                                        const allFiltered = $wire.selectAllFiltered === true;
                                        const selCount = allFiltered ? total : ($wire.selectedRoles || []).length;
                                        $el.checked = total > 0 && selCount >= total;
                                        $el.indeterminate = selCount > 0 && selCount < total;
                                    "
                                    title="Select all roles (all pages)"
                                    class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C]">
                            </th>
                            <th class="px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Role</th>
                            <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Permissions</th>
                            <th class="hidden md:table-cell px-2 sm:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">Created</th>
                            <th class="px-2 sm:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->roles as $role)
                            <tr class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="w-4 ps-6 py-3 text-center align-middle">
                                    <input type="checkbox"
                                        wire:key="row-role-cb-{{ $role->id }}-{{ $this->isRowSelected($role->id) ? '1' : '0' }}"
                                        wire:click="toggleRowSelection({{ $role->id }})"
                                        @checked($this->isRowSelected($role->id))
                                        class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] align-middle">
                                </td>
                                <td class="px-2 sm:px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-[#123524] dark:text-white">{{ \Illuminate\Support\Str::ucfirst($role->name) }}</span>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                    @forelse ($role->permissions->take(4) as $permission)
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide mr-1 mb-1">
                                            {{ str_replace('_', ' ', $permission->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-black/40 dark:text-white/40 italic">No permissions</span>
                                    @endforelse
                                    @if ($role->permissions->count() > 4)
                                        <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50 font-semibold">
                                            +{{ $role->permissions->count() - 4 }} more
                                        </span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-2 sm:px-6 py-3">
                                    <span class="text-black/50 dark:text-white/50">{{ $role->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-2 sm:px-6 py-3 text-end relative">
                                    {{-- 3-Dots Dropdown (Desktop) --}}
                                    <div x-data="{ open: false }" class="inline-block text-left">
                                        <button @click="open = !open" @click.outside="open = false"
                                            class="p-1.5 rounded-lg text-black/40 hover:text-black dark:text-white/40 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition focus:outline-none">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" x-transition x-cloak
                                            class="absolute right-0 z-20 mt-1 w-32 origin-top-right rounded-xl bg-white dark:bg-[#242526] shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none divide-y divide-black/5 dark:divide-white/5">
                                            
                                            {{-- View Button --}}
                                            <button type="button" wire:click="openView({{ $role->id }})"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                View
                                            </button>

                                            <a href="{{ route('update-role', $role->id) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">No roles found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-black/5 dark:border-white/5">
                <p class="text-sm text-black/60 dark:text-white/60">
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->roles->total() }}</span> results
                </p>

                <div class="inline-flex gap-x-2">
                    {{-- Prev Button --}}
                    @if ($this->roles->onFirstPage())
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @endif

                    {{-- Next Button --}}
                    @if ($this->roles->hasMorePages())
                        <button wire:click="nextPage"
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @else
                        <button disabled
                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
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

    {{-- ================= CENTERED SINGLE VIEW MODAL ================= --}}
    <template x-teleport="body">
        <div x-show="modalOpen" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            
            {{-- Backdrop --}}
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 @click="$wire.closeView()"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    
                    {{-- Modal Panel --}}
                    <div x-show="modalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#242526] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-black/5 dark:border-white/5">
                        
                        @if($this->viewingRole)
                            @php $role = $this->viewingRole; @endphp

                            {{-- Header --}}
                            <div class="px-4 py-5 sm:px-6 bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5 flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-[#123524] dark:text-white" id="modal-title" style="font-family: 'Fraunces', serif;">
                                        Role Details
                                    </h2>
                                </div>
                                <button type="button" @click="$wire.closeView()" class="relative rounded-md bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-4 py-6 sm:px-6">
                                
                                {{-- Profile Header --}}
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-16 h-16 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-2xl font-bold shrink-0">
                                        {{ strtoupper(substr($role->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold text-[#123524] dark:text-white truncate">{{ \Illuminate\Support\Str::ucfirst($role->name) }}</h3>
                                        <p class="text-sm text-black/60 dark:text-white/60">Role ID: {{ $role->id }}</p>
                                    </div>
                                </div>

                                {{-- Permissions --}}
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-black dark:text-white mb-3">Permissions ({{ $role->permissions->count() }})</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($role->permissions as $permission)
                                            <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide">
                                                {{ str_replace('_', ' ', $permission->name) }}
                                            </span>
                                        @empty
                                            <p class="text-sm text-black/40 dark:text-white/40 italic">No permissions assigned to this role.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Details List --}}
                                <div class="border-t border-black/5 dark:border-white/5 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="flex gap-x-3">
                                        <svg class="h-6 w-5 flex-none text-[#a97f1f] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-black dark:text-white">Created On</p>
                                            <p class="text-black/60 dark:text-white/60">{{ $role->created_at->format('M j, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="bg-[#F7F5EF] dark:bg-[#3A3B3C] px-4 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 border-t border-black/5 dark:border-white/5 rounded-b-2xl">
                                <button type="button" @click="$wire.closeView()"
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-white dark:bg-[#242526] px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    Close
                                </button>
                                <a href="{{ route('update-role', $role->id) }}" wire:navigate
                                    class="w-full sm:w-auto justify-center inline-flex rounded-lg bg-[#123524] dark:bg-[#D4A537] px-4 py-2.5 text-sm font-semibold text-white dark:text-[#123524] shadow-sm hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                                    Edit Role
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>