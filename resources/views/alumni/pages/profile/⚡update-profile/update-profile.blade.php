<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ========== HEADER ========== --}}
    <div class="relative overflow-hidden bg-[#123524] dark:bg-[#0a1a10] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Edit Profile</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">
                    Personal Information
                </h1>
            </div>
        </div>
    </div>

    {{-- ========== FLASHES ========== --}}
    @if (session('error'))
        <div
            class="mb-5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-semibold rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (!$hasProfile)
        <div
            class="mb-5 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-400 font-semibold rounded-xl p-4 text-sm">
            You don't have a profile yet. Fill out the form below to set it up.
        </div>
    @endif

    {{-- ========== FORM CARD ========== --}}
    <div class="bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 rounded-3xl p-8">

        {{-- Success flash — top of card --}}
        @if (session('success'))
            <div
                class="mb-5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="saveProfile" class="space-y-5" x-data="{ avatarBusy: false }"
            @avatar-busy.window="avatarBusy = $event.detail.busy">

            {{-- ========== AVATAR ========== --}}
            @php
                $fallbackAvatar =
                    'https://ui-avatars.com/api/?name=' .
                    urlencode($this->fullName) .
                    '&background=D4A537&color=123524';
                $existingAvatarUrl = $currentAvatar ? Storage::url($currentAvatar) : null;
            @endphp

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5" x-data="{
                previewUrl: null,
                progress: 0,
                error: null,
                busy: false,
                existing: @js($existingAvatarUrl),
                fallback: @js($fallbackAvatar),
                get displaySrc() {
                    return this.previewUrl || this.existing || this.fallback;
                },
                async handleFile(event) {
                    const file = event.target.files?.[0];
                    if (!file) return;
            
                    this.error = null;
            
                    if (!file.type.startsWith('image/')) {
                        this.error = 'Please select an image file.';
                        return;
                    }
                    if (file.size > 15 * 1024 * 1024) {
                        this.error = 'Image is too large (max 15MB).';
                        return;
                    }
            
                    // Instant local preview
                    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                    this.previewUrl = URL.createObjectURL(file);
            
                    this.busy = true;
                    this.$dispatch('avatar-busy', { busy: true });
                    this.progress = 0;
            
                    try {
                        const compressed = await this.compress(file, 1024, 0.85);
            
                        await new Promise((resolve, reject) => {
                            $wire.upload(
                                'avatarFile',
                                compressed,
                                () => resolve(),
                                () => reject(new Error('Upload failed')),
                                (e) => { this.progress = e.detail.progress; }
                            );
                        });
                    } catch (err) {
                        console.error(err);
                        this.error = 'Could not upload the image. Please try again.';
                        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                        this.previewUrl = null;
                        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                    } finally {
                        this.busy = false;
                        this.$dispatch('avatar-busy', { busy: false });
                    }
                },
                compress(file, maxSize, quality) {
                    return new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onerror = () => resolve(file);
                        reader.onload = (e) => {
                            const img = new Image();
                            img.onerror = () => resolve(file);
                            img.onload = () => {
                                try {
                                    let { width, height } = img;
                                    if (width > height && width > maxSize) {
                                        height = Math.round((height * maxSize) / width);
                                        width = maxSize;
                                    } else if (height > maxSize) {
                                        width = Math.round((width * maxSize) / height);
                                        height = maxSize;
                                    }
                                    const canvas = document.createElement('canvas');
                                    canvas.width = width;
                                    canvas.height = height;
                                    canvas.getContext('2d').drawImage(img, 0, 0, width, height);
                                    canvas.toBlob(
                                        (blob) => {
                                            if (!blob) return resolve(file);
                                            const name = (file.name || 'avatar').replace(/\.[^.]+$/, '') + '.jpg';
                                            resolve(new File([blob], name, {
                                                type: 'image/jpeg',
                                                lastModified: Date.now(),
                                            }));
                                        },
                                        'image/jpeg',
                                        quality
                                    );
                                } catch (err) {
                                    console.error(err);
                                    resolve(file);
                                }
                            };
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }">

                {{-- Preview --}}
                <div class="relative shrink-0">
                    <img :src="displaySrc"
                        class="w-20 h-20 rounded-2xl object-cover border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C]"
                        alt="Avatar preview">

                    <div x-show="busy" x-cloak
                        class="absolute inset-0 rounded-2xl bg-black/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Input --}}
                <div class="flex-1 min-w-0 w-full">
                    <label
                        class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Avatar
                    </label>

                    <input type="file" x-ref="fileInput" @change="handleFile($event)"
                        accept="image/jpeg,image/png,image/webp" :disabled="busy"
                        class="block w-full text-sm text-black/70 dark:text-white/70
                      file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                      file:text-sm file:font-semibold
                      file:bg-[#D4A537] file:text-[#123524]
                      hover:file:bg-[#E5B94A] file:cursor-pointer cursor-pointer
                      disabled:opacity-60 disabled:cursor-not-allowed">

                    <p x-show="busy" x-cloak class="text-xs text-black/40 dark:text-white/40 mt-1">
                        <span x-show="progress < 100">
                            Compressing &amp; uploading… <span x-text="progress"></span>%
                        </span>
                        <span x-show="progress >= 100">Finishing up…</span>
                    </p>

                    <p x-show="error" x-cloak class="text-xs text-red-500 dark:text-red-400 mt-1" x-text="error"></p>

                    @error('avatarFile')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ========== GENDER ========== --}}
            <div>
                <label for="gender"
                    class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Gender <span class="text-red-500">*</span>
                </label>
                <select wire:model="gender" id="gender"
                    class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition
                    @error('gender') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                @error('gender')
                    <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- ========== PHONE NUMBERS ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Primary contact --}}
                <div>
                    <label
                        class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Contact Number 1 <span class="text-red-500">*</span>
                    </label>

                    <div wire:ignore x-data="{
                        iti: null,
                        init() {
                            if (!window.intlTelInput) {
                                setTimeout(() => this.init(), 150);
                                return;
                            }
                            const el = this.$refs.input;
                            if (!el || el._iti) return;
                    
                            this.iti = window.intlTelInput(el, {
                                initialCountry: 'ph',
                                preferredCountries: ['ph'],
                                separateDialCode: true,
                                strictMode: true,
                                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js',
                            });
                            el._iti = this.iti;
                    
                            const initial = el.dataset.initial;
                            if (initial) this.iti.setNumber(initial);
                    
                            const sync = () => {
                                $wire.set('contact_number_1', this.iti.getNumber() || '');
                            };
                            el.addEventListener('input', sync);
                            el.addEventListener('blur', sync);
                            el.addEventListener('countrychange', sync);
                        }
                    }" x-init="init()">
                        <input x-ref="input" type="tel"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white text-sm focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition"
                            autocomplete="tel" inputmode="tel" data-initial="{{ $contact_number_1 }}">
                    </div>
                    @error('contact_number_1')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alternate contact --}}
                <div>
                    <label
                        class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                        Contact Number 2
                        <span
                            class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                    </label>

                    <div wire:ignore x-data="{
                        iti: null,
                        init() {
                            if (!window.intlTelInput) {
                                setTimeout(() => this.init(), 150);
                                return;
                            }
                            const el = this.$refs.input;
                            if (!el || el._iti) return;
                    
                            this.iti = window.intlTelInput(el, {
                                initialCountry: 'ph',
                                preferredCountries: ['ph'],
                                separateDialCode: true,
                                strictMode: true,
                                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js',
                            });
                            el._iti = this.iti;
                    
                            const initial = el.dataset.initial;
                            if (initial) this.iti.setNumber(initial);
                    
                            const sync = () => {
                                $wire.set('contact_number_2', this.iti.getNumber() || '');
                            };
                            el.addEventListener('input', sync);
                            el.addEventListener('blur', sync);
                            el.addEventListener('countrychange', sync);
                        }
                    }" x-init="init()">
                        <input x-ref="input" type="tel"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white text-sm focus:outline-none focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-1 focus:ring-[#123524] dark:focus:ring-[#D4A537] transition"
                            autocomplete="tel" inputmode="tel" data-initial="{{ $contact_number_2 }}">
                    </div>
                    @error('contact_number_2')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ========== HOME ADDRESS ========== --}}
            <div>
                <label
                    class="block text-xs text-black/60 dark:text-white/60 uppercase tracking-wide font-semibold mb-2">
                    Home Address
                </label>

                {{-- Address Type Toggle --}}
                <div class="mb-4">
                    <div class="flex flex-wrap gap-x-6 gap-y-2">
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
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ========== PHILIPPINES CASCADE ========== --}}
                @if ($address_type === 'philippines')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="region_code"
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition
                                @error('region_code') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                                <option value="">Select Region</option>
                                @foreach ($this->regions as $region)
                                    <option value="{{ $region->code }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            @error('region_code')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                Province <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="province_code" @disabled(!$region_code)
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition disabled:opacity-50 disabled:cursor-not-allowed
                                @error('province_code') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                                <option value="">Select Province</option>
                                @foreach ($this->provinces as $province)
                                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('province_code')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                City / Municipality <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="city_code" @disabled(!$province_code)
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition disabled:opacity-50 disabled:cursor-not-allowed
                                @error('city_code') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                                <option value="">Select City / Municipality</option>
                                @foreach ($this->cities as $city)
                                    <option value="{{ $city->code }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_code')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="barangay_code" @disabled(!$city_code)
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white focus:outline-none focus:ring-1 transition disabled:opacity-50 disabled:cursor-not-allowed
                                @error('barangay_code') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                                <option value="">Select Barangay</option>
                                @foreach ($this->barangays as $barangay)
                                    <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                                @endforeach
                            </select>
                            @error('barangay_code')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- ========== ABROAD FIELDS ========== --}}
                @if ($address_type === 'abroad')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="intl_country" maxlength="255"
                                placeholder="e.g. United Arab Emirates"
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                @error('intl_country') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                            @error('intl_country')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                State / Province / Emirate
                                <span
                                    class="text-black/40 dark:text-white/40 text-[10px] font-normal normal-case">(optional)</span>
                            </label>
                            <input type="text" wire:model="intl_state" maxlength="255"
                                placeholder="e.g. Dubai, California, Ontario"
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                @error('intl_state') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                            @error('intl_state')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="intl_city" maxlength="255"
                                placeholder="e.g. Dubai, Los Angeles"
                                class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                                @error('intl_city') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                            @error('intl_city')
                                <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- ========== STREET ADDRESS ========== --}}
                <div class="mt-5">
                    <label for="street_address"
                        class="block text-[11px] text-black/50 dark:text-white/50 font-semibold mb-1.5">
                        Street Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="street_address" wire:model="street_address" maxlength="255"
                        placeholder="{{ $address_type === 'philippines' ? 'House no., street, subdivision, etc.' : 'House no., street, apartment, etc.' }}"
                        class="w-full px-4 py-2.5 rounded-xl border bg-[#F1EFE7] dark:bg-[#3A3B3C] text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-1 transition
                        @error('street_address') border-red-400 dark:border-red-500/50 @else border-black/10 dark:border-white/10 focus:border-[#123524] dark:focus:border-[#D4A537] focus:ring-[#123524] dark:focus:ring-[#D4A537] @enderror">
                    @error('street_address')
                        <span class="block mt-1 text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ========== ACTIONS ========== --}}
            <div
                class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-4 border-t border-black/5 dark:border-white/10">
                <button type="submit" :disabled="avatarBusy" wire:loading.attr="disabled" wire:target="saveProfile"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveProfile">Save Changes</span>
                    <span wire:loading wire:target="saveProfile">Saving…</span>
                    <svg wire:loading.remove wire:target="saveProfile" class="w-4 h-4" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('alumni.profile') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-xl bg-white dark:bg-[#3A3B3C] border border-black/10 dark:border-white/10 text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition py-2.5 px-5">
                    Back
                </a>
            </div>
        </form>
    </div>

    @assets
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css" />
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>

        <style>
            .iti__flag.iti__ph {
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 8"><rect width="12" height="4" fill="%23003" /><rect y="4" width="12" height="4" fill="%23CE1126" /><polygon points="0,0 4,4 0,8" fill="%23FFF" /><circle cx="1.5" cy="4" r="0.8" fill="%23FCD116" /></svg>');
                background-position: 0 0;
                background-size: 100% 100%;
            }

            .dark .iti__selected-dial-code {
                color: #fff;
            }

            .dark .iti__country-list {
                background-color: #242526;
                color: #fff;
                border-color: rgba(255, 255, 255, 0.1);
            }

            .dark .iti__country-list .iti__country:hover,
            .dark .iti__country-list .iti__country.iti__highlight {
                background-color: #3A3B3C;
            }

            .dark .iti__dial-code {
                color: rgba(255, 255, 255, 0.6);
            }

            .dark .iti__divider {
                border-bottom-color: rgba(255, 255, 255, 0.1);
            }

            .dark .iti__arrow {
                border-top-color: #fff;
            }

            .dark .iti__arrow--up {
                border-bottom-color: #fff;
            }
        </style>
    @endassets
</div>
