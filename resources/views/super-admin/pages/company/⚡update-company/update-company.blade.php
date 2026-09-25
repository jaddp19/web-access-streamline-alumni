<div>
    <div class="max-w-[85rem] px-3 sm:px-6 lg:px-8 py-6 sm:py-10 lg:py-14 mx-auto">
        <div
            class="flex flex-col rounded-2xl border border-black/5 dark:border-white/5 bg-white dark:bg-[#242526] shadow-sm overflow-hidden">

            <!-- ===================== HEADER ===================== -->
            <div
                class="px-4 sm:px-6 py-4 sm:py-5 flex flex-col gap-3 lg:flex-row lg:justify-between lg:items-center border-b border-black/5 dark:border-white/5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-green-700/10 dark:bg-emerald-500/15 flex items-center justify-center text-green-700 dark:text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-[#123524] dark:text-white truncate"
                            style="font-family: 'Fraunces', serif;">
                            Edit Company
                        </h2>
                        <p class="text-xs sm:text-sm text-black/50 dark:text-white/50 truncate">
                            Last updated {{ $company->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('super-admin.company.view') }}"
                        class="w-full sm:w-auto justify-center py-2 px-3.5 inline-flex items-center gap-x-2 text-xs sm:text-sm font-semibold rounded-lg border border-[#123524]/15 dark:border-white/10 text-[#123524] dark:text-white hover:bg-[#123524]/5 dark:hover:bg-white/5 transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <!-- ===================== FLASHES ===================== -->
            @if (session('success'))
                <div
                    class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mx-4 sm:mx-6 mt-4 flex items-start gap-2.5 px-3 sm:px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-xs sm:text-sm font-medium">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
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
                        <label for="company_name"
                            class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="company_name" wire:model="company_name" maxlength="255"
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
                        <label for="company_logo"
                            class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Company Logo
                        </label>

                        <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                            {{-- Preview --}}
                            <div
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl border border-black/10 dark:border-white/10 bg-[#F7F5EF] dark:bg-[#3A3B3C] flex items-center justify-center overflow-hidden shrink-0">
                                @php
                                    $existingLogo    = $company->company_logo;
                                    $existingLogoUrl = $existingLogo
                                        ? (filter_var($existingLogo, FILTER_VALIDATE_URL)
                                            ? $existingLogo
                                            : Storage::url($existingLogo))
                                        : null;
                                @endphp
                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}" alt="New logo preview" loading="lazy"
                                        class="w-full h-full object-contain">
                                @elseif ($existingLogoUrl && ! $remove_logo)
                                    <img src="{{ $existingLogoUrl }}" alt="Current logo" loading="lazy"
                                        class="w-full h-full object-contain">
                                @else
                                    <svg class="w-8 h-8 text-black/20 dark:text-white/20" fill="none"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
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

                                <div wire:loading wire:target="company_logo"
                                    class="flex items-center gap-2 mt-1 text-xs text-[#123524] dark:text-[#D4A537]">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Uploading…
                                </div>

                                @error('company_logo')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                @if ($company->company_logo && ! $remove_logo && ! $company_logo)
                                    <button type="button" wire:click="$toggle('remove_logo')"
                                        class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:underline">
                                        Remove current logo
                                    </button>
                                @endif

                                @if ($remove_logo)
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400 font-semibold">
                                        Logo will be removed on save.
                                        <button type="button" wire:click="$toggle('remove_logo')"
                                            class="underline ml-1">Undo</button>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Company Address --}}
                    <div>
                        <label
                            class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Address
                        </label>

                        {{-- Address Type Toggle --}}
                        <div class="flex flex-wrap gap-x-6 gap-y-2 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="address_type" value="philippines"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-black dark:text-white">Philippines</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="address_type" value="abroad"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-black dark:text-white">Abroad / Other Country</span>
                            </label>
                        </div>
                        @error('address_type')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- ===== PHILIPPINES ===== --}}
                        @if ($address_type === 'philippines')
                            <div class="space-y-4">

                                {{-- Region --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        Region <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="regionCode"
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition
                                        @error('regionCode')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                        <option value="">Select Region</option>
                                        @foreach ($this->regions as $region)
                                            <option value="{{ $region->code }}">{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('regionCode')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Province --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        Province <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="provinceCode" @disabled(!$regionCode)
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition disabled:opacity-50 disabled:cursor-not-allowed
                                        @error('provinceCode')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                        <option value="">Select Province</option>
                                        @foreach ($this->provinces as $province)
                                            <option value="{{ $province->code }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('provinceCode')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- City / Municipality --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        City / Municipality <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="cityCode" @disabled(!$provinceCode)
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition disabled:opacity-50 disabled:cursor-not-allowed
                                        @error('cityCode')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                        <option value="">Select City / Municipality</option>
                                        @foreach ($this->cities as $city)
                                            <option value="{{ $city->code }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('cityCode')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Street Address --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        Street Address
                                        <span
                                            class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="text" wire:model="street_address" maxlength="500"
                                        placeholder="Building, street, barangay"
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                        @error('street_address')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                    @error('street_address')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        {{-- ===== ABROAD ===== --}}
                        @if ($address_type === 'abroad')
                            <div class="space-y-4">

                                {{-- Country --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="intl_country" maxlength="255"
                                        placeholder="e.g. United Arab Emirates"
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                        @error('intl_country')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                    @error('intl_country')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- State / Province / Emirate --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        State / Province / Emirate
                                        <span
                                            class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="text" wire:model="intl_state" maxlength="255"
                                        placeholder="e.g. Dubai, California, Ontario"
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                        @error('intl_state')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                    @error('intl_state')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- City --}}
                                <div>
                                    <label
                                        class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="intl_city" maxlength="255"
                                        placeholder="e.g. Dubai, Los Angeles"
                                        class="w-full py-2.5 px-3 sm:px-3.5 text-sm rounded-lg border bg-white dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                        @error('intl_city')
                                            border-red-400 dark:border-red-500/50 focus:ring-red-400/50 focus:border-transparent
                                        @else
                                            border-black/15 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537]
                                        @enderror">
                                    @error('intl_city')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Company Description --}}
                    <div>
                        <label for="company_desc"
                            class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60 mb-2">
                            Description
                        </label>
                        <textarea id="company_desc" wire:model="company_desc" rows="4" maxlength="2000"
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
                <div
                    class="mt-6 sm:mt-8 pt-5 border-t border-black/5 dark:border-white/10 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <a href="{{ route('super-admin.company.view') }}"
                        class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold rounded-lg border border-[#123524]/15 dark:border-white/10 text-[#123524] dark:text-white hover:bg-[#123524]/5 dark:hover:bg-white/5 transition">
                        Cancel
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save,company_logo"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-5 py-2.5 text-sm font-semibold rounded-lg bg-[#123524] dark:bg-[#D4A537] text-white dark:text-[#123524] hover:bg-[#0d2819] dark:hover:bg-[#E5B94A] transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Update Company</span>
                        <span wire:loading wire:target="save">Updating…</span>
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>