<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">

            <!-- ===================== CARD ===================== -->
            <div class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden p-4 sm:p-6 lg:p-8">

                <!-- Back -->
                <div class="mb-5 sm:mb-6">
                    <a href="{{ route('super-admin.email.view') }}"
                        class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524]/70 dark:text-[#D4A537]/70 hover:text-[#123524] dark:hover:text-[#D4A537] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        <span>Back to Email Templates</span>
                    </a>
                </div>

                <!-- Title -->
                <div class="flex items-center gap-3 sm:gap-4 mb-5 sm:mb-6">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Create Email Template
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">A URL-friendly slug will be generated from the subject</p>
                    </div>
                </div>

                <!-- Flashes -->
                @if (session('error'))
                    <div class="mb-4 sm:mb-5 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit="create" class="space-y-4 sm:space-y-5">

                    <!-- Subject -->
                    <div>
                        <label for="subject"
                            class="block text-[11px] sm:text-xs uppercase tracking-wide font-semibold text-black/60 dark:text-white/60 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.live.debounce.500ms="subject" type="text" id="subject"
                            maxlength="255"
                            placeholder="e.g. Welcome to the Alumni Portal"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-[#123524] dark:text-white placeholder-[#123524]/30 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 transition
                            @error('subject')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-[#123524]/15 dark:border-white/10 focus:ring-[#D4A537] focus:border-transparent
                            @enderror">
                        @error('subject')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror

                        <p class="text-[11px] text-black/40 dark:text-white/40 mt-1.5 flex items-center justify-between gap-2">
                            <span>Slug: <span class="font-mono text-black/60 dark:text-white/60">{{ $this->slugPreview }}</span></span>
                            <span>{{ $this->subjectLength }} / 255</span>
                        </p>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message"
                            class="block text-[11px] sm:text-xs uppercase tracking-wide font-semibold text-black/60 dark:text-white/60 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model.live.debounce.500ms="message" id="message" rows="8"
                            maxlength="20000"
                            placeholder="Write the email content here… HTML tags are supported."
                            class="w-full px-3 sm:px-4 py-3 rounded-xl border bg-[#F7F5EF] dark:bg-[#3A3B3C] text-sm text-[#123524] dark:text-white placeholder-[#123524]/30 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 transition resize-y
                            @error('message')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-[#123524]/15 dark:border-white/10 focus:ring-[#D4A537] focus:border-transparent
                            @enderror"></textarea>
                        @error('message')
                            <span class="block mt-1 text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror

                        <p class="text-[11px] text-black/40 dark:text-white/40 mt-1.5 text-right">
                            {{ $this->messageLength }} / 20,000
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                        <button type="submit"
                            wire:loading.attr="disabled" wire:target="create"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 sm:px-6 py-2.5 bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] text-sm font-semibold rounded-xl hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="create">Create Template</span>
                            <span wire:loading wire:target="create">Creating…</span>
                            <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14" />
                            </svg>
                        </button>

                        <button type="button" wire:click="togglePreview"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 sm:px-6 py-2.5 bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-[#123524] dark:text-white text-sm font-semibold rounded-xl hover:bg-black/5 dark:hover:bg-white/5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $showPreview ? 'Hide Preview' : 'Show Preview' }}
                        </button>
                    </div>
                </form>

                <!-- Live Preview -->
                @if ($showPreview)
                    <div class="mt-6 pt-6 border-t border-black/5 dark:border-white/10">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <h3 class="text-[11px] sm:text-xs uppercase tracking-wide font-semibold text-black/60 dark:text-white/60">
                                Live Preview
                            </h3>
                            <span class="text-[11px] text-black/40 dark:text-white/40">
                                Updates as you type
                            </span>
                        </div>

                        <div class="rounded-xl overflow-hidden border border-black/10 dark:border-white/10 bg-white">
                            <iframe
                                wire:key="email-preview-{{ md5($this->previewHtml) }}"
                                srcdoc="{{ $this->previewHtml }}"
                                class="w-full h-[400px] sm:h-[500px] lg:h-[600px]"
                                sandbox="allow-same-origin"
                                title="Email Template Preview"
                            ></iframe>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>