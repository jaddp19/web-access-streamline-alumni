<div>
    <!-- Table Section -->
    <div class="max-w-[85rem] px-3 sm:px-4 lg:px-6 xl:px-8 py-4 sm:py-6 lg:py-10 xl:py-14 mx-auto">
        <!-- Card -->
        <div
            class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- Loading overlay (subtle, non-blocking visual) -->
            <div wire:loading.flex
                wire:target="search,roleFilter,departmentFilter,courseFilter,setRoleFilter,clearCourseFilters,nextPage,previousPage,gotoPage,deleteSelected"
                class="absolute inset-0 z-20 hidden items-start justify-center bg-white/60 dark:bg-[#242526]/60 backdrop-blur-[1px] pt-24 pointer-events-none">
                <svg class="w-6 h-6 animate-spin text-[#123524] dark:text-[#D4A537]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>

            <!-- ===================== HEADER ===================== -->
            <div
                class="px-3 sm:px-5 lg:px-6 py-4 sm:py-5 flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-center border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            All Users
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Manage all users</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="exportFilteredCsv" wire:loading.attr="disabled"
                        wire:target="exportFilteredCsv"
                        class="flex-1 sm:flex-none justify-center py-2 px-3 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition disabled:opacity-50">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <path d="M7 10l5 5 5-5" />
                            <path d="M12 15V3" />
                        </svg>
                        <span wire:loading.remove wire:target="exportFilteredCsv">Export</span>
                        <span wire:loading wire:target="exportFilteredCsv">Exporting...</span>
                    </button>

                    <a href="{{ route('super-admin.user.create') }}"
                        class="flex-1 sm:flex-none justify-center py-2 px-3 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Add User
                    </a>
                </div>
            </div>

            <!-- ===================== SEARCH + ROLE TABS ===================== -->
            <div class="px-3 sm:px-5 lg:px-6 pt-3 sm:pt-4 border-b border-black/5 dark:border-white/5">
                {{-- Search + Course/Department/Batch filters --}}
                <div class="pb-3 flex flex-col gap-2 sm:gap-3">

                    <div class="flex flex-col lg:flex-row lg:items-center gap-2 sm:gap-3">

                        {{-- Search --}}
                        <div class="relative w-full lg:w-56 xl:w-72 shrink-0">
                            <input type="text" wire:model.live.debounce.400ms="search"
                                placeholder="Search name, email, school ID"
                                class="w-full py-2 pl-9 pr-8 text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/40 focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="absolute left-3 top-2.5 h-4 w-4 text-gray-400 dark:text-white/40" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
                            </svg>

                            @if ($search !== '')
                                <button type="button" wire:click="$set('search', '')"
                                    class="absolute right-2 top-2 p-0.5 rounded text-black/40 dark:text-white/40 hover:text-black/70 dark:hover:text-white transition"
                                    title="Clear search">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Department / Course / Batch filters — only meaningful on alumni views --}}
                        @if (in_array($roleFilter, ['all', 'alumni'], true))
                            <div class="flex flex-col sm:flex-row gap-2 sm:flex-1 min-w-0">

                                {{-- Department --}}
                                <div class="relative flex-1 min-w-0">
                                    <select wire:model.live="departmentFilter" wire:loading.attr="disabled"
                                        wire:target="departmentFilter"
                                        class="w-full appearance-none py-2 pl-3 pr-8 text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
                                        <option value="">All Departments</option>
                                        @foreach ($this->departmentsList as $dept)
                                            <option value="{{ $dept->id }}">
                                                {{ $dept->dept_code ? $dept->dept_code . ' — ' : '' }}{{ $dept->dept_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <svg class="w-4 h-4 text-black/40 dark:text-white/40" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="relative flex-1 min-w-0">
                                    <select wire:model.live="courseFilter" wire:loading.attr="disabled"
                                        wire:target="courseFilter"
                                        class="w-full appearance-none py-2 pl-3 pr-8 text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
                                        <option value="">
                                            {{ $departmentFilter !== '' ? 'All Courses in Dept.' : 'All Courses' }}
                                        </option>
                                        @foreach ($this->coursesList as $course)
                                            <option value="{{ $course->id }}">
                                                {{ $course->course_code ? $course->course_code . ' — ' : '' }}{{ $course->course_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <svg class="w-4 h-4 text-black/40 dark:text-white/40" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Batch --}}
                                <div class="relative flex-1 min-w-0">
                                    <select wire:model.live="batchFilter" wire:loading.attr="disabled"
                                        wire:target="batchFilter"
                                        class="w-full appearance-none py-2 pl-3 pr-8 text-sm rounded-lg bg-[#F7F5EF] dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] cursor-pointer disabled:opacity-60 disabled:cursor-wait">
                                        <option value="">All Batches</option>
                                        @foreach ($this->batchesList as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <svg class="w-4 h-4 text-black/40 dark:text-white/40" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Clear --}}
                                @if ($courseFilter !== '' || $departmentFilter !== '' || $batchFilter !== '')
                                    <button type="button" wire:click="clearCourseFilters"
                                        class="shrink-0 px-3 py-2 text-xs font-semibold rounded-lg border border-black/10 dark:border-white/10 text-[#123524] dark:text-[#D4A537] hover:bg-black/5 dark:hover:bg-white/5 transition whitespace-nowrap">
                                        Clear filters
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Result text --}}
                    @if ($search !== '' || $courseFilter !== '' || $departmentFilter !== '' || $batchFilter !== '')
                        <p class="text-xs text-black/50 dark:text-white/50 truncate">
                            <span
                                class="font-semibold text-[#123524] dark:text-white">{{ $this->users->total() }}</span>
                            result(s)
                            @if ($search !== '')
                                for "<span class="font-semibold">{{ $search }}</span>"
                            @endif
                            @if ($departmentFilter !== '' || $courseFilter !== '' || $batchFilter !== '')
                                <span class="text-black/40 dark:text-white/40">
                                    @php
                                        $activeFilters = array_filter([
                                            $departmentFilter !== '' ? 'dept' : null,
                                            $courseFilter !== '' ? 'course' : null,
                                            $batchFilter !== '' ? 'batch' : null,
                                        ]);
                                    @endphp
                                    (filtered by {{ implode(' / ', $activeFilters) }})
                                </span>
                            @endif
                        </p>
                    @endif
                </div>

                {{-- Role tabs — horizontal scroll with edge fade --}}
                <div class="relative -mx-3 sm:-mx-5 lg:-mx-6 px-3 sm:px-5 lg:px-6">
                    <div
                        class="flex items-center gap-1 overflow-x-auto whitespace-nowrap pb-2 [&::-webkit-scrollbar]:h-0 [scrollbar-width:none]">
                        @php
                            $tabs = [
                                'all' => 'All Users',
                                'alumni' => 'Alumni',
                                'program head' => 'Program Head',
                                'registrar' => 'Registrar',
                                'pending' => 'Pending Tracer',
                            ];
                        @endphp
                        @foreach ($tabs as $key => $label)
                            <button type="button" wire:click="setRoleFilter('{{ $key }}')"
                                class="shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-t-lg border-b-2 transition
                    {{ $roleFilter === $key
                        ? 'text-[#123524] dark:text-[#D4A537] border-[#123524] dark:border-[#D4A537]'
                        : 'text-black/50 dark:text-white/50 border-transparent hover:text-[#123524] dark:hover:text-[#D4A537]' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ===================== BULK ACTION BAR ===================== -->
            @if (!empty($selectedUsers))
                <div
                    class="px-3 sm:px-5 lg:px-6 py-3 bg-red-50 dark:bg-red-500/10 border-b border-red-100 dark:border-red-500/20 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs sm:text-sm text-red-700 dark:text-red-400 font-medium">
                        {{ $selectAllFiltered ? $this->totalUsersCount : count($selectedUsers) }} user(s) selected
                    </p>
                    <div class="flex flex-col xs:flex-row gap-2 sm:items-center">
                        <button type="button" wire:click="exportSelectedCsv" wire:loading.attr="disabled"
                            wire:target="exportSelectedCsv"
                            class="w-full xs:w-auto px-4 py-1.5 bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="exportSelectedCsv">Export Selected</span>
                            <span wire:loading wire:target="exportSelectedCsv">Exporting...</span>
                        </button>
                        <button x-data
                            @click="
                                if (confirm('Are you sure you want to delete {{ $selectAllFiltered ? $this->totalUsersCount : count($selectedUsers) }} user(s)?')) {
                                    $wire.deleteSelected()
                                }
                            "
                            class="w-full xs:w-auto px-4 py-1.5 bg-red-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                            Delete Selected
                        </button>
                    </div>
                </div>
            @endif

            <!-- ===================== MOBILE CARD LIST (< sm) ===================== -->
            <div class="sm:hidden divide-y divide-black/5 dark:divide-white/5">
                @forelse ($this->users as $user)
                    <div wire:key="mobile-user-{{ $user->id }}" class="p-3 xs:p-4 flex items-start gap-3">
                        <input type="checkbox"
                            wire:key="mobile-user-cb-{{ $user->id }}-{{ in_array($user->id, $selectedUsers, true) ? '1' : '0' }}"
                            wire:click="toggleRowSelection({{ $user->id }})"
                            @checked(in_array($user->id, $selectedUsers, true))
                            class="mt-1.5 rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] shrink-0">

                        @php
                            $rawAvatar = $user->userProfile?->avatar;
                            $avatarUrl = $rawAvatar
                                ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                                    ? $rawAvatar
                                    : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                                : null;
                            $initial = strtoupper(substr($user->name, 0, 1));
                        @endphp

                        <div class="shrink-0">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" loading="lazy"
                                    class="w-9 h-9 rounded-full object-cover bg-[#123524]/10 dark:bg-white/10"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none;"
                                    class="w-9 h-9 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold">
                                    {{ $initial }}
                                </div>
                            @else
                                <div
                                    class="w-9 h-9 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold">
                                    {{ $initial }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-[#123524] dark:text-white truncate">{{ $user->name }}
                                </p>
                                <div class="shrink-0 flex items-center gap-2">
                                    <a href="{{ route('super-admin.alumni.view-single', $user->id) }}"
                                        class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                                        View
                                    </a>
                                    <span class="text-black/20 dark:text-white/20">|</span>
                                    <a href="{{ route('super-admin.user.update', $user->id) }}"
                                        class="text-xs font-semibold text-black/50 dark:text-white/50 hover:underline">
                                        Edit
                                    </a>
                                </div>
                            </div>
                            <p class="text-xs text-black/60 dark:text-white/60 truncate">{{ $user->email }}</p>

                            <div class="mt-2 flex flex-wrap items-center gap-1">
                                @php $roleNames = $user->roles->pluck('name'); @endphp
                                @forelse ($roleNames as $roleName)
                                    <span
                                        class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide">
                                        {{ $roleName }}
                                    </span>
                                @empty
                                    <span class="text-xs text-black/40 dark:text-white/40 italic">No role</span>
                                @endforelse
                            </div>

                            @if ($user->hasRole('alumni'))
                                <div class="mt-1.5">
                                    @if ($user->tracerStudy)
                                        <span
                                            class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-green-600/10 dark:bg-emerald-500/15 text-green-700 dark:text-emerald-400 font-semibold uppercase tracking-wide">
                                            Tracer: Completed
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wide">
                                            Tracer: Pending
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <p class="mt-1.5 text-[11px] text-black/40 dark:text-white/40">
                                {{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none"
                                stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <p class="text-black/40 dark:text-white/40 text-sm">No users found for this filter.</p>
                    </div>
                @endforelse

                @if ($this->users->count() > 0)
                    <div class="px-3 xs:px-4 py-3">
                        <button type="button" wire:click="toggleSelectAll"
                            class="text-xs font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                            {{ $selectAllFiltered ? 'Deselect all' : 'Select all users' }}
                        </button>
                    </div>
                @endif
            </div>
            <!-- ===================== END MOBILE CARD LIST ===================== -->

            <!-- ===================== TABLE (sm and up) ===================== -->
            <div
                class="hidden sm:block overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-black/10 dark:[&::-webkit-scrollbar-thumb]:bg-white/10">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-[#F7F5EF] dark:bg-[#3A3B3C] border-b border-black/5 dark:border-white/5">
                        <tr>
                            <th class="ps-4 lg:ps-6 py-3 w-4">
                                <input type="checkbox"
                                    wire:key="header-user-cb-{{ $this->totalUsersCount }}"
                                    wire:click="toggleSelectAll"
                                    x-data
                                    x-effect="
                                        const total = {{ $this->totalUsersCount }};
                                        const allFiltered = $wire.selectAllFiltered === true;
                                        const selCount = allFiltered ? total : ($wire.selectedUsers || []).length;
                                        $el.checked = total > 0 && selCount >= total;
                                        $el.indeterminate = selCount > 0 && selCount < total;
                                    "
                                    title="Select all users (all pages)"
                                    class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C]">
                            </th>
                            <th
                                class="px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Name
                            </th>
                            <th
                                class="hidden md:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Email
                            </th>
                            <th
                                class="hidden lg:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Role
                            </th>
                            <th
                                class="hidden xl:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Tracer Study
                            </th>
                            <th
                                class="hidden xl:table-cell px-3 lg:px-6 py-3 text-start font-bold uppercase tracking-wide text-[#123524]/60 dark:text-white/60 text-[11px]">
                                Created
                            </th>
                            <th class="px-3 lg:px-6 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @forelse ($this->users as $user)
                            @php
                                $rawAvatar = $user->userProfile?->avatar;
                                $avatarUrl = $rawAvatar
                                    ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                                        ? $rawAvatar
                                        : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                                    : null;
                                $initial = strtoupper(substr($user->name, 0, 1));
                                $roleNames = $user->roles->pluck('name');
                            @endphp

                            <tr wire:key="table-user-{{ $user->id }}"
                                class="hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors">
                                <td class="w-4 ps-4 lg:ps-6 py-3 text-center align-middle">
                                    <input type="checkbox"
                                        wire:key="table-user-cb-{{ $user->id }}-{{ in_array($user->id, $selectedUsers, true) ? '1' : '0' }}"
                                        wire:click="toggleRowSelection({{ $user->id }})"
                                        @checked(in_array($user->id, $selectedUsers, true))
                                        class="rounded border-black/20 dark:border-white/20 text-[#123524] dark:text-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] dark:bg-[#3A3B3C] align-middle">
                                </td>
                                <td class="px-3 lg:px-6 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="shrink-0">
                                            @if ($avatarUrl)
                                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                                                    loading="lazy"
                                                    class="w-8 h-8 rounded-full object-cover bg-[#123524]/10 dark:bg-white/10"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div style="display: none;"
                                                    class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold">
                                                    {{ $initial }}
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-xs font-bold">
                                                    {{ $initial }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <span
                                                class="font-semibold text-[#123524] dark:text-white block truncate">{{ $user->name }}</span>
                                            <span
                                                class="md:hidden text-black/50 dark:text-white/50 text-[11px] block truncate">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 lg:px-6 py-3 max-w-[220px] lg:max-w-none">
                                    <span
                                        class="text-black/70 dark:text-white/70 block truncate">{{ $user->email }}</span>
                                </td>
                                <td class="hidden lg:table-cell px-3 lg:px-6 py-3">
                                    @forelse ($roleNames as $roleName)
                                        <span
                                            class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide mr-1 mb-0.5">
                                            {{ $roleName }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-black/40 dark:text-white/40 italic">No role</span>
                                    @endforelse
                                </td>
                                <td class="hidden xl:table-cell px-3 lg:px-6 py-3">
                                    @if ($user->hasRole('alumni'))
                                        @if ($user->tracerStudy)
                                            <span
                                                class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-green-600/10 dark:bg-emerald-500/15 text-green-700 dark:text-emerald-400 font-semibold uppercase tracking-wide">
                                                Completed
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 font-semibold uppercase tracking-wide">
                                                Pending
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-black/30 dark:text-white/30 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="hidden xl:table-cell px-3 lg:px-6 py-3">
                                    <span
                                        class="text-black/50 dark:text-white/50 whitespace-nowrap">{{ $user->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 text-end">
                                    <div class="flex items-center justify-end gap-2 lg:gap-3">
                                        <a href="{{ route('super-admin.alumni.view-single', $user->id) }}"
                                            class="inline-flex items-center gap-1 text-[#123524] dark:text-[#D4A537] hover:text-[#0d2819] dark:hover:text-[#E5B94A] font-semibold hover:underline whitespace-nowrap">
                                            View
                                        </a>
                                        <a href="{{ route('super-admin.user.update', $user->id) }}"
                                            class="inline-flex items-center gap-1 text-black/50 dark:text-white/50 hover:text-[#123524] dark:hover:text-[#D4A537] font-semibold hover:underline whitespace-nowrap">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div
                                        class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#123524]/5 dark:bg-white/5 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#123524]/30 dark:text-white/30" fill="none"
                                            stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <p class="text-black/40 dark:text-white/40 text-sm">No users found for this filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- ===================== END TABLE ===================== -->

            <!-- ===================== FOOTER / PAGINATION ===================== -->
            <div
                class="px-3 sm:px-5 lg:px-6 py-3 sm:py-4 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center border-t border-black/5 dark:border-white/5">
                <p class="text-xs sm:text-sm text-black/60 dark:text-white/60 text-center sm:text-left">
                    Showing
                    <span
                        class="font-semibold text-[#123524] dark:text-white">{{ $this->users->firstItem() ?? 0 }}</span>–<span
                        class="font-semibold text-[#123524] dark:text-white">{{ $this->users->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-[#123524] dark:text-white">{{ $this->users->total() }}</span>
                    results
                </p>

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    {{-- Prev --}}
                    @if ($this->users->onFirstPage())
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @else
                        <button wire:click="previousPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 15l-6-6 6-6" />
                            </svg>
                            Prev
                        </button>
                    @endif

                    {{-- Page indicator (mobile only) --}}
                    <span class="sm:hidden text-xs font-semibold text-black/60 dark:text-white/60 whitespace-nowrap">
                        {{ $this->users->currentPage() }} / {{ $this->users->lastPage() }}
                    </span>

                    {{-- Next --}}
                    @if ($this->users->hasMorePages())
                        <button wire:click="nextPage"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @else
                        <button disabled
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 inline-flex items-center justify-center gap-x-1 text-xs sm:text-sm font-semibold rounded-lg border border-black/10 dark:border-white/10 text-black/30 dark:text-white/30 cursor-not-allowed">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 3l6 6-6 6" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            <!-- ===================== END FOOTER ===================== -->

        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>