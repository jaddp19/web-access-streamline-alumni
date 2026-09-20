<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="max-w-[900px] mx-auto px-4 sm:px-6 py-6 space-y-5">

        {{-- Post card --}}
        <article class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden">

            {{-- Author header --}}
            <header class="flex items-center justify-between gap-3 px-5 sm:px-6 pt-5 pb-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if ($this->authorAvatarUrl)
                        <img src="{{ $this->authorAvatarUrl }}" alt="{{ $post->user->name }}"
                            class="w-12 h-12 rounded-full object-cover shrink-0 bg-[#D4A537]"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span style="display: none;"
                            class="w-12 h-12 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $this->authorInitial }}
                        </span>
                    @else
                        <span class="w-12 h-12 flex items-center justify-center text-lg font-bold text-[#0f2b1c] bg-yellow-500 rounded-full shrink-0">
                            {{ $this->authorInitial }}
                        </span>
                    @endif

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <p class="font-semibold text-black dark:text-white text-sm leading-tight truncate">
                                {{ $post->user->name ?? 'CSAV Admin' }}
                            </p>
                            @if ($this->authorRole)
                                <span class="inline-flex items-center align-middle whitespace-nowrap leading-none text-[10px] px-1.5 py-0.5 rounded bg-black/5 dark:bg-white/10 text-black/60 dark:text-white/60 font-semibold uppercase tracking-wide">
                                    {{ $this->authorRole }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-black/50 dark:text-white/50 mt-0.5">
                            {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <span class="shrink-0 text-[10px] px-2.5 py-1 rounded-full bg-[#123524]/10 dark:bg-[#D4A537]/15 text-[#123524] dark:text-[#D4A537] font-semibold uppercase tracking-wide">
                    Official
                </span>
            </header>

            {{-- Category --}}
            @if ($post->category)
                <div class="px-5 sm:px-6 pb-2">
                    <span class="inline-block text-[11px] px-2.5 py-1 rounded-full bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide">
                        {{ $post->category->cat_name }}
                    </span>
                </div>
            @endif

            {{-- Title --}}
            <div class="px-5 sm:px-6 pb-4">
                <h1 class="text-xl sm:text-2xl font-bold text-black dark:text-white leading-snug"
                    style="font-family: 'Fraunces', serif;">
                    {{ $post->title }}
                </h1>
            </div>

            {{-- Cover image --}}
            @if ($this->coverImageUrl)
                <div class="bg-black/5 dark:bg-black/30">
                    <img src="{{ $this->coverImageUrl }}" alt="{{ $post->title }}"
                        class="w-full max-h-[520px] object-contain mx-auto" loading="lazy">
                </div>
            @endif

            {{-- Description body --}}
            @if ($post->description)
                <div class="px-5 sm:px-6 py-5">
                    <div class="text-sm sm:text-base text-black/80 dark:text-white/80 whitespace-pre-line leading-relaxed">
                        {{ $post->description }}
                    </div>
                </div>
            @endif

            {{-- Attachments --}}
            @if (!empty($this->attachmentList))
                <div class="px-5 sm:px-6 py-5 border-t border-black/5 dark:border-white/5">
                    <h3 class="text-xs font-bold text-black/60 dark:text-white/60 uppercase tracking-wide mb-3">
                        Attachments · {{ count($this->attachmentList) }}
                    </h3>

                    <div class="space-y-2">
                        @foreach ($this->attachmentList as $att)
                            <a href="{{ $att['url'] }}" download
                                class="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/5 hover:border-[#123524]/30 dark:hover:border-[#D4A537]/30 transition group">

                                {{-- Extension badge --}}
                                <span class="w-10 h-10 rounded-lg bg-white dark:bg-[#242526] flex items-center justify-center text-[10px] font-bold uppercase text-[#123524] dark:text-[#D4A537] shrink-0 border border-black/5 dark:border-white/5">
                                    {{ $att['ext'] ?: 'FILE' }}
                                </span>

                                <span class="flex-1 min-w-0 text-sm font-medium text-black/80 dark:text-white/80 truncate">
                                    {{ $att['name'] }}
                                </span>

                                <svg class="w-4 h-4 text-black/40 dark:text-white/40 group-hover:text-[#123524] dark:group-hover:text-[#D4A537] transition shrink-0"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </article>

        {{-- Related posts --}}
        @if ($this->relatedPosts->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-sm font-bold text-black/60 dark:text-white/60 uppercase tracking-wide px-1">
                    More from CSAV
                </h2>

                @foreach ($this->relatedPosts as $related)
                    @php
                        $relatedRole = $related->user?->roles->first()?->name;
                        $relatedImage = $related->image
                            ? (filter_var($related->image, FILTER_VALIDATE_URL)
                                ? $related->image
                                : \Illuminate\Support\Facades\Storage::url($related->image))
                            : null;
                    @endphp

                    <a href="{{ route('alumni.view-notification', $related->id) }}"
                        class="flex items-start gap-3 bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition group">

                        @if ($relatedImage)
                            <img src="{{ $relatedImage }}" alt="{{ $related->title }}"
                                class="w-16 h-16 rounded-xl object-cover shrink-0 bg-[#F0F2F5] dark:bg-[#3A3B3C]"
                                loading="lazy">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-black/30 dark:text-white/30" fill="none"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09" />
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-black dark:text-white text-sm truncate group-hover:text-[#1877F2] dark:group-hover:text-[#D4A537] transition">
                                {{ $related->title }}
                            </p>

                            @if ($related->description)
                                <p class="text-xs text-black/60 dark:text-white/60 mt-0.5 line-clamp-2">
                                    {{ $related->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                @if ($related->category)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#D4A537]/15 dark:bg-[#D4A537]/20 text-[#a97f1f] dark:text-[#E5B94A] font-semibold uppercase tracking-wide">
                                        {{ $related->category->cat_name }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-black/50 dark:text-white/50">
                                    {{ $related->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </section>
        @endif

    </div>
</div>