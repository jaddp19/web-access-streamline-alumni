<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== HEADER BANNER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-tight"
                        style="font-family: 'Fraunces', serif;">Inbox</h1>
                    <p class="text-xs text-black/50 dark:text-white/50">Messages from your department head or from the registrar</p>
                </div>
            </div>
        </div>
    </div>

    @php
        // ===== STATIC CONVERSATIONS =====
        $conversations = [
            [
                'id' => 1,
                'name' => 'Maria Santos',
                'avatar' => 'https://i.pravatar.cc/150?img=47',
                'last_message' => 'See you at the reunion! 🎉',
                'time' => '2m',
                'unread' => 2,
                'online' => true,
            ],
            [
                'id' => 2,
                'name' => 'Juan Dela Cruz',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'last_message' => 'Thanks for the career tips!',
                'time' => '15m',
                'unread' => 1,
                'online' => true,
            ],
            [
                'id' => 3,
                'name' => 'Ana Reyes',
                'avatar' => 'https://i.pravatar.cc/150?img=45',
                'last_message' => 'You: I\'ll send the photos later',
                'time' => '1h',
                'unread' => 0,
                'online' => false,
            ],
            [
                'id' => 4,
                'name' => 'Pedro Bautista',
                'avatar' => 'https://i.pravatar.cc/150?img=33',
                'last_message' => 'Congrats on the new job!',
                'time' => '3h',
                'unread' => 0,
                'online' => false,
            ],
            [
                'id' => 5,
                'name' => 'CSAV Alumni Office',
                'avatar' => 'https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180',
                'last_message' => 'Reminder: Homecoming 2026 registration is open',
                'time' => '1d',
                'unread' => 0,
                'online' => false,
            ],
            [
                'id' => 6,
                'name' => 'Liza Mercado',
                'avatar' => 'https://i.pravatar.cc/150?img=49',
                'last_message' => 'Let\'s catch up soon!',
                'time' => '2d',
                'unread' => 0,
                'online' => false,
            ],
            [
                'id' => 7,
                'name' => 'Mark Villanueva',
                'avatar' => 'https://i.pravatar.cc/150?img=15',
                'last_message' => 'You: Sure, sounds good 👍',
                'time' => '3d',
                'unread' => 0,
                'online' => false,
            ],
            [
                'id' => 8,
                'name' => 'Rachel Lim',
                'avatar' => 'https://i.pravatar.cc/150?img=44',
                'last_message' => 'Sent you the file 📎',
                'time' => '5d',
                'unread' => 0,
                'online' => false,
            ],
        ];
    @endphp

    {{-- ========== INBOX LIST ========== --}}
    <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- Search --}}
        <div class="relative">
            <input type="text" placeholder="Search messages..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-2xl bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 transition">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-black/40 dark:text-white/40"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
            </svg>
        </div>

        {{-- Inbox list --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
            @foreach ($conversations as $conv)
                <a href="#"
                    class="flex items-center gap-3 px-4 py-3.5 border-b border-black/5 dark:border-white/5 last:border-b-0 transition
                        hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]">

                    {{-- Avatar with online dot --}}
                    <div class="relative shrink-0">
                        <img src="{{ $conv['avatar'] }}" alt="{{ $conv['name'] }}"
                            class="w-13 h-13 rounded-full object-cover bg-[#F0F2F5] dark:bg-[#3A3B3C]"
                            style="width: 52px; height: 52px;">
                        @if ($conv['online'])
                            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-[#242526]"></span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-black dark:text-white truncate">
                                {{ $conv['name'] }}
                            </p>
                            <span class="text-[11px] text-black/50 dark:text-white/50 shrink-0">
                                {{ $conv['time'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 mt-1">
                            <p class="text-xs truncate
                                {{ $conv['unread'] > 0
                                    ? 'font-semibold text-black dark:text-white'
                                    : 'text-black/60 dark:text-white/60' }}">
                                {{ $conv['last_message'] }}
                            </p>
                            @if ($conv['unread'] > 0)
                                <span class="shrink-0 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-[#1877F2] text-white text-[10px] font-bold">
                                    {{ $conv['unread'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</div>