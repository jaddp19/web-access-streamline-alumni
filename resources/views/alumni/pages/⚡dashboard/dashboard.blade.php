<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== THREE-COLUMN FEED LAYOUT (FB HOME STYLE) ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="lg:col-span-3 space-y-4">
            {{-- Profile quick card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24 border border-transparent dark:border-white/5">
                <div class="h-16 bg-gradient-to-r from-[#123524] to-[#1C6B45]"></div>
                <div class="px-4 pb-4 text-center -mt-10">
                    <img src="{{ $this->avatarUrl }}" alt="{{ $this->alumni->name }}"
                        class="w-20 h-20 mx-auto rounded-full object-cover ring-4 ring-white dark:ring-[#242526] bg-[#D4A537]">
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
                    </div>
                </div>
            </div>
        </aside>

        {{-- ===== MAIN FEED COLUMN ===== --}}
        <main class="lg:col-span-6 space-y-4">

            {{-- Create post (FB home composer) --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4 border border-transparent dark:border-white/5">
                <div class="flex items-center gap-3">
                    <img src="{{ $this->avatarUrl }}" alt="{{ $this->alumni->name }}"
                        class="w-10 h-10 rounded-full object-cover shrink-0 bg-[#D4A537]">
                    <a href="{{ route('alumni.message') }}" class="flex-1 text-left px-4 py-2.5 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-black/50 dark:text-white/60 text-sm transition">
                        What's on your mind, {{ explode(' ', $this->alumni->name)[0] }}?
                    </a>
                </div>
            </div>

            {{-- Filter row --}}
            <div class="flex items-center justify-between px-2">
                <h2 class="text-sm font-semibold text-black/60 dark:text-white/60 uppercase tracking-wide">Recent posts from alumni</h2>
                <a href="{{ route('alumni.message') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">See all</a>
            </div>

            {{-- Feed: load recent posts inline --}}
            @php
                $recentPosts = \App\Models\Post::with('user.userProfile')->latest()->take(3)->get();
            @endphp

            @forelse ($recentPosts as $post)
                <article class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
                    <header class="flex items-center justify-between p-4 pb-2">
                        <div class="flex items-center gap-3">
                            @if ($post->user?->userProfile?->avatar)
                                <img src="{{ Storage::url($post->user->userProfile->avatar) }}" alt="{{ $post->user->name }}"
                                    class="w-10 h-10 rounded-full object-cover shrink-0 bg-[#D4A537]">
                            @else
                                <div class="w-10 h-10 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold shrink-0">
                                    {{ strtoupper(substr($post->user->name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-black dark:text-white text-sm leading-tight">{{ $post->user->name ?? 'Unknown Alumni' }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50">{{ $post->created_at->diffForHumans() }} &middot; {{ ucfirst($post->status) }}</p>
                            </div>
                        </div>
                    </header>
                    <div class="px-4 pb-3">
                        <p class="font-bold text-black dark:text-white text-[15px]" style="font-family: 'Fraunces', serif;">{{ $post->title }}</p>
                    </div>
                    @if ($post->image)
                        <div class="bg-black">
                            <img src="{{ Storage::url($post->image) }}" alt="Post image" class="w-full max-h-[400px] object-contain">
                        </div>
                    @endif
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

            @php
                $totalAlumni  = \App\Models\User::role('alumni')->count();
                $totalBatches = \App\Models\Batch::count();
                $myBatchName  = $this->userProfile?->batch?->batch_name;
            @endphp

            {{-- Alumni at a glance (replaces redundant Quick Links) --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
                <div class="px-4 py-3 border-b border-black/5 dark:border-white/5">
                    <h3 class="text-sm font-bold text-black/60 dark:text-white/60 uppercase tracking-wide">Alumni at a glance</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-black dark:text-white leading-none">{{ number_format($totalAlumni) }}</p>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">Registered alumni</p>
                        </div>
                    </div>

                    @if ($myBatchName)
                        <div class="flex items-center gap-3 pt-3 border-t border-black/5 dark:border-white/5">
                            <div class="w-9 h-9 rounded-lg bg-[#123524]/10 flex items-center justify-center text-[#123524] shrink-0">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-black dark:text-white leading-tight">Class of {{ $myBatchName }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">Your batch</p>
                            </div>
                        </div>
                    @endif
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