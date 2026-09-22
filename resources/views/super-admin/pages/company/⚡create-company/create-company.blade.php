<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- ===================== HEADER ===================== -->
            <div class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col gap-3 lg:flex-row lg:justify-between lg:items-center border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Add Company
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50">Register a new partner or employer company</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('super-admin.company.view') }}"
                        class="w-full sm:w-auto justify-center py-2 px-3.5 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg border border-[#123524]/15 dark:border-white/10 text-[#123524] dark:text-white hover:bg-[#123524]/5 dark:hover:bg-white/5 transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <!-- ===================== FLASHES ===================== -->
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- ===================== FORM ===================== -->
            <form wire:submit="save" class="px-4 sm:px-6 py-5 sm:py-6">
                <div class="grid grid-cols-1 gap-4 sm:gap-5">

                    {{-- Company Name --}}
                    <div>
                        <label for="company_name" class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="company_name" wire:model="company_name"
                            maxlength="255"
                            placeholder="e.g. Accenture Philippines"
                            class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                            @error('company_name')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror">
                        @error('company_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company Logo --}}
                    <div>
                        <label for="company_logo" class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Company Logo
                        </label>

                        <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                            {{-- Preview --}}
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl border border-black/10 dark:border-white/10 bg-[#F7F5EF] dark:bg-[#3A3B3C] flex items-center justify-center overflow-hidden shrink-0">
                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}" alt="Logo preview" loading="lazy" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-8 h-8 text-black/20 dark:text-white/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 18h16.5M3.75 6.75h.008v.008H3.75V6.75zm0 3.75h.008v.008H3.75V10.5zm0 3.75h.008v.008H3.75v-.008z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Upload Control --}}
                            <div class="flex-1 min-w-0">
                                <input type="file" wire:model="company_logo" id="company_logo"
                                    accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                    class="block w-full text-xs sm:text-sm text-black/70 dark:text-white/70
                                        file:mr-3 file:py-2 file:px-3
                                        file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-semibold
                                        file:bg-[#123524] dark:file:bg-[#D4A537] file:text-white dark:file:text-[#123524]
                                        hover:file:bg-[#0d2819] dark:hover:file:bg-[#E5B94A] file:cursor-pointer
                                        border border-black/15 dark:border-white/10 rounded-lg bg-white dark:bg-[#3A3B3C]">
                                <p class="mt-1.5 text-[11px] sm:text-xs text-black/50 dark:text-white/50">
                                    PNG, JPG, WebP, or SVG · max 2MB · min 32×32px
                                </p>

                                <div wire:loading wire:target="company_logo" class="flex items-center gap-2 mt-1 text-xs text-[#123524] dark:text-[#D4A537]">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Uploading…
                                </div>

                                @error('company_logo')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Company Address --}}
                    <div>
                        <label for="company_address" class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Address
                        </label>
                        <textarea id="company_address" wire:model="company_address" rows="2"
                            maxlength="500"
                            placeholder="Building, street, city, province"
                            class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition resize-y
                            @error('company_address')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror"></textarea>
                        @error('company_address')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company Description --}}
                    <div>
                        <label for="company_desc" class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Description
                        </label>
                        <textarea id="company_desc" wire:model="company_desc" rows="4"
                            maxlength="2000"
                            placeholder="Short description of the company…"
                            class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition resize-y
                            @error('company_desc')
                                border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                            @else
                                border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                            @enderror"></textarea>
                        @error('company_desc')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 sm:mt-8 pt-5 border-t border-black/5 dark:border-white/10 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <a href="{{ route('super-admin.company.view') }}"
                       class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold rounded-lg border border-[#123524]/15 dark:border-white/10 text-[#123524] dark:text-white hover:bg-[#123524]/5 dark:hover:bg-white/5 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        wire:loading.attr="disabled" wire:target="save,company_logo"
                        class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Save Company</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>