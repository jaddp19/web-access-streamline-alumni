<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== HEADER BANNER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-tight" style="font-family: 'Fraunces', serif;">Notifications</h1>
                    <p class="text-xs text-black/50 dark:text-white/50">Stay updated with your community</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <div class="px-4 py-1.5 rounded-full bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] text-xs font-semibold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    3 unread
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TWO-COLUMN LAYOUT ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="lg:col-span-3 space-y-4">
            {{-- Profile quick card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24 border border-transparent dark:border-white/5">
                <div class="h-16 bg-gradient-to-r from-[#123524] to-[#1C6B45]"></div>
                <div class="px-4 pb-4 text-center -mt-10">
                    <span class="w-20 h-20 mx-auto flex items-center justify-center text-3xl font-bold text-[#0f2b1c] bg-yellow-500 rounded-full ring-4 ring-white dark:ring-[#242526] shadow-md shrink-0">
                        {{ strtoupper(substr($this->alumni->name ?? '?', 0, 1)) }}
                    </span>
                    <p class="font-bold text-black dark:text-white mt-2" style="font-family: 'Fraunces', serif;">{{ $this->alumni->name }}</p>
                    <p class="text-xs text-black/50 dark:text-white/50">Alumni Member</p>
                </div>
            </div>
        </aside>

        {{-- ===== MAIN COLUMN — NOTIFICATIONS ===== --}}
        <main class="lg:col-span-9 space-y-4">

            {{-- Filter tabs --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-2 flex items-center gap-1 overflow-x-auto">
                <button type="button" class="shrink-0 px-4 py-2 rounded-xl bg-[#123524] text-white text-sm font-semibold transition">
                    All
                </button>
                <button type="button" class="shrink-0 px-4 py-2 rounded-xl text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] text-sm font-semibold transition">
                    Unread
                </button>
                <button type="button" class="shrink-0 px-4 py-2 rounded-xl text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] text-sm font-semibold transition">
                    Mentions
                </button>
                <button type="button" class="shrink-0 px-4 py-2 rounded-xl text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] text-sm font-semibold transition">
                    System
                </button>
            </div>

            @php
                // ===== STATIC SAMPLE DATA =====
                // Swap with a real DB query later
                $notifications = [
                    [
                        'id' => 1,
                        'type' => 'like',
                        'actor' => 'Maria Santos',
                        'message' => 'liked your post "Batch 2024 Reunion Photos"',
                        'time' => '2 minutes ago',
                        'read' => false,
                        'link' => '#',
                    ],
                    [
                        'id' => 2,
                        'type' => 'comment',
                        'actor' => 'Juan Dela Cruz',
                        'message' => 'commented on your post: "Great memories! Miss everyone."',
                        'time' => '15 minutes ago',
                        'read' => false,
                        'link' => '#',
                    ],
                    [
                        'id' => 3,
                        'type' => 'event',
                        'actor' => 'CSAV Alumni Office',
                        'message' => 'posted a new event: "Annual Alumni Homecoming 2026"',
                        'time' => '1 hour ago',
                        'read' => false,
                        'link' => '#',
                    ],
                    [
                        'id' => 4,
                        'type' => 'follow',
                        'actor' => 'Ana Reyes',
                        'message' => 'started following you',
                        'time' => '3 hours ago',
                        'read' => true,
                        'link' => '#',
                    ],
                    [
                        'id' => 5,
                        'type' => 'system',
                        'actor' => 'System',
                        'message' => 'Your profile has been verified by the registrar.',
                        'time' => '1 day ago',
                        'read' => true,
                        'link' => '#',
                    ],
                    [
                        'id' => 6,
                        'type' => 'mention',
                        'actor' => 'Pedro Bautista',
                        'message' => 'mentioned you in a comment on "Career Opportunities Abroad"',
                        'time' => '2 days ago',
                        'read' => true,
                        'link' => '#',
                    ],
                    [
                        'id' => 7,
                        'type' => 'event',
                        'actor' => 'CSAV Alumni Office',
                        'message' => 'reminder: "Career Talk with Batch 2015" starts tomorrow at 9:00 AM',
                        'time' => '3 days ago',
                        'read' => true,
                        'link' => '#',
                    ],
                ];

                // Icon + color mapping per notification type
                $iconMap = [
                    'like'    => ['bg' => 'bg-[#1877F2]/10', 'text' => 'text-[#1877F2]', 'path' => 'M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V2.75a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z'],
                    'comment' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-600', 'path' => 'M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z'],
                    'event'   => ['bg' => 'bg-[#D4A537]/15', 'text' => 'text-[#a97f1f]', 'path' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                    'follow'  => ['bg' => 'bg-purple-500/10', 'text' => 'text-purple-600', 'path' => 'M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z'],
                    'mention' => ['bg' => 'bg-orange-500/10', 'text' => 'text-orange-600', 'path' => 'M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25'],
                    'system'  => ['bg' => 'bg-[#123524]/10', 'text' => 'text-[#123524]', 'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp

            {{-- Notifications list --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden">

                @foreach ($notifications as $notification)
                    @php $icon = $iconMap[$notification['type']]; @endphp

                    <a href="{{ $notification['link'] }}"
                       class="flex items-start gap-3 px-4 py-4 border-b border-black/5 dark:border-white/5 last:border-b-0 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]/50 transition
                              {{ $notification['read'] ? '' : 'bg-[#1877F2]/[0.03] dark:bg-[#1877F2]/[0.06]' }}">

                        {{-- Type icon --}}
                        <div class="w-11 h-11 rounded-full {{ $icon['bg'] }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 {{ $icon['text'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon['path'] }}" />
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-black dark:text-white leading-snug">
                                <span class="font-semibold">{{ $notification['actor'] }}</span>
                                <span class="text-black/70 dark:text-white/70"> {{ $notification['message'] }}</span>
                            </p>
                            <p class="text-xs text-black/50 dark:text-white/50 mt-1">{{ $notification['time'] }}</p>
                        </div>

                        {{-- Unread dot --}}
                        @unless ($notification['read'])
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1877F2] shrink-0 mt-2" title="Unread"></span>
                        @endunless
                    </a>
                @endforeach

            </div>

            {{-- "Load more" (static placeholder) --}}
            <div class="flex justify-center pt-2">
                <button type="button"
                    class="px-5 py-2.5 rounded-xl bg-white dark:bg-[#242526] shadow-sm text-sm font-semibold text-black/70 dark:text-white/70 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                    Load older notifications
                </button>
            </div>

        </main>
    </div>
</div>