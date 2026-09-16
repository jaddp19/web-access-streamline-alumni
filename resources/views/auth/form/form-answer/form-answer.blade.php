<div class="min-h-screen bg-[#F7F5EF] py-6 px-3 sm:py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl mx-auto">

        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#123524] leading-tight"
                style="font-family: 'Fraunces', serif;">
                Alumni Tracer Study
            </h1>
            <p class="text-sm sm:text-base text-[#123524]/60 mt-1.5 sm:mt-2">Section {{ $step }} of
                {{ $totalSteps }}</p>
        </div>

        <div class="flex items-center gap-2 mb-8">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i <= $step ? 'bg-[#123524]' : 'bg-[#123524]/15' }}"></div>
            @endfor
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[#123524]/10 p-4 sm:p-6 md:p-8">

            {{-- STEP 1: Personal Information --}}
            @if ($step === 1)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section
                        1 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Personal Information</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Sex <span
                                class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                                <input type="radio" wire:model="gender" value="Male"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Male</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                                <input type="radio" wire:model="gender" value="Female"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Female</span>
                            </label>
                        </div>
                        @error('gender')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ===== PHONE INPUT ===== --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">
                            Mobile Number <span class="text-red-500">*</span>
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
                                    $wire.set('phone_number_1', this.iti.getNumber() || '');
                                };
                                el.addEventListener('blur', sync);
                                el.addEventListener('countrychange', sync);
                            }
                        }" x-init="init()">
                            <input x-ref="input" type="tel" id="phone-input"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"
                                autocomplete="tel" inputmode="tel" data-initial="{{ $phone_number_1 }}">
                        </div>
                        @error('phone_number_1')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ===== STREET ADDRESS ===== --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Street Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" wire:model="street_address" placeholder="House No., Street, Purok"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        @error('street_address')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ===== REGION ===== --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Region <span
                                class="text-red-500">*</span></label>
                        <select wire:model.live="regionCode"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            <option value="">Select region</option>
                            @foreach ($this->regions as $region)
                                <option value="{{ $region->code }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('regionCode')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ===== PROVINCE ===== --}}
                    @if ($regionCode)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Province <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="provinceCode"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                <option value="">Select province</option>
                                @foreach ($this->provinces as $province)
                                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('provinceCode')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- ===== CITY / MUNICIPALITY ===== --}}
                    @if ($provinceCode)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">City / Municipality <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="cityCode"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                <option value="">Select city / municipality</option>
                                @foreach ($this->cities as $city)
                                    <option value="{{ $city->code }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('cityCode')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- ===== BARANGAY ===== --}}
                    @if ($cityCode)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Barangay <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="barangayCode"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                <option value="">Select barangay</option>
                                @foreach ($this->barangays as $barangay)
                                    <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                                @endforeach
                            </select>
                            @error('barangayCode')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 2: Civil Status & Program --}}
            @if ($step === 2)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section
                        2 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Civil Status & Educational Background
                    </h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Civil Status <span
                                class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 gap-2">
                            @foreach (['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated', 'single-parent' => 'Single Parent'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model="civil_status" value="{{ $value }}"
                                        class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('civil_status')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">College Program/Degree Completed
                            <span class="text-red-500">*</span></label>
                        <select wire:model="course_id"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            <option value="">Select program</option>
                            @foreach ($courses as $id => $title)
                                <option value="{{ $id }}">{{ $title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Year Graduated <span
                                class="text-red-500">*</span></label>
                        <select wire:model="batch_id"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            <option value="">Select batch/year</option>
                            @foreach ($batches as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('batch_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- STEP 3: Employment Data --}}
            @if ($step === 3)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section
                        3 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Employment Data</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Current Employment Status <span
                                class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach (['employed' => 'Employed', 'unemployed' => 'Unemployed', 'self-employed' => 'Self-employed', 'other' => 'Other'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_status"
                                        value="{{ $value }}"
                                        class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('employment_status')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($employment_status === 'employed')
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Current Job Position</label>
                            <input type="text" wire:model="current_job_position"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Are you employed in a field
                                related to your degree?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach (['yes' => 'Yes', 'no' => 'No', 'partially-related' => 'Partially related'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="employed_related_to_degree"
                                            value="{{ $value }}"
                                            class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('employed_related_to_degree')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of Employment</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractual-project-based' => 'Contractual/Project-based', 'freelance' => 'Freelance', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="employment_type"
                                            value="{{ $value }}"
                                            class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('employment_type')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of
                                Organization/Company</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['private-company' => 'Private company', 'government-agency' => 'Government agency', 'non-government-organization' => 'Non-government organization', 'educational-institution' => 'Educational institution', 'self-employed-business' => 'Self-employed/business', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="organization_type"
                                            value="{{ $value }}"
                                            class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('organization_type')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Current Area of
                                Employment</label>
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_area" value="philippines"
                                        class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">Philippines</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_area" value="abroad"
                                        class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">Abroad</span>
                                </label>
                            </div>
                            @error('employment_area')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($employment_area === 'abroad')
                            <div>
                                <label class="block text-sm font-semibold text-[#123524] mb-2">If abroad, please
                                    specify the country</label>
                                <input type="text" wire:model="abroad_country"
                                    class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                @error('abroad_country')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">How long after graduation
                                did you obtain your first job?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['1-3-months' => '1-3 Months', '4-6-months' => '4-6 Months', 'more-than-6-months' => 'More than 6 Months', 'more-than-1-year' => 'More than 1 year', 'not-yet-employed' => 'I have not yet been employed'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="months_to_first_job"
                                            value="{{ $value }}"
                                            class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('months_to_first_job')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 4: Further Studies --}}
            @if ($step === 4)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section
                        4 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Further Studies & Career Development
                    </h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Have you pursued further studies
                            after graduating from CSAV? <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="1"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="0"
                                    class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">No</span>
                            </label>
                        </div>
                        @error('is_pursued_further_studies')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($is_pursued_further_studies)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">If yes, what level of study
                                are you currently pursuing or have completed? <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['Certificate', 'Bachelor', 'Master', 'Post Doctorate'] as $level)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="level_of_study"
                                            value="{{ $level }}"
                                            class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $level }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('level_of_study')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endif

            {{-- Navigation --}}
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-[#123524]/10">
                @if ($step > 1)
                    <button type="button" wire:click="previousStep"
                        class="w-full sm:w-auto px-6 py-3 sm:py-2.5 rounded-xl border border-[#123524]/20 text-[#123524] font-semibold hover:bg-[#123524]/5 active:bg-[#123524]/10 transition">
                        Back
                    </button>
                @else
                    <span class="hidden sm:block"></span>
                @endif

                @if ($step < $totalSteps)
                    <button type="button" wire:click="nextStep"
                        class="w-full sm:w-auto px-6 py-3 sm:py-2.5 rounded-xl bg-[#123524] hover:bg-[#0d2819] active:bg-[#0a2013] text-white font-semibold shadow-md transition">
                        Next
                    </button>
                @else
                    <button type="button" wire:click="submit"
                        class="w-full sm:w-auto px-6 py-3 sm:py-2.5 rounded-xl bg-[#D4A537] hover:bg-[#bf9330] active:bg-[#a97f28] text-[#123524] font-bold shadow-md transition">
                        Submit
                    </button>
                @endif
            </div>
        </div>
    </div>

    @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css" />
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>

        <style>
            /* Replace the PH flag sprite with a correct SVG */
            .iti__flag.iti__ph {
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 8"><rect width="12" height="4" fill="%23003" /><rect y="4" width="12" height="4" fill="%23CE1126" /><polygon points="0,0 4,4 0,8" fill="%23FFF" /><circle cx="1.5" cy="4" r="0.8" fill="%23FCD116" /></svg>');
                background-position: 0 0;
                background-size: 100% 100%;
            }
        </style>
    @endassets

    @script
        <script>
            const initMap = () => {
                const el = document.getElementById('map');
                if (!el || typeof L === 'undefined' || el._leaflet_id) return;

                const lat = Number($wire.latitude) || 10.45;
                const lng = Number($wire.longitude) || 123.88;

                const map = L.map(el).setView([lat, lng], 13);
                const marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', (event) => {
                    const pos = event.target.getLatLng();
                    $wire.set('latitude', pos.lat);
                    $wire.set('longitude', pos.lng);
                });

                queueMicrotask(() => map.invalidateSize());
            };

            initMap();

            $wire.on('step-changed', () => {
                queueMicrotask(initMap);
            });
        </script>
    @endscript
</div>
