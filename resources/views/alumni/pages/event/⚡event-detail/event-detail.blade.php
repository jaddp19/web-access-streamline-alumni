<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">
    <div class="max-w-[900px] mx-auto px-4 py-6">

        {{-- ========== BACK ========== --}}
        <div class="mb-4">
            <a href="{{ route('alumni.dashboard') }}"
                class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                </svg>
                Back
            </a>
        </div>

        {{-- ========== FLASH ========== --}}
        @if (session('rsvp_success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-start gap-2">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('rsvp_success') }}</span>
            </div>
        @endif

        {{-- ========== CARD ========== --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden border border-transparent dark:border-white/5">

            {{-- Cover image --}}
            @if ($event->image)
                <div class="bg-black">
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                        class="w-full max-h-[420px] object-contain">
                </div>
            @endif

            <div class="p-5 sm:p-8">

                {{-- ========== TITLE + STATUS BADGE ========== --}}
                <div class="flex items-start justify-between gap-3 mb-4">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#0f2b1c] dark:text-white"
                        style="font-family: 'Fraunces', serif;">
                        {{ $event->title }}
                    </h1>

                    @if ($event->isCancelled())
                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400 font-bold uppercase tracking-wide shrink-0">
                            Cancelled
                        </span>
                    @elseif ($event->isCompleted())
                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400 font-bold uppercase tracking-wide shrink-0">
                            Completed
                        </span>
                    @endif
                </div>

                {{-- ========== INFO GRID ========== --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">

                    {{-- Date --}}
                    <div class="flex items-center gap-2.5 text-sm text-black/70 dark:text-white/70 bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-xl px-3.5 py-3">
                        <svg class="w-4 h-4 text-[#123524] dark:text-[#D4A537] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span class="font-semibold">{{ $event->starts_at->format('l, F j, Y') }}</span>
                    </div>

                    {{-- Time --}}
                    <div class="flex items-center gap-2.5 text-sm text-black/70 dark:text-white/70 bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-xl px-3.5 py-3">
                        <svg class="w-4 h-4 text-[#123524] dark:text-[#D4A537] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold">
                            {{ $event->starts_at->format('g:i A') }}
                            @if ($event->ends_at) — {{ $event->ends_at->format('g:i A') }} @endif
                        </span>
                    </div>

                    {{-- Location --}}
                    @if ($event->location)
                        <div class="flex items-center gap-2.5 text-sm text-black/70 dark:text-white/70 bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-xl px-3.5 py-3">
                            <svg class="w-4 h-4 text-[#123524] dark:text-[#D4A537] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="font-semibold truncate">{{ $event->location }}</span>
                        </div>
                    @endif

                    {{-- Attendee count --}}
                    <div class="flex items-center gap-2.5 text-sm text-black/70 dark:text-white/70 bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-xl px-3.5 py-3">
                        <svg class="w-4 h-4 text-[#123524] dark:text-[#D4A537] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-semibold">
                            {{ $this->attendeeCount }} attending
                            @if ($event->capacity) · limit {{ $event->capacity }} @endif
                        </span>
                    </div>
                </div>

                {{-- ========== REGISTRATION DEADLINE ========== --}}
                @if ($event->registration_deadline && ! $event->isCancelled() && $event->starts_at->isFuture())
                    <div class="mb-6 flex items-center gap-2.5 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl px-3.5 py-2.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            <strong>Registration closes</strong>
                            {{ $event->registration_deadline->diffForHumans() }}
                            — {{ $event->registration_deadline->format('M j, Y · g:i A') }}
                        </span>
                    </div>
                @endif

                {{-- ========== DESCRIPTION ========== --}}
                @if ($event->description)
                    <div class="text-sm sm:text-base text-black/70 dark:text-white/70 mb-6 whitespace-pre-line leading-relaxed">
                        {{ $event->description }}
                    </div>
                @endif

                {{-- ========== RSVP SECTION ========== --}}
                @if ($this->canRsvp)
                    <div class="border-t border-black/5 dark:border-white/5 pt-6">
                        <h2 class="text-base font-bold text-[#0f2b1c] dark:text-white mb-3"
                            style="font-family: 'Fraunces', serif;">
                            Will you attend?
                        </h2>

                        @error('rsvp')
                            <div class="mb-3 px-3 py-2 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg text-red-700 dark:text-red-400 text-xs">
                                {{ $message }}
                            </div>
                        @enderror

                        @php $current = $this->myRsvp?->response; @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            {{-- Going --}}
                            <button type="button" wire:click="rsvp('yes')" wire:loading.attr="disabled" wire:target="rsvp"
                                class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold rounded-xl transition border-2 disabled:opacity-50
                                {{ $current === 'yes'
                                    ? 'bg-emerald-600 border-emerald-600 text-white'
                                    : 'bg-white dark:bg-[#3A3B3C] border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Going
                            </button>

                            {{-- Maybe --}}
                            <button type="button" wire:click="rsvp('maybe')" wire:loading.attr="disabled" wire:target="rsvp"
                                class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold rounded-xl transition border-2 disabled:opacity-50
                                {{ $current === 'maybe'
                                    ? 'bg-amber-500 border-amber-500 text-white'
                                    : 'bg-white dark:bg-[#3A3B3C] border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                </svg>
                                Maybe
                            </button>

                            {{-- Can't go --}}
                            <button type="button" wire:click="rsvp('no')" wire:loading.attr="disabled" wire:target="rsvp"
                                class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold rounded-xl transition border-2 disabled:opacity-50
                                {{ $current === 'no'
                                    ? 'bg-red-500 border-red-500 text-white'
                                    : 'bg-white dark:bg-[#3A3B3C] border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Can't go
                            </button>
                        </div>

                        @if ($current)
                            <button type="button" wire:click="cancelRsvp" wire:loading.attr="disabled" wire:target="cancelRsvp"
                                class="mt-3 text-xs text-black/40 dark:text-white/40 hover:underline disabled:opacity-50">
                                Remove my response
                            </button>
                        @endif
                    </div>
                @else
                    {{-- RSVP closed notice --}}
                    <div class="border-t border-black/5 dark:border-white/5 pt-6">
                        <div class="rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] px-4 py-3 text-sm text-black/50 dark:text-white/50 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span>
                                @if ($event->isCancelled())
                                    This event has been cancelled.
                                @elseif ($event->starts_at->isPast())
                                    This event has already taken place.
                                @elseif ($event->registration_deadline && now()->isAfter($event->registration_deadline))
                                    Registration closed on {{ $event->registration_deadline->format('M j, Y') }}.
                                @else
                                    RSVP is currently unavailable.
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========== RELATED / BACK LINK ========== --}}
        <div class="mt-5 text-center">
            <a href="{{ route('alumni.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-[#1877F2] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                </svg>
                Back to events
            </a>
        </div>
    </div>
</div>