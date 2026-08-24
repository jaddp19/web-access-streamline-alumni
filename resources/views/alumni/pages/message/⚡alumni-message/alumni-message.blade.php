<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen" wire:poll.5s="$refresh">

    {{-- ========== FACEBOOK-STYLE HEADER BANNER ========== --}}
    <div class="bg-white dark:bg-[#242526] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-40">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-[#123524] flex items-center justify-center text-white font-bold text-xl shrink-0" style="font-family: 'Fraunces', serif;">
                    {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-black dark:text-white leading-tight" style="font-family: 'Fraunces', serif;">Alumni Wall</h1>
                    <p class="text-xs text-black/50 dark:text-white/50">Your community feed</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <div class="px-4 py-1.5 rounded-full bg-[#123524]/5 dark:bg-[#D4A537]/10 text-[#123524] dark:text-[#D4A537] text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    {{ $this->posts->total() }} Alumni posts
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TWO-COLUMN LAYOUT ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ===== LEFT SIDEBAR (Profile quick card) ===== --}}
        <aside class="hidden lg:block lg:col-span-3">
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden sticky top-24">
                <div class="h-16 bg-gradient-to-r from-[#123524] to-[#1C6B45]"></div>
                <div class="px-4 pb-4 text-center -mt-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-[#D4A537] ring-4 ring-white dark:ring-[#242526] flex items-center justify-center text-[#123524] font-bold text-3xl" style="font-family: 'Fraunces', serif;">
                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                    </div>
                    <p class="font-bold text-black dark:text-white mt-2" style="font-family: 'Fraunces', serif;">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-black/50 dark:text-white/50">Alumni Member</p>

                    <div class="border-t border-black/5 dark:border-white/5 mt-4 pt-3 text-left">
                        <a href="{{ route('alumni.profile') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#123524] dark:hover:text-[#D4A537] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                My Profile
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                        <a href="{{ route('alumni.dashboard') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#123524] dark:hover:text-[#D4A537] transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                                Dashboard
                            </span>
                            <span class="text-xs text-black/40 dark:text-white/40">&rarr;</span>
                        </a>
                        <a href="{{ route('alumni.settings') }}" class="flex items-center justify-between py-1.5 text-sm text-black/70 dark:text-white/70 hover:text-[#123524] dark:hover:text-[#D4A537] transition">
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
        <main class="lg:col-span-9 space-y-4">

            {{-- ========== CREATE POST (FB-style) ========== --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                    </div>
                    <button type="button"
                        onclick="document.getElementById('composer-body').classList.toggle('hidden'); document.getElementById('composer-body').scrollIntoView({behavior:'smooth', block:'center'});"
                        class="flex-1 text-left px-4 py-2.5 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-black/50 dark:text-white/60 text-sm transition">
                        What's on your mind, {{ explode(' ', auth()->user()->name)[0] }}?
                    </button>
                </div>

                <div id="composer-body" class="hidden mt-3 pt-3 border-t border-black/5 dark:border-white/5 space-y-3">
                    <form wire:submit.prevent="post">
                        <input type="text" wire:model.defer="title" placeholder="Title of your post"
                            class="w-full px-4 py-2.5 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] border-0 text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#1877F2]/40 text-sm font-semibold">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                        <textarea wire:model.defer="description" rows="3" placeholder="Share something with your fellow alumni..."
                            class="mt-2 w-full px-4 py-2.5 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] border-0 text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#1877F2]/40 text-sm resize-none"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                        @if ($image)
                            <div class="mt-2 text-xs text-black/50 dark:text-white/50 inline-flex items-center gap-2 bg-[#F0F2F5] dark:bg-[#3A3B3C] px-3 py-1.5 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                {{ $image->getClientOriginalName() }}
                            </div>
                        @endif

                        <div class="mt-3 flex items-center justify-between border border-black/10 dark:border-white/10 rounded-xl px-2 py-1.5">
                            <span class="text-xs font-semibold text-black/60 dark:text-white/60 pl-2">Add to your post</span>
                            <div class="flex items-center gap-1">
                                <label class="p-2 rounded-full hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] cursor-pointer text-[#45BD62]" title="Photo/Video">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                    <input type="file" wire:model="image" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                        <div wire:loading wire:target="image" class="text-xs text-black/50 dark:text-white/50 mt-1">Uploading image...</div>

                        <div class="mt-3 flex items-center justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('composer-body').classList.add('hidden');"
                                class="px-4 py-2 text-sm font-semibold rounded-lg text-black/70 dark:text-white/70 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166FE5] transition inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="text-sm text-emerald-800 dark:text-emerald-300 font-medium bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl px-4 py-2.5 inline-flex items-center gap-2 w-full">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- ========== POSTS FEED ========== --}}
            @forelse ($this->posts as $post)
                <article class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm overflow-hidden">

                    {{-- Post header --}}
                    <header class="flex items-center justify-between p-4 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold shrink-0">
                                {{ strtoupper(substr($post->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-black dark:text-white text-sm leading-tight">{{ $post->user->name ?? 'Unknown Alumni' }}</p>
                                <p class="text-xs text-black/50 dark:text-white/50 flex items-center gap-1 mt-0.5">
                                    {{ $post->created_at->diffForHumans() }} &middot;
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A8.966 8.966 0 013 12c0-1.264.26-2.467.732-3.559" /></svg>
                                Public
                                </p>
                            </div>
                        </div>
                        @if ($post->user_id === auth()->id())
                            <button wire:click="deletePost({{ $post->id }})"
                                wire:confirm="Delete this post?"
                                class="w-9 h-9 rounded-full hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] flex items-center justify-center text-black/50 dark:text-white/50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        @endif
                    </header>

                    {{-- Post body --}}
                    <div class="px-4 pb-3">
                        <p class="font-bold text-black dark:text-white text-base" style="font-family: 'Fraunces', serif;">{{ $post->title }}</p>
                        @if ($post->description)
                            <p class="text-black/80 dark:text-white/80 text-sm whitespace-pre-line mt-1 leading-relaxed">{{ $post->description }}</p>
                        @endif
                    </div>

                    {{-- Post image (FB-style, full-width with no side padding) --}}
                    @if ($post->image)
                        <div class="bg-black">
                            <img src="{{ Storage::url($post->image) }}" alt="Post image" class="w-full max-h-[500px] object-contain">
                        </div>
                    @endif

                    {{-- Stats bar --}}
                    <div class="px-4 py-2 flex items-center justify-between text-xs text-black/50 dark:text-white/50">
                        <span>
                            <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2 10h4v12H2zm6.5-7l3 7v12h-3v-7l-3 1v-3l3-10zm6.5 7h4v12h-4zm6.5-3l-3 3v9h3v-9l1.5-1.5L18 7z"/></svg>
                            {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                        </span>
                    </div>

                    {{-- Action bar --}}
                    <div class="border-t border-black/5 dark:border-white/5 px-2 py-1 flex items-center justify-around">
                        <button type="button"
                            onclick="document.getElementById('comment-input-{{ $post->id }}').focus(); document.getElementById('comment-input-{{ $post->id }}').scrollIntoView({behavior:'smooth', block:'center'});"
                            class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-[#3A3B3C] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                            Comment
                        </button>
                    </div>

                    {{-- Comments list --}}
                    @if ($post->comments->count())
                        <div class="px-4 pb-3 pt-2 space-y-3">
                            @foreach ($post->comments as $comment)
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-full bg-[#1C6B45]/15 dark:bg-[#1C6B45]/25 flex items-center justify-center text-[#1C6B45] dark:text-[#4CAF7D] font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="inline-block bg-[#F0F2F5] dark:bg-[#3A3B3C] rounded-2xl px-3 py-2 max-w-full">
                                            <p class="font-semibold text-black dark:text-white text-xs leading-tight">{{ $comment->user->name ?? 'Unknown' }}</p>
                                            <p class="text-black/90 dark:text-white/90 text-sm whitespace-pre-line break-words">{{ $comment->body }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 ml-2 mt-0.5 text-xs text-black/50 dark:text-white/50">
                                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                                            @if ($comment->user_id === auth()->id())
                                                <button wire:click="deleteComment({{ $comment->id }})"
                                                    wire:confirm="Delete this comment?"
                                                    class="font-semibold hover:text-red-500 transition">
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Comment composer (FB-style with avatar + input) --}}
                    <div class="border-t border-black/5 dark:border-white/5 px-4 py-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#D4A537] flex items-center justify-center text-[#123524] font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                        </div>
                        <form wire:submit.prevent="addComment({{ $post->id }})" class="flex-1 flex items-center gap-2">
                            <input id="comment-input-{{ $post->id }}" type="text"
                                wire:model.defer="commentDrafts.{{ $post->id }}"
                                placeholder="Write a comment..."
                                class="flex-1 px-3.5 py-2 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] border-0 text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-[#1877F2]/40 transition">
                            <button type="submit"
                                class="text-[#1877F2] hover:bg-[#1877F2]/10 p-2 rounded-full transition disabled:opacity-40"
                                title="Send comment">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#F0F2F5] dark:bg-[#3A3B3C] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-black/40 dark:text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                    </div>
                    <p class="font-bold text-black dark:text-white text-lg" style="font-family: 'Fraunces', serif;">No posts yet</p>
                    <p class="text-black/50 dark:text-white/50 text-sm mt-1">Be the first to share something with your fellow alumni.</p>
                </div>
            @endforelse

            @if ($this->posts->hasPages())
                <div class="pt-2 flex justify-center">
                    {{ $this->posts->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
