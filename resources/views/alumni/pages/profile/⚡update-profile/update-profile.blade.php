<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER (BENTO STYLE) ========== -->
    <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
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
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Personal Information</h1>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 font-semibold rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (!$hasProfile)
        <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 font-semibold rounded-xl p-4 text-sm">
            You don't have a profile yet. Fill out the form below to set it up.
        </div>
    @endif

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="saveProfile" class="space-y-5">

            <!-- Avatar -->
            <div class="flex items-center gap-5">
                <img src="{{ $avatarFile ? $avatarFile->temporaryUrl() : ($currentAvatar ? Storage::url($currentAvatar) : 'https://ui-avatars.com/api/?name=' . urlencode($name)) }}"
                    class="w-20 h-20 rounded-2xl object-cover border border-black/10 shrink-0">

                <div class="flex-1">
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Avatar</label>
                    <input type="file" wire:model="avatarFile" accept="image/*"
                        class="block w-full text-sm text-black/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#D4A537] file:text-[#123524] hover:file:bg-[#E5B94A] file:cursor-pointer cursor-pointer">
                    <div wire:loading wire:target="avatarFile" class="text-xs text-black/40 mt-1">Uploading...</div>
                    @error('avatarFile')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Name + Email -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Name</label>
                    <input type="text" wire:model.defer="name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Gender</label>
                <select wire:model.defer="gender"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                @error('gender')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone Numbers (intl-tel-input) -->
            <div class="grid sm:grid-cols-2 gap-5">

                {{-- Primary contact --}}
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">
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
                            el.addEventListener('blur', sync);
                            el.addEventListener('countrychange', sync);
                        }
                    }" x-init="init()">
                        <input x-ref="input" type="tel"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black text-sm focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                            autocomplete="tel" inputmode="tel" data-initial="{{ $contact_number_1 }}">
                    </div>
                    @error('contact_number_1')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alternate contact --}}
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">
                        Contact Number 2 <span class="text-black/40 text-[10px] font-normal normal-case">(optional)</span>
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
                            el.addEventListener('blur', sync);
                            el.addEventListener('countrychange', sync);
                        }
                    }" x-init="init()">
                        <input x-ref="input" type="tel"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black text-sm focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                            autocomplete="tel" inputmode="tel" data-initial="{{ $contact_number_2 }}">
                    </div>
                    @error('contact_number_2')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Philippine Address (cascading dropdowns) -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Home Address</label>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] text-black/50 font-semibold mb-1.5">Region</label>
                        <select wire:model.live="region_code"
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                            <option value="">Select Region</option>
                            @foreach ($this->regions as $region)
                                <option value="{{ $region->code }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] text-black/50 font-semibold mb-1.5">Province</label>
                        <select wire:model.live="province_code" @disabled(!$region_code)
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select Province</option>
                            @foreach ($this->provinces as $province)
                                <option value="{{ $province->code }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] text-black/50 font-semibold mb-1.5">City / Municipality</label>
                        <select wire:model.live="city_code" @disabled(!$province_code)
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select City / Municipality</option>
                            @foreach ($this->cities as $city)
                                <option value="{{ $city->code }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] text-black/50 font-semibold mb-1.5">Barangay</label>
                        <select wire:model.live="barangay_code" @disabled(!$city_code)
                            class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select Barangay</option>
                            @foreach ($this->barangays as $barangay)
                                <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        @error('barangay_code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="block text-[11px] text-black/50 font-semibold mb-1.5">Street Address</label>
                    <input type="text" wire:model.defer="street_address"
                        placeholder="House no., street, subdivision, etc."
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('street_address')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if ($address)
                    <div class="mt-4">
                        <label class="block text-[11px] text-black/50 font-semibold mb-1.5">Saved Location</label>
                        <p class="text-sm text-[#123524] bg-[#D4A537]/15 border border-[#D4A537]/30 rounded-xl px-4 py-2.5 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>{{ $address }}</span>
                        </p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Save Changes
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
                <a href="{{ route('alumni.profile') }}"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-white border border-black/10 text-black hover:bg-black/5 transition py-2.5 px-5">
                    Back
                </a>
            </div>
        </form>

        @if (session('success'))
            <div class="mt-6 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
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
        </style>
    @endassets
</div>