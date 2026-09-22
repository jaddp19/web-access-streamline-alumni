<div class="max-w-[85rem] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10">
    <div class="max-w-3xl mx-auto">

        <div class="mb-5">
            <a href="{{ route('super-admin.events.view') }}"
                class="inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold text-[#123524] dark:text-[#D4A537] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                </svg>
                Back to events
            </a>
        </div>

        <div class="relative overflow-hidden bg-[#123524] dark:bg-[#1a1b1c] rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 mb-5">
            <div class="relative flex items-center gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-white/50 text-xs sm:text-sm">Super Admin</p>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate" style="font-family: 'Fraunces', serif;">
                        Create Event
                    </h1>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="mb-5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-3 sm:p-4 text-xs sm:text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/5 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8">
            <form wire:submit="save" class="space-y-4 sm:space-y-5">

                {{-- Title --}}
                <div>
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Event Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title" maxlength="255"
                        placeholder="e.g. Alumni Homecoming 2026"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                        @error('title') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                    @error('title') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Description</label>
                    <textarea wire:model="description" rows="4" maxlength="5000"
                        placeholder="Describe the event…"
                        class="w-full px-3 sm:px-4 py-3 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition resize-y
                        @error('description') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror"></textarea>
                    @error('description') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Location --}}
                <div>
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Location</label>
                    <input type="text" wire:model="location" maxlength="255"
                        placeholder="e.g. CSAV Gymnasium"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                        @error('location') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                    @error('location') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                            Starts At <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" wire:model="starts_at"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('starts_at') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                        @error('starts_at') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Ends At</label>
                        <input type="datetime-local" wire:model="ends_at"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('ends_at') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                        @error('ends_at') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Registration deadline + capacity --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Registration Deadline</label>
                        <input type="datetime-local" wire:model="registration_deadline"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('registration_deadline') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                        @error('registration_deadline') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Capacity (optional)</label>
                        <input type="number" wire:model="capacity" min="1" max="10000"
                            placeholder="Leave empty for unlimited"
                            class="w-full px-3 sm:px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:ring-1 transition
                            @error('capacity') border-red-400 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] @enderror">
                        @error('capacity') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Status</label>
                    <select wire:model="status"
                        class="w-full px-3 sm:px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-sm text-black dark:text-white focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 transition">
                        <option value="draft">Draft — save without publishing</option>
                        <option value="published">Published — visible to alumni + email blast</option>
                    </select>

                    {{-- Resend invitation (only when published) --}}
@if ($status === 'published')
    <div class="rounded-xl border border-[#D4A537]/30 bg-[#D4A537]/5 dark:bg-[#D4A537]/10 p-3 sm:p-4">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" wire:model.live="resendInvitation"
                class="mt-0.5 shrink-0 size-4 rounded text-[#123524] dark:text-[#D4A537] border-black/30 dark:border-white/30">
            <span class="min-w-0">
                <span class="block text-xs sm:text-sm font-semibold text-[#123524] dark:text-white">
                    Resend invitation to all alumni
                </span>
                <span class="block text-[11px] sm:text-xs text-[#123524]/60 dark:text-white/60 mt-0.5">
                    {{ $event->status === 'published'
                        ? 'This event is already published. Tick to send a fresh invite — otherwise edits won\'t notify alumni.'
                        : 'This event will become published. Alumni will be notified on save.' }}
                </span>
            </span>
        </label>
    </div>
@endif
                </div>

                {{-- Cover image --}}
                <div>
                    <label class="block text-[11px] sm:text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">Cover Image</label>
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] flex items-center justify-center shrink-0 relative">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removeImage"
                                    class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @else
                                <svg class="w-8 h-8 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-xs sm:text-sm text-black/70 dark:text-white/70 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-[#D4A537] file:text-[#123524] hover:file:bg-[#E5B94A] file:cursor-pointer cursor-pointer">
                            <p class="text-[11px] text-black/40 dark:text-white/40 mt-1">JPG, PNG, WebP · max 5MB</p>
                            <div wire:loading wire:target="image" class="text-xs text-[#1877F2] font-semibold mt-1">Uploading…</div>
                            @error('image') <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save,image"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Create Event</span>
                        <span wire:loading wire:target="save">Creating…</span>
                    </button>
                    <a href="{{ route('super-admin.events.view') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>