<div class="bg-[#F8FAFC] dark:bg-[#18191A] min-h-full">

    {{-- ========== HEADER ========== --}}
    <header class="bg-white dark:bg-[#242526] border-b border-black/5 dark:border-white/5 sticky top-1 z-30">
        <div class="max-w-[1100px] mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-black dark:text-white leading-none" style="font-family: 'Fraunces', serif;">
                    Settings
                </h1>
                <p class="text-xs text-black/50 dark:text-white/50 mt-1">Manage your account and preferences</p>
            </div>
        </div>
    </header>

    {{-- ========== LAYOUT ========== --}}
    <div class="max-w-[1100px] mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ===== SIDEBAR TABS ===== --}}
        <aside class="lg:col-span-3">
            <nav class="space-y-1 sticky top-36">
                <p class="px-3 mb-2 text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">
                    General
                </p>

                <button type="button" wire:click="setTab('appearance')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'appearance' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    Appearance
                </button>

                <button type="button" wire:click="setTab('profile')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'profile' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    My Profile
                </button>
            </nav>
        </aside>

        {{-- ===== CONTENT ===== --}}
        <main class="lg:col-span-9">

            {{-- ========== APPEARANCE TAB ========== --}}
            @if ($activeTab === 'appearance')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                            Appearance
                        </h2>
                        <p class="text-sm text-black/60 dark:text-white/60">Choose how the interface looks to you.</p>
                    </div>

                    <section class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6"
                             x-data="{
                                 theme: localStorage.getItem('theme') || 'system',
                                 setTheme(value) {
                                     this.theme = value;
                                     localStorage.setItem('theme', value);
                                     this.applyTheme();
                                 },
                                 applyTheme() {
                                     const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                                     const shouldBeDark = this.theme === 'dark' || (this.theme === 'system' && prefersDark);
                                     document.documentElement.classList.toggle('dark', shouldBeDark);
                                 }
                             }"
                             x-init="applyTheme()">

                        <h3 class="text-sm font-bold text-black dark:text-white uppercase tracking-wide mb-4">
                            Theme
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Light --}}
                            <button type="button" @click="setTheme('light')"
                                class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition text-center
                                    {{-- selected --}}
                                    border-black/10 dark:border-white/10 hover:border-[#1C6B45]/50 dark:hover:border-[#D4A537]/50"
                                :class="theme === 'light'
                                    ? 'border-[#1C6B45] dark:border-[#D4A537] ring-2 ring-[#1C6B45]/20 dark:ring-[#D4A537]/20'
                                    : ''">
                                <div class="w-12 h-12 rounded-lg bg-[#F8FAFC] border border-black/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-black dark:text-white">Light</span>
                                <span class="text-xs text-black/50 dark:text-white/50">Bright interface</span>
                            </button>

                            {{-- Dark --}}
                            <button type="button" @click="setTheme('dark')"
                                class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition text-center
                                    border-black/10 dark:border-white/10 hover:border-[#1C6B45]/50 dark:hover:border-[#D4A537]/50"
                                :class="theme === 'dark'
                                    ? 'border-[#1C6B45] dark:border-[#D4A537] ring-2 ring-[#1C6B45]/20 dark:ring-[#D4A537]/20'
                                    : ''">
                                <div class="w-12 h-12 rounded-lg bg-[#18191A] border border-white/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-black dark:text-white">Dark</span>
                                <span class="text-xs text-black/50 dark:text-white/50">Easy on the eyes</span>
                            </button>

                            {{-- System --}}
                            <button type="button" @click="setTheme('system')"
                                class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition text-center
                                    border-black/10 dark:border-white/10 hover:border-[#1C6B45]/50 dark:hover:border-[#D4A537]/50"
                                :class="theme === 'system'
                                    ? 'border-[#1C6B45] dark:border-[#D4A537] ring-2 ring-[#1C6B45]/20 dark:ring-[#D4A537]/20'
                                    : ''">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#F8FAFC] to-[#18191A] border border-black/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-black dark:text-white">System</span>
                                <span class="text-xs text-black/50 dark:text-white/50">Follow OS setting</span>
                            </button>
                        </div>
                    </section>
                </div>
            @endif

            {{-- ========== PROFILE TAB ========== --}}
            @if ($activeTab === 'profile')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                            My Profile
                        </h2>
                        <p class="text-sm text-black/60 dark:text-white/60">Update your personal information.</p>
                    </div>

                    @if (session('profile_success'))
                        <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd" />
                            </svg>
                            {{ session('profile_success') }}
                        </div>
                    @endif

                    <section class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6">
                        <form wire:submit.prevent="saveProfile" class="space-y-6">

                            {{-- Avatar --}}
                            <div class="flex items-center gap-5">
                                @php
                                    $preview = $avatarFile
                                        ? $avatarFile->temporaryUrl()
                                        : $this->avatarUrl;
                                @endphp

                                @if ($preview)
                                    <img src="{{ $preview }}"
                                         alt="Avatar"
                                         class="w-20 h-20 rounded-2xl object-cover border border-black/10 dark:border-white/10 shrink-0 bg-[#F1EFE7] dark:bg-[#3A3B3C]">
                                @else
                                    <div class="w-20 h-20 rounded-2xl bg-[#123524]/10 dark:bg-[#D4A537]/15 flex items-center justify-center text-[#123524] dark:text-[#D4A537] text-2xl font-bold shrink-0">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                        Avatar
                                    </label>
                                    <input type="file" wire:model="avatarFile" accept="image/*"
                                           class="block w-full text-sm text-black/70 dark:text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#1C6B45] file:text-white hover:file:bg-[#165a3b] file:cursor-pointer cursor-pointer">
                                    <div wire:loading wire:target="avatarFile" class="text-xs text-black/40 dark:text-white/40 mt-1">
                                        Uploading…
                                    </div>
                                    @error('avatarFile')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Gender + Batch --}}
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="gender"
                                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                        Batch <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="batch_id"
                                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                        <option value="">Select batch</option>
                                        @foreach ($this->batches as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('batch_id')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Phone numbers --}}
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                        Contact Number 1 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="contact_number_1"
                                           placeholder="e.g. +639171234567"
                                           class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                    @error('contact_number_1')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                        Contact Number 2 <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="text" wire:model="contact_number_2"
                                           placeholder="e.g. +639171234567"
                                           class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                    @error('contact_number_2')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Address --}}
                            <div>
                                <label class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                                    Home Address
                                </label>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">Region <span class="text-red-500">*</span></label>
                                        <select wire:model.live="regionCode"
                                                class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                            <option value="">Select region</option>
                                            @foreach ($this->regions as $region)
                                                <option value="{{ $region->code }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('regionCode')
                                            <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if ($regionCode)
                                        <div>
                                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">Province <span class="text-red-500">*</span></label>
                                            <select wire:model.live="provinceCode"
                                                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                                <option value="">Select province</option>
                                                @foreach ($this->provinces as $province)
                                                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('provinceCode')
                                                <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    @if ($provinceCode)
                                        <div>
                                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">City / Municipality <span class="text-red-500">*</span></label>
                                            <select wire:model.live="cityCode"
                                                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                                <option value="">Select city / municipality</option>
                                                @foreach ($this->cities as $city)
                                                    <option value="{{ $city->code }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('cityCode')
                                                <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    @if ($cityCode)
                                        <div>
                                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">Barangay <span class="text-red-500">*</span></label>
                                            <select wire:model.live="barangayCode"
                                                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                                <option value="">Select barangay</option>
                                                @foreach ($this->barangays as $barangay)
                                                    <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('barangayCode')
                                                <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5">
                                    <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">Street Address <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="street_address"
                                           placeholder="House no., street, subdivision, etc."
                                           class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#1C6B45] dark:focus:ring-[#D4A537] transition">
                                    @error('street_address')
                                        <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                                <button type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveProfile,avatarFile"
                                    class="px-6 py-2.5 rounded-xl bg-[#1C6B45] hover:bg-[#165a3b] text-white text-sm font-semibold transition disabled:opacity-60">
                                    <span wire:loading.remove wire:target="saveProfile">Save Changes</span>
                                    <span wire:loading wire:target="saveProfile">Saving…</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

        </main>
    </div>
</div>