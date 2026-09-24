<div>
    <div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14">
        <div class="relative flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            {{-- ===================== HEADER ===================== --}}
            <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#D4A537]/15 dark:bg-[#D4A537]/20 flex items-center justify-center text-[#a97f1f] dark:text-[#E5B94A] shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Duplicate Detection
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">
                            Suspected duplicate records that may need review
                        </p>
                    </div>
                </div>
            </div>

            {{-- ===================== SUMMARY BANNER ===================== --}}
            <div class="px-4 sm:px-6 py-4 border-b border-black/5 dark:border-white/5">
                @if ($this->totalIssues === 0)
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl
                                bg-emerald-50 dark:bg-emerald-500/10
                                border border-emerald-200 dark:border-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">All clear</p>
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80">
                                No duplicate records detected. Data is consistent.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl
                                bg-amber-50 dark:bg-amber-500/10
                                border border-amber-200 dark:border-amber-500/20">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                                {{ $this->totalIssues }} {{ \Illuminate\Support\Str::plural('group', $this->totalIssues) }} flagged
                            </p>
                            <p class="text-xs text-amber-700/80 dark:text-amber-400/80">
                                Review the records below and contact users for verification.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ===================== TABS ===================== --}}
            <div class="px-4 sm:px-6 py-3 border-b border-black/5 dark:border-white/5">
                <div class="inline-flex items-center gap-1 p-1 rounded-xl bg-black/5 dark:bg-white/5 overflow-x-auto">

                    {{-- Name + Batch tab --}}
                    <button type="button" wire:click="setTab('name')"
                        class="px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                            {{ $tab === 'name'
                                ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                        Name + Batch
                        @if ($this->nameGroups->count() > 0)
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500 text-white font-bold">
                                {{ $this->nameGroups->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- Contact Number tab --}}
                    <button type="button" wire:click="setTab('contact')"
                        class="px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                            {{ $tab === 'contact'
                                ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                        Contact Number
                        @if ($this->contactGroups->count() > 0)
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500 text-white font-bold">
                                {{ $this->contactGroups->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- Email tab --}}
                    <button type="button" wire:click="setTab('email')"
                        class="px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition whitespace-nowrap
                            {{ $tab === 'email'
                                ? 'bg-white dark:bg-[#242526] text-[#123524] dark:text-white shadow-sm'
                                : 'text-black/60 dark:text-white/60 hover:text-black dark:hover:text-white' }}">
                        Email
                        @if ($this->emailGroups->count() > 0)
                            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500 text-white font-bold">
                                {{ $this->emailGroups->count() }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- ===================== CONTENT ===================== --}}
            <div class="divide-y divide-black/5 dark:divide-white/5">

                {{-- ============== NAME + BATCH TAB ============== --}}
                @if ($tab === 'name')
                    @forelse ($this->nameGroups as $group)
                        <div wire:key="name-group-{{ md5($group['key'] . $group['batch']) }}" class="p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                             bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                                    {{ $group['count'] }} matches
                                </span>
                                <p class="text-sm font-semibold text-[#123524] dark:text-white">
                                    {{ $group['key'] }}
                                    @if ($group['batch'])
                                        <span class="text-black/50 dark:text-white/50 font-normal">· Batch {{ $group['batch'] }}</span>
                                    @endif
                                </p>
                            </div>

                            <div class="space-y-2">
                                @foreach ($group['users'] as $user)
                                    <div class="flex items-center gap-3 p-3 rounded-xl
                                                bg-[#F7F5EF] dark:bg-[#3A3B3C]
                                                border border-black/5 dark:border-white/5">
                                        <div class="w-9 h-9 rounded-full bg-[#123524] dark:bg-[#D4A537] flex items-center justify-center text-white dark:text-[#123524] font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-[#123524] dark:text-white truncate">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-black/60 dark:text-white/60 truncate">
                                                {{ $user->email }}
                                                @if ($user->contact_number_1)
                                                    · {{ $user->contact_number_1 }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-[10px] text-black/40 dark:text-white/40">
                                                Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}
                                            </p>
                                            @if ($user->is_verified)
                                                <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded
                                                             bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 font-semibold">
                                                    Verified
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded
                                                             bg-black/5 dark:bg-white/10 text-black/50 dark:text-white/50 font-semibold">
                                                    Unverified
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-[#123524] dark:text-white">No name + batch duplicates</p>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-1">Every alumni name within a batch is unique.</p>
                        </div>
                    @endforelse
                @endif

                {{-- ============== CONTACT NUMBER TAB ============== --}}
                @if ($tab === 'contact')
                    @forelse ($this->contactGroups as $group)
                        <div wire:key="contact-group-{{ md5($group['key']) }}" class="p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                             bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                                    {{ $group['count'] }} matches
                                </span>
                                <p class="text-sm font-mono font-semibold text-[#123524] dark:text-white">
                                    {{ $group['key'] }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                @foreach ($group['profiles'] as $profile)
                                    <div class="flex items-center gap-3 p-3 rounded-xl
                                                bg-[#F7F5EF] dark:bg-[#3A3B3C]
                                                border border-black/5 dark:border-white/5">
                                        <div class="w-9 h-9 rounded-full bg-[#123524] dark:bg-[#D4A537] flex items-center justify-center text-white dark:text-[#123524] font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($profile->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-[#123524] dark:text-white truncate">
                                                {{ $profile->user?->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-black/60 dark:text-white/60 truncate">
                                                {{ $profile->user?->email }}
                                                @if ($profile->batch)
                                                    · Batch {{ $profile->batch->batch_name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-[#123524] dark:text-white">No contact number duplicates</p>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-1">Every mobile number belongs to a unique account.</p>
                        </div>
                    @endforelse
                @endif

                {{-- ============== EMAIL TAB ============== --}}
                @if ($tab === 'email')
                    @forelse ($this->emailGroups as $group)
                        <div wire:key="email-group-{{ md5($group['key']) }}" class="p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                             bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                                    {{ $group['count'] }} matches
                                </span>
                                <p class="text-sm font-semibold text-[#123524] dark:text-white truncate">
                                    {{ $group['key'] }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                @foreach ($group['users'] as $user)
                                    <div class="flex items-center gap-3 p-3 rounded-xl
                                                bg-[#F7F5EF] dark:bg-[#3A3B3C]
                                                border border-black/5 dark:border-white/5">
                                        <div class="w-9 h-9 rounded-full bg-[#123524] dark:bg-[#D4A537] flex items-center justify-center text-white dark:text-[#123524] font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-[#123524] dark:text-white truncate">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-black/60 dark:text-white/60 truncate">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-[#123524] dark:text-white">No email duplicates</p>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-1">Every email address is unique.</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>
    </div>
</div>