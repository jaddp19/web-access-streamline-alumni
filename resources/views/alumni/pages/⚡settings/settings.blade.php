<div class="bg-[#F8FAFC] dark:bg-[#111827] min-h-screen">

    {{-- ========== TOP NAVIGATION BAR ========== --}}
    <header class="bg-white dark:bg-[#1F2937] border-b border-black/5 dark:border-white/5 sticky top-0 z-30">
        <div class="max-w-[1200px] mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-none" style="font-family: 'Fraunces', serif;">Account Settings</h1>
                    <p class="text-xs text-black/50 dark:text-white/50 mt-1">Manage your alumni portal preferences</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" class="hs-dark-mode-active:hidden block hs-dark-mode p-2 text-black/60 dark:text-white/60 rounded-lg hover:bg-[#F0F2F5] dark:hover:bg-white/5 transition" data-hs-theme-click-value="dark">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
                <button type="button" class="hs-dark-mode-active:block hidden hs-dark-mode p-2 text-black/60 dark:text-white/60 rounded-lg hover:bg-[#F0F2F5] dark:hover:bg-white/5 transition" data-hs-theme-click-value="light">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- ========== MAIN SETTINGS LAYOUT ========== --}}
    <div class="max-w-[1200px] mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ===== LEFT SIDEBAR (Settings Menu) ===== --}}
        <aside class="lg:col-span-3">
            <nav class="space-y-1 sticky top-24">
                <p class="px-3 mb-2 text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">General</p>

                <button type="button" wire:click="setTab('profile')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'profile' ? 'text-[#1C6B45] bg-[#1C6B45]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    Personal Details
                </button>

                <button type="button" wire:click="setTab('password')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'password' ? 'text-[#1C6B45] bg-[#1C6B45]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    Security
                </button>

                <div class="my-4 border-t border-black/10 dark:border-white/10"></div>

                <p class="px-3 mb-2 text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">Privacy</p>

                <button type="button" wire:click="setTab('preferences')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-left rounded-lg transition
                        {{ $activeTab === 'preferences' ? 'text-[#1C6B45] bg-[#1C6B45]/10' : 'text-black/60 dark:text-white/60 hover:bg-white dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    Network Preferences
                </button>
            </nav>
        </aside>

        {{-- ===== CONTENT PANEL ===== --}}
        <main class="lg:col-span-9">

            {{-- Personal details --}}
            @if ($activeTab === 'profile')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Personal Details</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Update your basic account information.</p>
                        </div>
                    </div>

                    <section class="bg-white dark:bg-[#1F2937] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6">
                        <form wire:submit.prevent="updateProfile" class="space-y-6">
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">Full Name</label>
                                    <input type="text" wire:model.defer="name"
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-[#111827] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] focus:ring-1 focus:ring-[#1C6B45] transition text-sm">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">Email Address</label>
                                    <input type="email" wire:model.defer="email"
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-[#111827] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] focus:ring-1 focus:ring-[#1C6B45] transition text-sm">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">Graduation Batch</label>
                                <input type="text" value="{{ $this->userProfile?->batch?->batch_name ?? 'Not set' }}" disabled
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-black/50 dark:text-white/40 cursor-not-allowed text-sm">
                                <p class="text-xs text-black/40 dark:text-white/40">Verified by the registrar.</p>
                            </div>

                            <div class="flex items-center justify-end pt-4">
                                <button type="submit"
                                    class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] text-white hover:bg-[#165a3b] transition shadow-sm">
                                    Save Changes
                                </button>
                            </div>
                        </form>

                        @if (session('profile_success'))
                            <div class="mt-6 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                {{ session('profile_success') }}
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Password --}}
            @if ($activeTab === 'password')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Security</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Manage your account password.</p>
                        </div>
                    </div>

                    <section class="bg-white dark:bg-[#1F2937] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-6">
                        <form wire:submit.prevent="updatePassword" class="space-y-6">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">Current Password</label>
                                <input type="password" wire:model.defer="current_password"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-[#111827] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] focus:ring-1 focus:ring-[#1C6B45] transition text-sm">
                                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">New Password</label>
                                    <input type="password" wire:model.defer="new_password"
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-[#111827] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] focus:ring-1 focus:ring-[#1C6B45] transition text-sm">
                                    @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wider">Confirm New Password</label>
                                    <input type="password" wire:model.defer="new_password_confirmation"
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-[#111827] text-black dark:text-white focus:outline-none focus:border-[#1C6B45] focus:ring-1 focus:ring-[#1C6B45] transition text-sm">
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-4">
                                <button type="submit"
                                    class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] text-white hover:bg-[#165a3b] transition shadow-sm">
                                    Update Password
                                </button>
                            </div>
                        </form>

                        @if (session('password_success'))
                            <div class="mt-6 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                {{ session('password_success') }}
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Preferences --}}
            @if ($activeTab === 'preferences')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Network Preferences</h2>
                            <p class="text-sm text-black/60 dark:text-white/60">Control your visibility and notifications.</p>
                        </div>
                    </div>

                    <section class="bg-white dark:bg-[#1F2937] rounded-2xl shadow-sm border border-black/5 dark:border-white/5 overflow-hidden">
                        <div class="p-6">
                            <form wire:submit.prevent="savePreferences" class="space-y-4">
                                <div class="space-y-3">
                                    <label class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#111827] border border-black/5 dark:border-white/10 rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group">
                                        <div class="flex flex-col">
                                            <p class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] transition">Email Notifications</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Stay updated via email.</p>
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="emailNotifications" class="sr-only peer">
                                            <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                        </div>
                                    </label>

                                    <label class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#111827] border border-black/5 dark:border-white/10 rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group">
                                        <div class="flex flex-col">
                                            <p class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] transition">Event Alerts</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Receive instant alumni gathering alerts.</p>
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="eventNotifications" class="sr-only peer">
                                            <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                        </div>
                                    </label>

                                    <label class="flex items-center justify-between bg-[#F8FAFC] dark:bg-[#111827] border border-black/5 dark:border-white/10 rounded-xl px-4 py-4 cursor-pointer hover:bg-white dark:hover:bg-white/5 transition group">
                                        <div class="flex flex-col">
                                            <p class="font-semibold text-sm text-black dark:text-white group-hover:text-[#1C6B45] transition">Public Profile Visibility</p>
                                            <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Allow other alumni to find your profile.</p>
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 ml-3">
                                            <input type="checkbox" wire:model.defer="profileVisible" class="sr-only peer">
                                            <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                        </div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-end pt-4">
                                    <button type="submit"
                                        class="px-6 py-2 text-sm font-bold rounded-lg bg-[#1C6B45] text-white hover:bg-[#165a3b] transition shadow-sm">
                                        Save Preferences
                                    </button>
                                </div>
                            </form>

                            @if (session('preferences_success'))
                                <div class="mt-6 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm flex items-center gap-3">
                                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    {{ session('preferences_success') }}
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            @endif

        </main>

    </div>
</div>
