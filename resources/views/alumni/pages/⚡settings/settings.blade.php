<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== FB-STYLE COVER + PROFILE HEADER ========== --}}
    <div class="bg-white dark:bg-[#242526]">
        <div class="max-w-[1100px] mx-auto">

            {{-- Cover photo --}}
            <div class="h-48 sm:h-64 lg:h-72 bg-gradient-to-r from-[#123524] via-[#1C6B45] to-[#123524] relative overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]"></div>
                    <div class="absolute right-20 top-10 w-32 h-32 rounded-full bg-white/30"></div>
                    <div class="absolute right-60 -bottom-10 w-40 h-40 rounded-full bg-[#D4A537]"></div>
                </div>

                {{-- DARK / LIGHT MODE TOGGLE --}}
                <div class="absolute top-4 right-4">
                    <button type="button" class="hs-dark-mode-active:hidden block hs-dark-mode font-medium text-white rounded-full hover:bg-white/10 focus:outline-hidden focus:bg-white/10" data-hs-theme-click-value="dark">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        </span>
                    </button>
                    <button type="button" class="hs-dark-mode-active:block hidden hs-dark-mode font-medium text-white rounded-full hover:bg-white/10 focus:outline-hidden focus:bg-white/10" data-hs-theme-click-value="light">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Profile strip --}}
            <div class="px-4 pb-4 -mt-16 sm:-mt-20 relative">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-[#D4A537] ring-4 ring-white dark:ring-[#242526] flex items-center justify-center text-[#123524] font-bold text-5xl sm:text-6xl shadow-lg" style="font-family: 'Fraunces', serif;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="pb-2">
                            <h1 class="text-3xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">{{ Auth::user()->name }}</h1>
                            <p class="text-black/60 dark:text-white/60 text-sm mt-0.5">Settings &middot; Manage your account</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pb-2">
                        <span @class([
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' => Auth::user()->verification_status === 'verified',
                            'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400'    => Auth::user()->verification_status === 'pending',
                            'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400'        => Auth::user()->verification_status === 'rejected',
                        ])>
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ ucfirst(Auth::user()->verification_status) }}
                        </span>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="mt-6 border-t border-black/10 dark:border-white/10">
                    <ul class="flex items-center gap-1 overflow-x-auto">
                        <li><a href="{{ route('alumni.dashboard') }}" class="inline-block px-4 py-3 text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-white/5 rounded-t-lg transition">Timeline</a></li>
                        <li><a href="{{ route('alumni.profile') }}" class="inline-block px-4 py-3 text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-white/5 rounded-t-lg transition">About</a></li>
                        <li><a href="{{ route('alumni.message') }}" class="inline-block px-4 py-3 text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-white/5 rounded-t-lg transition">Posts</a></li>
                        <li><a href="#" class="inline-block px-4 py-3 text-sm font-semibold text-[#1877F2] border-b-[3px] border-[#1877F2] -mb-px">Settings</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TWO-COLUMN SETTINGS ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- ===== LEFT SIDEBAR (category menu, FB Account-Center style) ===== --}}
        <aside class="lg:col-span-4">
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24">

                <div class="px-4 pt-4 pb-2">
                    <h3 class="text-lg font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Settings</h3>
                </div>

                <div class="px-4 pb-2 pt-1">
                    <p class="text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-wide">Your Account</p>
                </div>
                <nav class="pb-2">
                    <button type="button" wire:click="setTab('profile')"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left transition
                            {{ $activeTab === 'profile' ? 'text-[#1877F2] bg-[#1877F2]/5 dark:bg-[#1877F2]/10' : 'text-black/70 dark:text-white/70 hover:bg-[#F0F2F5] dark:hover:bg-white/5' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        <span>
                            Personal details
                            <span class="block text-xs font-normal text-black/40 dark:text-white/40">Name, email, school ID</span>
                        </span>
                    </button>

                    <button type="button" wire:click="setTab('password')"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left transition
                            {{ $activeTab === 'password' ? 'text-[#1877F2] bg-[#1877F2]/5 dark:bg-[#1877F2]/10' : 'text-black/70 dark:text-white/70 hover:bg-[#F0F2F5] dark:hover:bg-white/5' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        <span>
                            Password and security
                            <span class="block text-xs font-normal text-black/40 dark:text-white/40">Change your password</span>
                        </span>
                    </button>
                </nav>

                <div class="px-4 pb-2 pt-1 border-t border-black/5 dark:border-white/10">
                    <p class="text-xs font-bold text-black/40 dark:text-white/40 uppercase tracking-wide mt-3">Preferences</p>
                </div>
                <nav class="pb-2">
                    <button type="button" wire:click="setTab('preferences')"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left transition
                            {{ $activeTab === 'preferences' ? 'text-[#1877F2] bg-[#1877F2]/5 dark:bg-[#1877F2]/10' : 'text-black/70 dark:text-white/70 hover:bg-[#F0F2F5] dark:hover:bg-white/5' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <span>
                            Notifications
                            <span class="block text-xs font-normal text-black/40 dark:text-white/40">Emails, events, visibility</span>
                        </span>
                    </button>
                </nav>

            </div>
        </aside>

        {{-- ===== RIGHT PANEL (only the active tab renders) ===== --}}
        <main class="lg:col-span-8">

            {{-- Personal details --}}
            @if ($activeTab === 'profile')
                <section class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
                    <h2 class="text-xl font-bold text-black dark:text-white mb-1" style="font-family: 'Fraunces', serif;">Personal details</h2>
                    <p class="text-sm text-black/60 dark:text-white/60 mb-5">Update your name and email address.</p>

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">Name</label>
                                <input type="text" wire:model.defer="name"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-[#F0F2F5] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] transition text-sm">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">Email</label>
                                <input type="email" wire:model.defer="email"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-[#F0F2F5] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] transition text-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">School ID</label>
                            <input type="text" value="{{ Auth::user()->school_id }}" disabled
                                class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 text-black/50 dark:text-white/40 cursor-not-allowed text-sm">
                            <p class="text-xs text-black/40 dark:text-white/40 mt-1">Contact the registrar if this needs correcting.</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit"
                                class="px-5 py-2 text-sm font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166FE5] transition">
                                Save changes
                            </button>
                        </div>
                    </form>

                    @if (session('profile_success'))
                        <div class="mt-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-3 text-sm">
                            {{ session('profile_success') }}
                        </div>
                    @endif
                </section>
            @endif

            {{-- Password --}}
            @if ($activeTab === 'password')
                <section class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
                    <h2 class="text-xl font-bold text-black dark:text-white mb-1" style="font-family: 'Fraunces', serif;">Password and security</h2>
                    <p class="text-sm text-black/60 dark:text-white/60 mb-5">Change your password to keep your account safe.</p>

                    <form wire:submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">Current password</label>
                            <input type="password" wire:model.defer="current_password"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-[#F0F2F5] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] transition text-sm">
                            @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">New password</label>
                            <input type="password" wire:model.defer="new_password"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-[#F0F2F5] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] transition text-sm">
                            @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-black/60 dark:text-white/60 font-semibold mb-1.5">Confirm new password</label>
                            <input type="password" wire:model.defer="new_password_confirmation"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-black/10 dark:border-white/10 bg-[#F0F2F5] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] transition text-sm">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit"
                                class="px-5 py-2 text-sm font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166FE5] transition">
                                Update password
                            </button>
                        </div>
                    </form>

                    @if (session('password_success'))
                        <div class="mt-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-3 text-sm">
                            {{ session('password_success') }}
                        </div>
                    @endif
                </section>
            @endif

            {{-- Preferences --}}
            @if ($activeTab === 'preferences')
                <section class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
                    <h2 class="text-xl font-bold text-black dark:text-white mb-1" style="font-family: 'Fraunces', serif;">Notifications &amp; visibility</h2>
                    <p class="text-sm text-black/60 dark:text-white/60 mb-5">Manage how you receive updates from the alumni network.</p>

                    <form wire:submit.prevent="savePreferences" class="space-y-3">
                        <label class="flex items-center justify-between bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:bg-[#E4E6EB] dark:hover:bg-white/10 transition">
                            <div>
                                <p class="font-semibold text-sm text-black dark:text-white">Email notifications</p>
                                <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Receive updates via email</p>
                            </div>
                            <div class="relative inline-flex items-center shrink-0 ml-3">
                                <input type="checkbox" wire:model.defer="emailNotifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1877F2] transition-colors"></div>
                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>

                        <label class="flex items-center justify-between bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:bg-[#E4E6EB] dark:hover:bg-white/10 transition">
                            <div>
                                <p class="font-semibold text-sm text-black dark:text-white">Event notifications</p>
                                <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Alumni event alerts</p>
                            </div>
                            <div class="relative inline-flex items-center shrink-0 ml-3">
                                <input type="checkbox" wire:model.defer="eventNotifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1877F2] transition-colors"></div>
                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>

                        <label class="flex items-center justify-between bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:bg-[#E4E6EB] dark:hover:bg-white/10 transition">
                            <div>
                                <p class="font-semibold text-sm text-black dark:text-white">Public profile</p>
                                <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">Visible to other alumni</p>
                            </div>
                            <div class="relative inline-flex items-center shrink-0 ml-3">
                                <input type="checkbox" wire:model.defer="profileVisible" class="sr-only peer">
                                <div class="w-11 h-6 bg-black/20 dark:bg-white/20 rounded-full peer peer-checked:bg-[#1877F2] transition-colors"></div>
                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit"
                                class="px-5 py-2 text-sm font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166FE5] transition">
                                Save preferences
                            </button>
                        </div>
                    </form>

                    @if (session('preferences_success'))
                        <div class="mt-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-3 text-sm">
                            {{ session('preferences_success') }}
                        </div>
                    @endif
                </section>
            @endif

        </main>

    </div>
</div>
