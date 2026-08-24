<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== TWO-COLUMN FEED LAYOUT (FB HOME STYLE) ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="lg:col-span-3 space-y-4">
            {{-- Profile quick card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24 border border-transparent dark:border-white/5">
                <div class="h-16 bg-gradient-to-r from-[#123524] to-[#1C6B45]"></div>
                <div class="px-4 pb-4 text-center -mt-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-[#D4A537] ring-4 ring-white dark:ring-[#242526] flex items-center justify-center text-[#123524] font-bold text-3xl" style="font-family: 'Fraunces', serif;">
                        {{ strtoupper(substr($this->alumni->name, 0, 1)) }}
                    </div>
                    <p class="font-bold text-black dark:text-white mt-2" style="font-family: 'Fraunces', serif;">{{ $this->alumni->name }}</p>
                    <p class="text-xs text-black/50 dark:text-white/50">Alumni Member</p>

                    <div class="border-t border-black/5 dark:border-white/5 mt-4 pt-3 text-left">
                        <a href="{{ route('alumni.profile') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#1877F2] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                My Profile
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                        <a href="{{ route('alumni.profile.update', $this->alumni->id) }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#1877F2] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                Edit Profile
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                        <a href="{{ route('alumni.message') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#1877F2] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                                Messages
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                        <a href="{{ route('alumni.settings') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#1877F2] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Settings
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ===== MAIN FEED COLUMN ===== --}}
        <main class="lg:col-span-6 space-y-4">

            {{-- Create post (FB home composer) --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4 border border-transparent dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold shrink-0">
                        {{ strtoupper(substr($this->alumni->name, 0, 1)) }}
                    </div>
                    <a href="{{ route('alumni.message') }}" class="flex-1 text-left px-4 py-2.5 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-black/50 dark:text-white/60 text-sm transition">
                        What's on your mind, {{ explode(' ', $this->alumni->name)[0] }}?
                    </a>
                </div>
            </div>

            {{-- Filter row (FB "Stories" / divider) --}}
            <div class="flex items-center justify-between px-2">
                <h2 class="text-sm font-semibold text-black/60 dark:text-white/60 uppercase tracking-wide">Recent posts from alumni</h2>
                <a href="{{ route('alumni.message') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">See all</a>
            </div>

            {{-- Feed: load recent posts inline --}}
            @php
                $recentPosts = \App\Models\Post::with(['user', 'comments.user'])->withCount('comments')->latest()->take(3)->get();
            @endphp

            @forelse ($recentPosts as $post)
                <article class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
                    <header class="flex items-center justify-between p-4 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold shrink-0">
                                {{ strtoupper(substr($post->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-black dark:text-white text-sm leading-tight">{{ $post->user->name ?? 'Unknown Alumni' }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50">{{ $post->created_at->diffForHumans() }} &middot; Public</p>
                            </div>
                        </div>
                    </header>
                    <div class="px-4 pb-3">
                        <p class="font-bold text-black dark:text-white text-[15px]" style="font-family: 'Fraunces', serif;">{{ $post->title }}</p>
                        @if ($post->description)
                            <p class="text-black/80 dark:text-white/80 text-sm whitespace-pre-line mt-1 leading-relaxed">{{ Str::limit($post->description, 180) }}</p>
                        @endif
                    </div>
                    @if ($post->image)
                        <div class="bg-black">
                            <img src="{{ Storage::url($post->image) }}" alt="Post image" class="w-full max-h-[400px] object-contain">
                        </div>
                    @endif
                    <div class="border-t border-black/5 dark:border-white/5 px-4 py-2 text-xs text-black/50 dark:text-white/50">
                        {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                    </div>
                    <div class="border-t border-black/5 dark:border-white/5 px-2 py-1 flex items-center justify-around">
                        <a href="{{ route('alumni.message') }}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                            Comment
                        </a>
                    </div>
                </article>
            @empty
                <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-12 text-center border border-transparent dark:border-white/5">
                    <div class="w-16 h-16 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-black/40 dark:text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                    </div>
                    <p class="font-bold text-black dark:text-white text-lg" style="font-family: 'Fraunces', serif;">No posts yet</p>
                    <p class="text-black/50 dark:text-white/50 text-sm mt-1">Be the first to share something with your fellow alumni.</p>
                    <a href="{{ route('alumni.message') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#1877F2] text-white text-sm font-semibold hover:bg-[#166FE5] transition">
                        Create the first post
                    </a>
                </div>
            @endforelse
        </main>

        {{-- ===== RIGHT SIDEBAR ===== --}}
        <aside class="lg:col-span-3 space-y-4">
            {{-- Account status / completion --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4 sticky top-24 border border-transparent dark:border-white/5">
                <h3 class="text-sm font-bold text-black/60 dark:text-white/60 uppercase tracking-wide mb-3">Your account</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-black/60 dark:text-white/60 font-semibold">Status</span>
                        <span @class([
                            'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $this->alumni->verification_status === 'verified',
                            'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'    => $this->alumni->verification_status === 'pending',
                            'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'        => $this->alumni->verification_status === 'rejected',
                        ])>
                            {{ ucfirst($this->alumni->verification_status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-black/60 dark:text-white/60 font-semibold">Profile</span>
                        <span class="text-sm font-bold text-black dark:text-white">{{ $this->profileCompletion['percent'] }}%</span>
                    </div>
                    <div class="w-full h-1.5 bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-full overflow-hidden">
                        <div class="h-full bg-[#1877F2] rounded-full transition-all" style="width: {{ $this->profileCompletion['percent'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-black/60 dark:text-white/60 font-semibold">School ID</span>
                        <span class="text-xs font-mono font-semibold text-black dark:text-white">{{ $this->alumni->school_id }}</span>
                    </div>
                </div>

                @if ($this->profileCompletion['percent'] < 100)
                    <a href="{{ route('alumni.profile.update', $this->alumni->id) }}" class="mt-4 block w-full text-center py-2 rounded-lg bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-sm font-semibold text-black dark:text-white transition">
                        Finish setup
                    </a>
                @endif
            </div>

            {{-- Quick links --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
                <div class="px-4 py-3 border-b border-black/5 dark:border-white/5">
                    <h3 class="text-sm font-bold text-black/60 dark:text-white/60 uppercase tracking-wide">Quick links</h3>
                </div>
                <div class="py-2">
                    <a href="{{ route('alumni.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                        <svg class="w-5 h-5 text-[#1877F2]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        My Profile
                    </a>
                    <a href="{{ route('alumni.message') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                        <svg class="w-5 h-5 text-[#1877F2]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                        Messages
                    </a>
                    <a href="{{ route('alumni.settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                        <svg class="w-5 h-5 text-[#1877F2]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Settings
                    </a>
                </div>
            </div>

            {{-- School branding --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4 text-center border border-transparent dark:border-white/5">
                <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
                     alt="School Logo"
                     class="w-12 h-12 rounded-full ring-2 ring-[#D4A537]/50 mx-auto object-cover">
                <p class="font-bold text-black dark:text-white text-sm mt-2" style="font-family: 'Fraunces', serif;">Colegio de Sta. Ana de Victorias</p>
                <p class="text-[11px] text-black/40 dark:text-white/40 mt-0.5">Alumni Network</p>
            </div>
        </aside>

    </div>
</div>
