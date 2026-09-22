<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== HEADER BANNER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-tight"
                        style="font-family: 'Fraunces', serif;">Inbox</h1>
                    <p class="text-xs text-black/50 dark:text-white/50">
                        Messages from your department head or from the registrar
                    </p>
                </div>
            </div>

            {{-- Pending pill --}}
            @if ($this->pendingCount > 0)
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full
                            bg-[#1877F2]/10 text-[#1877F2] text-xs font-semibold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-[#1877F2] animate-pulse"></span>
                    {{ $this->pendingCount }} awaiting your reply
                </div>
            @endif
        </div>
    </div>

    {{-- ========== INBOX LIST ========== --}}
    <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- Search --}}
        <div class="relative">
            <input type="text" wire:model.live.debounce.400ms="search"
                placeholder="Search messages..."
                class="w-full pl-10 pr-4 py-3 text-sm rounded-2xl bg-white dark:bg-[#242526] border border-black/5 dark:border-white/5 shadow-sm focus:outline-none focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 transition">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-black/40 dark:text-white/40"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-4.34-4.34m0 0A8 8 0 1 0 5.34 5.34 8 8 0 0 0 16.66 16.66z" />
            </svg>
        </div>

        {{-- Filter tabs --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-1.5 flex items-center gap-1 overflow-x-auto">
            <button type="button" wire:click="setFilter('all')"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition
                    {{ $filter === 'all'
                        ? 'bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524]'
                        : 'text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]' }}">
                All
            </button>
            <button type="button" wire:click="setFilter('pending')"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition inline-flex items-center gap-1.5
                    {{ $filter === 'pending'
                        ? 'bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524]'
                        : 'text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]' }}">
                Awaiting
                @if ($this->pendingCount > 0)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold
                        {{ $filter === 'pending'
                            ? 'bg-white/20 dark:bg-[#123524]/20 text-white dark:text-[#123524]'
                            : 'bg-[#1877F2] text-white' }}">
                        {{ $this->pendingCount }}
                    </span>
                @endif
            </button>
            <button type="button" wire:click="setFilter('responded')"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition
                    {{ $filter === 'responded'
                        ? 'bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524]'
                        : 'text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]' }}">
                Responded
            </button>
        </div>

        {{-- Inbox list --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">
            @forelse ($this->messages as $event)
                @php
                    $author = $event->creator;
                    $rawAvatar = $author?->userProfile?->avatar;
                    $avatarUrl = $rawAvatar
                        ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                            ? $rawAvatar
                            : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                        : null;
                    $initial = strtoupper(substr($author?->name ?? '?', 0, 1));

                    $myResponse = $this->myRsvps[$event->id] ?? null;
                    $hasResponded = $myResponse !== null;

                    $badge = $hasResponded
                        ? match ($myResponse) {
                            'yes'   => ['bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400', 'Going'],
                            'maybe' => ['bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400', 'Maybe'],
                            'no'    => ['bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400', 'Not going'],
                            default => null,
                        }
                        : null;

                    $authorRole = $author?->roles->first()?->name;
                @endphp

                <a href="{{ route('alumni.view-single-message', $event) }}"
                    wire:key="msg-{{ $event->id }}"
                    wire:navigate
                    class="relative flex items-center gap-3 px-4 py-3.5 border-b border-black/5 dark:border-white/5 last:border-b-0 transition
                        {{ $hasResponded
                            ? 'hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C]'
                            : 'bg-[#1877F2]/[0.04] dark:bg-[#1877F2]/[0.07] hover:bg-[#1877F2]/[0.08] dark:hover:bg-[#1877F2]/[0.12]' }}">

                    {{-- Left accent for un-responded --}}
                    @unless ($hasResponded)
                        <span class="absolute left-0 top-0 bottom-0 w-1 bg-[#1877F2]"></span>
                    @endunless

                    {{-- Avatar --}}
                    <div class="relative shrink-0 ml-1">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $author?->name }}"
                                class="rounded-full object-cover bg-[#F0F2F5] dark:bg-[#3A3B3C]"
                                style="width: 52px; height: 52px;">
                        @else
                            <span
                                class="rounded-full bg-yellow-500 flex items-center justify-center text-[#0f2b1c] font-bold text-lg shrink-0"
                                style="width: 52px; height: 52px;">
                                {{ $initial }}
                            </span>
                        @endif

                        {{-- Blue dot on avatar when unread --}}
                        @unless ($hasResponded)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-[#1877F2] border-2 border-white dark:border-[#242526]"
                                title="Awaiting your response"></span>
                        @endunless
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-black dark:text-white truncate">
                                {{ $author?->name ?? 'CSAV Admin' }}
                                @if ($authorRole)
                                    <span class="ml-1 inline-flex items-center text-[10px] px-1.5 py-0.5 rounded bg-black/5 dark:bg-white/10 text-black/60 dark:text-white/60 font-semibold uppercase tracking-wide align-middle">
                                        {{ $authorRole }}
                                    </span>
                                @endif
                            </p>
                            <span class="text-[11px] shrink-0
                                {{ $hasResponded
                                    ? 'text-black/50 dark:text-white/50'
                                    : 'text-[#1877F2] font-semibold' }}">
                                {{ $event->created_at->diffForHumans(null, true, true) }}
                            </span>
                        </div>

                        <p class="text-sm mt-0.5 truncate
                            {{ $hasResponded
                                ? 'font-bold text-black dark:text-white'
                                : 'font-extrabold text-black dark:text-white' }}"
                            style="font-family: 'Fraunces', serif;">
                            {{ $event->title }}
                        </p>

                        <div class="flex items-center justify-between gap-2 mt-1">
                            <p class="text-xs truncate
                                {{ $hasResponded
                                    ? 'text-black/60 dark:text-white/60'
                                    : 'text-black/70 dark:text-white/70' }}">
                                {{ \Illuminate\Support\Str::limit(strip_tags($event->description ?? 'No description'), 80) }}
                            </p>

                            @if ($badge)
                                <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide {{ $badge[0] }}">
                                    {{ $badge[1] }}
                                </span>
                            @else
                                <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide
                                            bg-[#1877F2] text-white">
                                    New
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center">
                        <svg class="w-8 h-8 text-black/40 dark:text-white/40" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                        </svg>
                    </div>
                    <p class="font-bold text-black dark:text-white text-lg" style="font-family: 'Fraunces', serif;">
                        @if ($search)
                            No messages match your search
                        @elseif ($filter === 'pending')
                            You're all caught up!
                        @elseif ($filter === 'responded')
                            You haven't responded to any messages yet
                        @else
                            No messages yet
                        @endif
                    </p>
                    <p class="text-black/50 dark:text-white/50 text-sm mt-1 max-w-xs mx-auto">
                        @if ($search)
                            Try a different keyword.
                        @elseif ($filter === 'pending')
                            You've responded to every message so far.
                        @else
                            Messages from the registrar and your department head will appear here.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($this->messages->hasPages())
            <div class="flex justify-center pt-2">
                {{ $this->messages->links() }}
            </div>
        @endif
    </div>
</div>