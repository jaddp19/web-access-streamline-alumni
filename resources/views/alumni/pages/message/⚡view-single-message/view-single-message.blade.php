<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== HEADER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-4 flex items-center gap-3">
            <a href="{{ route('alumni.message') }}" wire:navigate
                class="w-9 h-9 rounded-full flex items-center justify-center
                       text-black/60 dark:text-white/60 hover:bg-black/5 dark:hover:bg-white/5 transition shrink-0"
                aria-label="Back to inbox">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-black dark:text-white leading-tight truncate"
                    style="font-family: 'Fraunces', serif;">
                    {{ $this->event->title }}
                </h1>
                <p class="text-xs text-black/50 dark:text-white/50">
                    {{ $this->creator?->name ?? 'CSAV Admin' }}
                    @if ($this->creator?->roles->first()?->name)
                        · <span class="capitalize">{{ $this->creator->roles->first()->name }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- Flash --}}
        @if (session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20
                        text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20
                        text-red-700 dark:text-red-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- ========== MESSAGE CARD ========== --}}
        <article class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">

            {{-- Author header --}}
            @php
                $rawAvatar = $this->creator?->userProfile?->avatar;
                $avatarUrl = $rawAvatar
                    ? (filter_var($rawAvatar, FILTER_VALIDATE_URL)
                        ? $rawAvatar
                        : \Illuminate\Support\Facades\Storage::url($rawAvatar))
                    : null;
                $initial = strtoupper(substr($this->creator?->name ?? '?', 0, 1));
            @endphp

            <header class="flex items-center gap-3 p-4 pb-3 border-b border-black/5 dark:border-white/5">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $this->creator?->name }}"
                        class="w-11 h-11 rounded-full object-cover shrink-0 bg-[#D4A537]">
                @else
                    <span
                        class="w-11 h-11 rounded-full bg-yellow-500 flex items-center justify-center text-[#0f2b1c] font-bold text-lg shrink-0">
                        {{ $initial }}
                    </span>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-black dark:text-white text-sm leading-tight truncate">
                        {{ $this->creator?->name ?? 'CSAV Admin' }}
                    </p>
                    <p class="text-xs text-black/50 dark:text-white/50">
                        {{ $this->event->created_at->format('M d, Y · g:i A') }}
                    </p>
                </div>

                <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15
                             text-[#123524] dark:text-[#D4A537] font-semibold uppercase tracking-wide shrink-0">
                    Official
                </span>
            </header>

            {{-- Event details --}}
            <div class="px-4 py-4 space-y-4">

                <div>
                    <p class="font-bold text-black dark:text-white text-lg"
                        style="font-family: 'Fraunces', serif;">
                        {{ $this->event->title }}
                    </p>
                    @if ($this->event->description)
                        <p class="text-sm text-black/70 dark:text-white/70 mt-2 leading-relaxed whitespace-pre-line">
                            {{ $this->event->description }}
                        </p>
                    @endif
                </div>

                {{-- Cover image --}}
                @if ($this->event->image)
                    <div class="rounded-xl overflow-hidden border border-black/5 dark:border-white/5 bg-black">
                        <img src="{{ $this->event->image_url }}" alt="{{ $this->event->title }}"
                            class="w-full max-h-[400px] object-contain" loading="lazy">
                    </div>
                @endif

                {{-- Meta grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-black/5 dark:border-white/5">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                Starts
                            </p>
                            <p class="text-sm text-black dark:text-white font-medium">
                                {{ $this->event->starts_at->format('M d, Y · g:i A') }}
                            </p>
                            @if ($this->event->ends_at)
                                <p class="text-xs text-black/50 dark:text-white/50">
                                    Ends {{ $this->event->ends_at->format('M d, Y · g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($this->event->location)
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#D4A537]/15 flex items-center justify-center text-[#a97f1f] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                    Location
                                </p>
                                <p class="text-sm text-black dark:text-white font-medium">
                                    {{ $this->event->location }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($this->event->registration_deadline)
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                    RSVP deadline
                                </p>
                                <p class="text-sm text-black dark:text-white font-medium">
                                    {{ $this->event->registration_deadline->format('M d, Y · g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($this->event->capacity)
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">
                                    Attendees
                                </p>
                                <p class="text-sm text-black dark:text-white font-medium">
                                    {{ $this->attendeeCount }} / {{ $this->event->capacity }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ========== RSVP SECTION ========== --}}
            <div class="px-4 py-4 bg-[#F0F2F5] dark:bg-[#1a1b1c] border-t border-black/5 dark:border-white/5">
                <p class="text-xs font-semibold text-black/60 dark:text-white/60 uppercase tracking-wide mb-3">
                    Your response
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    {{-- Yes --}}
                    <button type="button" wire:click="rsvp('yes')"
                        @disabled($this->event->isFull() && $this->response !== 'yes')
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold transition
                            disabled:opacity-40 disabled:cursor-not-allowed
                            {{ $this->response === 'yes'
                                ? 'bg-emerald-500 text-white shadow-sm'
                                : 'bg-white dark:bg-[#242526] text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        @if ($this->response === 'yes') Going @else I'll go @endif
                    </button>

                    {{-- Maybe --}}
                    <button type="button" wire:click="rsvp('maybe')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ $this->response === 'maybe'
                                ? 'bg-amber-500 text-white shadow-sm'
                                : 'bg-white dark:bg-[#242526] text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 hover:bg-amber-50 dark:hover:bg-amber-500/10' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        Maybe
                    </button>

                    {{-- No --}}
                    <button type="button" wire:click="rsvp('no')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ $this->response === 'no'
                                ? 'bg-red-500 text-white shadow-sm'
                                : 'bg-white dark:bg-[#242526] text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Can't make it
                    </button>
                </div>

                @if ($this->response)
                    <p class="text-xs text-black/50 dark:text-white/50 mt-3 text-center">
                        You responded <span class="font-semibold capitalize">{{ $this->response }}</span>.
                        You can change your response anytime before the deadline.
                    </p>
                @endif
            </div>
        </article>

        {{-- Back --}}
        <div>
            <a href="{{ route('alumni.message') }}" wire:navigate
                class="inline-flex items-center gap-2 text-sm font-semibold text-[#1877F2] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to inbox
            </a>
        </div>
    </div>
</div>