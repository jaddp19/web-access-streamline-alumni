<div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10">

    {{-- ========== HEADER ========== --}}
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-4 sm:mb-5">
        <div class="absolute -right-10 -top-10 w-32 sm:w-48 h-32 sm:h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-14 sm:top-16 w-16 sm:w-24 h-16 sm:h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-3 sm:gap-4">
            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white/50 text-xs sm:text-sm">Registrar</p>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate"
                    style="font-family: 'Fraunces', serif;">
                    Update Post
                </h1>
            </div>
        </div>
    </div>

    {{-- ========== FLASHES ========== --}}
    @if (session('success'))
        <div class="mb-4 sm:mb-5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 sm:mb-5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 sm:mb-5 bg-[#D4A537]/10 dark:bg-[#D4A537]/15 border border-[#D4A537]/30 text-[#123524] dark:text-[#E5B94A] rounded-xl p-3 sm:p-4 text-xs sm:text-sm flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <span>Editing <strong>{{ $post->title }}</strong>. The slug will be regenerated only if you change the title.</span>
    </div>

    {{-- ========== FORM CARD ========== --}}
    <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
        <form wire:submit="save" class="space-y-4 sm:space-y-5">

            {{-- Title --}}
            <div>
                <label for="title"
                    class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Post Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" wire:model.live.debounce.400ms="title"
                    maxlength="255" placeholder="e.g. Alumni Homecoming 2026"
                    class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                    @error('title')
                        border-red-400 dark:border-red-500/50
                    @else
                        border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                    @enderror">
                @error('title')
                    <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                @enderror

                <p class="text-[11px] text-black/40 dark:text-white/40 mt-1.5">
                    Slug: <span class="font-mono text-black/60 dark:text-white/60">{{ $this->slugPreview }}</span>
                </p>
            </div>

            {{-- Description --}}
            <div>
                <label for="description"
                    class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Description
                    <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                </label>
                <textarea id="description" wire:model="description" rows="5"
                    maxlength="5000" placeholder="Write the post body or a short summary…"
                    class="w-full px-3 sm:px-4 py-3 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition resize-y
                    @error('description')
                        border-red-400 dark:border-red-500/50
                    @else
                        border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                    @enderror"></textarea>
                @error('description')
                    <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                @enderror

                <p class="text-[11px] text-black/40 dark:text-white/40 mt-1.5">
                    {{ $this->descriptionLength }} / 5000 characters
                </p>
            </div>

            {{-- Category + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <label for="category_id"
                        class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="category_id" id="category_id"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                        @error('category_id')
                            border-red-400 dark:border-red-500/50
                        @else
                            border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                        @enderror">
                        <option value="">Select category</option>
                        @forelse ($this->categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @empty
                            <option value="" disabled>No categories available</option>
                        @endforelse
                    </select>
                    @error('category_id')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="status"
                        class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="status" id="status"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition">
                        <option value="draft">Draft — save without publishing</option>
                        <option value="private">Private — admin view only</option>
                        <option value="public">Public — visible to alumni</option>
                    </select>
                    @error('status')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                @if ($post->status === 'public')
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="resendAnnouncement"
                            class="mt-0.5 size-4 rounded">
                        <span class="text-xs">
                            Resend this announcement to all alumni.
                        </span>
                    </label>
                @endif
            </div>
            
            {{-- Cover Image --}}
            <div>
                <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Cover Image
                </label>

                <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                    {{-- Preview --}}
                    <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0 relative">
                        @php
                            $previewSrc = null;
                            if ($image) {
                                $previewSrc = $image->temporaryUrl();
                            } elseif ($currentImage) {
                                $previewSrc = filter_var($currentImage, FILTER_VALIDATE_URL)
                                    ? $currentImage
                                    : Storage::url($currentImage);
                            }
                        @endphp

                        @if ($previewSrc)
                            <img src="{{ $previewSrc }}" alt="Preview" loading="lazy" class="w-full h-full object-cover">
                            <button type="button" wire:click="{{ $image ? 'removeImage' : 'removeExistingImage' }}"
                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition"
                                title="Remove image">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @else
                            <svg class="w-8 h-8 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        @endif
                    </div>

                    {{-- Upload --}}
                    <div class="flex-1 min-w-0">
                        <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-xs sm:text-sm text-black/70 dark:text-white/70
                                file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0
                                file:text-xs sm:file:text-sm file:font-semibold
                                file:bg-[#D4A537] file:text-[#123524]
                                hover:file:bg-[#E5B94A] file:cursor-pointer cursor-pointer">
                        <p class="text-[11px] text-black/40 dark:text-white/40 mt-1">
                            Uploading a new image will replace the current one. Max 5MB.
                        </p>
                        <div wire:loading wire:target="image" class="flex items-center gap-2 text-xs text-[#1877F2] font-semibold mt-1">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Uploading…
                        </div>
                        @error('image')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            <div>
                <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Attachments
                    <span class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">
                        ({{ $this->allAttachmentsCount }} / 5)
                    </span>
                </label>

                {{-- Existing attachments --}}
                @if (! empty($existingAttachments))
                    <div class="mb-3 space-y-2">
                        @foreach ($existingAttachments as $index => $path)
                            <div wire:key="existing-attachment-{{ $index }}-{{ md5($path) }}"
                                class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-[#F1EFE7] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-4 h-4 text-black/40 dark:text-white/40 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                    </svg>
                                    <span class="text-xs text-black/70 dark:text-white/70 truncate">
                                        {{ basename($path) }}
                                    </span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 font-semibold shrink-0">
                                        Uploaded
                                    </span>
                                </div>
                                <button type="button" wire:click="removeExistingAttachment({{ $index }})"
                                    class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline shrink-0">
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload new --}}
                <input type="file" wire:model="attachments" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png,.webp,.txt,.csv"
                    class="block w-full text-xs sm:text-sm text-black/70 dark:text-white/70
                        file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0
                        file:text-xs sm:file:text-sm file:font-semibold
                        file:bg-[#123524] dark:file:bg-[#3A3B3C] file:text-white
                        hover:file:bg-[#0d2819] dark:hover:file:bg-white/10 file:cursor-pointer cursor-pointer">
                <p class="text-[11px] text-black/40 dark:text-white/40 mt-1">
                    Adding files appends to the existing list. Total max 5. Each file max 10MB.
                </p>

                <div wire:loading wire:target="attachments" class="flex items-center gap-2 text-xs text-[#1877F2] font-semibold mt-1">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Uploading…
                </div>

                @error('attachments')
                    <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                @enderror

                @foreach ($errors->get('attachments.*') as $messages)
                    @foreach ($messages as $msg)
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $msg }}</span>
                    @endforeach
                @endforeach

                {{-- New uploads preview --}}
                @if (! empty($attachments))
                    <div class="mt-3 space-y-2">
                        @foreach ($attachments as $index => $file)
                            @if ($file)
                                <div wire:key="new-attachment-{{ $index }}-{{ $file->getFilename() }}"
                                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-[#F1EFE7] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-black/40 dark:text-white/40 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                        </svg>
                                        <span class="text-xs text-black/70 dark:text-white/70 truncate">
                                            {{ $file->getClientOriginalName() }}
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 font-semibold shrink-0">
                                            New
                                        </span>
                                    </div>
                                    <button type="button" wire:click="removeAttachment({{ $index }})"
                                        class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline shrink-0">
                                        Remove
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                <button type="submit" wire:loading.attr="disabled" wire:target="save,image,attachments"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">Update Post</span>
                    <span wire:loading wire:target="save">Updating…</span>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>

                <a href="{{ route('super-admin.post.view') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>