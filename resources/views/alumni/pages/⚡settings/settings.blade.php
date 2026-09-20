<div class="bg-[#F8FAFC] dark:bg-[#18191A] min-h-screen">

    {{-- ========== TOP NAVIGATION BAR ========== --}}
    <header class="bg-white dark:bg-[#242526] border-b border-black/5 dark:border-white/5 top-0 z-30">
        <div class="max-w-[1200px] mx-auto px-6 py-4 flex items-center justify-between gap-4">

            {{-- Title --}}
            <div>
                <h1 class="text-xl font-bold text-black dark:text-white leading-none"
                    style="font-family: 'Fraunces', serif;">Account Settings</h1>
                <p class="text-xs text-black/50 dark:text-white/50 mt-1">Manage your alumni portal preferences</p>
            </div>

            {{-- Back Button (right side) --}}
            <a href="{{ route('alumni.dashboard') }}"
                class="inline-flex items-center gap-x-2 px-3.5 py-2 text-sm font-semibold rounded-lg bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
                <span>Back</span>
            </a>

        </div>
    </header>

    {{-- ========== MAIN SETTINGS LAYOUT ========== --}}
    <div class="max-w-[1200px] mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ===== LEFT SIDEBAR (Settings Menu) ===== --}}
        <aside class="lg:col-span-3">
            <nav class="space-y-1 sticky top-24">
                <p class="px-3 mb-2 text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">
                    General</p>

                <button type="button" wire:click="setTab('appearance')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'appearance' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    Appearance
                </button>

                <button type="button" wire:click="setTab('profile')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'profile' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Personal Details
                </button>

                <button type="button" wire:click="setTab('password')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'password' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Security
                </button>

                <div class="my-4 border-t border-black/10 dark:border-white/10"></div>

                <p class="px-3 mb-2 text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">
                    Privacy</p>

                <button type="button" wire:click="setTab('preferences')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
        {{ $activeTab === 'preferences' ? 'text-[#1C6B45] dark:text-[#D4A537] bg-[#1C6B45]/10 dark:bg-[#D4A537]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    Network Preferences
                </button>

                {{-- Update Form — routes to the alumni tracer form --}}
                <a href="{{ route('update-form') }}" target="_blank"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
        text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    Update Form
                </a>
            </nav>
        </aside>

        {{-- ===== CONTENT PANEL ===== --}}
        <main class="lg:col-span-9">

            {{-- ========== APPEARANCE ========== --}}
            @if ($activeTab === 'appearance')
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white"
                            style="font-family: 'Fraunces', serif;">Appearance</h2>
                        <p class="text-sm text-black/60 dark:text-white/60">Choose how the interface looks to you.</p>
                    </div>

                    <section
                        class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6"
                        x-data="{
                            theme: localStorage.getItem('theme') || 'system',
                            options: ['light', 'dark', 'system'],
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
                        }" x-init="applyTheme()">

                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-sm font-bold text-black dark:text-white uppercase tracking-wide">
                                    Theme
                                </h3>
                                <p class="text-xs text-black/50 dark:text-white/50 mt-1">
                                    Choose how CSAV Alumni looks on this device.
                                </p>
                            </div>
                        </div>

                        {{-- Segmented pill toggle --}}
                        <div class="relative inline-flex w-full sm:w-auto p-1 rounded-2xl bg-[#F1EFE7] dark:bg-[#3A3B3C]">

                            {{-- sliding indicator --}}
                            <div class="absolute inset-y-1 rounded-xl bg-white dark:bg-[#123524] shadow-sm transition-all duration-300 ease-out"
                                :style="{
                                    width: 'calc((100% - 0.5rem) / 3)',
                                    left: `calc(0.25rem + (${options.indexOf(theme)} * (100% - 0.5rem) / 3))`
                                }">
                            </div>

                            <button type="button" @click="setTheme('light')"
                                class="relative z-10 flex-1 flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                                :class="theme === 'light' ? 'text-[#123524] dark:text-[#D4A537]' :
                                    'text-black/50 dark:text-white/50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4" />
                                    <path stroke-linecap="round"
                                        d="M12 3v1.5M12 19.5V21M4.5 12H3M21 12h-1.5M6 6l1 1M17 17l1 1M6 18l1-1M17 7l1-1" />
                                </svg>
                                <span class="hidden sm:inline">Light</span>
                            </button>

                            <button type="button" @click="setTheme('dark')"
                                class="relative z-10 flex-1 flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                                :class="theme === 'dark' ? 'text-[#123524] dark:text-[#D4A537]' :
                                    'text-black/50 dark:text-white/50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                                </svg>
                                <span class="hidden sm:inline">Dark</span>
                            </button>

                            <button type="button" @click="setTheme('system')"
                                class="relative z-10 flex-1 flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors"
                                :class="theme === 'system' ? 'text-[#123524] dark:text-[#D4A537]' :
                                    'text-black/50 dark:text-white/50'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="12" rx="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path stroke-linecap="round" d="M8 20h8M12 16v4" />
                                </svg>
                                <span class="hidden sm:inline">System</span>
                            </button>
                        </div>

                        <p class="text-xs text-black/40 dark:text-white/40 mt-4" x-show="theme === 'system'"
                            x-transition x-cloak>
                            Currently following your OS setting.
                        </p>
                    </section>
                </div>
            @endif

            {{-- ========== PERSONAL DETAILS ========== --}}
            @if ($activeTab === 'profile')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white"
                                style="font-family: 'Fraunces', serif;">Personal Details</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Update your basic account information.
                            </p>
                        </div>
                    </div>

                    @if (session('profile_success'))
                        <div
                            class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('profile_success') }}
                        </div>
                    @endif

                    <section
                        class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6">
                        <form wire:submit.prevent="updateProfile" class="space-y-6">
                            <!-- Name parts: First + Last side-by-side -->
                            <div class="grid sm:grid-cols-2 gap-6">

                                {{-- First Name --}}
                                <div class="space-y-1.5">
                                    <label for="first_name"
                                        class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                        First Name <span class="text-red-500 dark:text-red-400">*</span>
                                    </label>
                                    <input id="first_name" type="text" wire:model.blur="first_name"
                                        class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                @error('first_name') border-red-400 focus:border-red-500 focus:ring-red-500
                @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                @enderror">
                                    @error('first_name')
                                        <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Last Name --}}
                                <div class="space-y-1.5">
                                    <label for="last_name"
                                        class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                        Last Name <span class="text-red-500 dark:text-red-400">*</span>
                                    </label>
                                    <input id="last_name" type="text" wire:model.blur="last_name"
                                        class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                @error('last_name') border-red-400 focus:border-red-500 focus:ring-red-500
                @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                @enderror">
                                    @error('last_name')
                                        <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Middle Name (full width) -->
                            <div class="space-y-1.5">
                                <label for="middle_name"
                                    class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                    Middle Name <span
                                        class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                                </label>
                                <input id="middle_name" type="text" wire:model.blur="middle_name"
                                    class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                                @error('middle_name') border-red-400 focus:border-red-500 focus:ring-red-500
                                @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                                @enderror">
                                @error('middle_name')
                                    <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Email (full width) -->
                            <div class="space-y-1.5">
                                <label for="email"
                                    class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                    Email Address <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                <input id="email" type="email" wire:model.blur="email"
                                    class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                                @error('email') border-red-400 focus:border-red-500 focus:ring-red-500
                                @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                                @enderror">
                                @error('email')
                                    <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end pt-4">
                                <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                                    class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#165a3b] dark:hover:bg-[#E5B94A] transition shadow-sm disabled:opacity-60">
                                    <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                                    <span wire:loading wire:target="updateProfile">Saving&hellip;</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

            {{-- ========== SECURITY / PASSWORD ========== --}}
            @if ($activeTab === 'password')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white"
                                style="font-family: 'Fraunces', serif;">Security</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Manage your account password.</p>
                        </div>
                    </div>

                    @if (session('password_success'))
                        <div
                            class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('password_success') }}
                        </div>
                    @endif

                    <section
                        class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6">
                        <form wire:submit.prevent="updatePassword" class="space-y-6">

                            {{-- Current password --}}
                            <div class="space-y-1.5">
                                <label for="current_password"
                                    class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                    Current Password <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                <input id="current_password" type="password" wire:model.blur="current_password"
                                    autocomplete="current-password"
                                    class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                                        @error('current_password') border-red-400 focus:border-red-500 focus:ring-red-500
                                        @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                                        @enderror">
                                @error('current_password')
                                    <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="grid sm:grid-cols-2 gap-6">

                                {{-- New password --}}
                                <div class="space-y-1.5">
                                    <label for="new_password"
                                        class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                        New Password <span class="text-red-500 dark:text-red-400">*</span>
                                    </label>
                                    <input id="new_password" type="password" wire:model.blur="new_password"
                                        autocomplete="new-password"
                                        class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                                            @error('new_password') border-red-400 focus:border-red-500 focus:ring-red-500
                                            @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                                            @enderror">
                                    <p class="text-[11px] text-black/50 dark:text-white/50">
                                        Min 8 characters, with uppercase, lowercase, number &amp; symbol.
                                    </p>
                                    @error('new_password')
                                        <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Confirm password --}}
                                <div class="space-y-1.5">
                                    <label for="new_password_confirmation"
                                        class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">
                                        Confirm New Password <span class="text-red-500 dark:text-red-400">*</span>
                                    </label>
                                    <input id="new_password_confirmation" type="password"
                                        wire:model.blur="new_password_confirmation" autocomplete="new-password"
                                        class="w-full px-3.5 py-2.5 rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition text-sm
                                            @error('new_password_confirmation') border-red-400 focus:border-red-500 focus:ring-red-500
                                            @else border-black/10 dark:border-white/10 focus:border-[#1C6B45] dark:focus:border-[#D4A537] focus:ring-[#1C6B45] dark:focus:ring-[#D4A537]
                                            @enderror">
                                    @error('new_password_confirmation')
                                        <span class="flex items-start gap-1.5 text-red-500 dark:text-red-400 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-4">
                                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                                    class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#165a3b] dark:hover:bg-[#E5B94A] transition shadow-sm disabled:opacity-60">
                                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                    <span wire:loading wire:target="updatePassword">Updating&hellip;</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

            {{-- ========== PREFERENCES ========== --}}
            @if ($activeTab === 'preferences')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white"
                                style="font-family: 'Fraunces', serif;">Network Preferences</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Control your visibility and
                                notifications.</p>
                        </div>
                    </div>

                    @if (session('preferences_success'))
                        <div
                            class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('preferences_success') }}
                        </div>
                    @endif

                    <section
                        class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 overflow-hidden">
                        <div class="p-6">
                            <form wire:submit.prevent="savePreferences" class="space-y-4">
                                <div class="space-y-3">

                                    {{-- Email notifications --}}
                                    <label
                                        class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#3A3B3C] border rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group
                                        @error('emailNotifications') border-red-400 dark:border-red-500/40 @else border-black/5 dark:border-white/10 @enderror">
                                        <div class="flex flex-col">
                                            <p
                                                class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] dark:group-hover:text-[#D4A537] transition">
                                                Email Notifications</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Stay updated via
                                                email.</p>
                                            @error('emailNotifications')
                                                <span
                                                    class="text-red-500 dark:text-red-400 text-xs font-medium mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="emailNotifications"
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] dark:peer-checked:bg-[#D4A537] transition-colors">
                                            </div>
                                            <div
                                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm">
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Event alerts --}}
                                    <label
                                        class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#3A3B3C] border rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group
                                        @error('eventNotifications') border-red-400 dark:border-red-500/40 @else border-black/5 dark:border-white/10 @enderror">
                                        <div class="flex flex-col">
                                            <p
                                                class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] dark:group-hover:text-[#D4A537] transition">
                                                Event Alerts</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Receive instant
                                                alumni gathering alerts.</p>
                                            @error('eventNotifications')
                                                <span
                                                    class="text-red-500 dark:text-red-400 text-xs font-medium mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="eventNotifications"
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] dark:peer-checked:bg-[#D4A537] transition-colors">
                                            </div>
                                            <div
                                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm">
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Profile visibility --}}
                                    <label
                                        class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#3A3B3C] border rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group
                                        @error('profileVisible') border-red-400 dark:border-red-500/40 @else border-black/5 dark:border-white/10 @enderror">
                                        <div class="flex flex-col">
                                            <p
                                                class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] dark:group-hover:text-[#D4A537] transition">
                                                Public Profile Visibility</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Allow other
                                                alumni to find your profile.</p>
                                            @error('profileVisible')
                                                <span
                                                    class="text-red-500 dark:text-red-400 text-xs font-medium mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="profileVisible"
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] dark:peer-checked:bg-[#D4A537] transition-colors">
                                            </div>
                                            <div
                                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm">
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-end pt-4">
                                    <button type="submit" wire:loading.attr="disabled" wire:target="savePreferences"
                                        class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#165a3b] dark:hover:bg-[#E5B94A] transition shadow-sm disabled:opacity-60">
                                        <span wire:loading.remove wire:target="savePreferences">Save Preferences</span>
                                        <span wire:loading wire:target="savePreferences">Saving&hellip;</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            @endif

        </main>
    </div>
</div>