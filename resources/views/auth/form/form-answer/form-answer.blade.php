<div class="min-h-screen bg-[#F7F5EF] py-6 px-3 sm:py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl mx-auto">

        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#123524] leading-tight" style="font-family: 'Fraunces', serif;">
                Alumni Tracer Study
            </h1>
            <p class="text-sm sm:text-base text-[#123524]/60 mt-1.5 sm:mt-2">Section {{ $step }} of {{ $totalSteps }}</p>
        </div>

        <!-- Progress bar -->
        <div class="flex items-center gap-1.5 sm:gap-2 mb-6 sm:mb-8">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i <= $step ? 'bg-[#123524]' : 'bg-[#123524]/15' }}"></div>
            @endfor
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[#123524]/10 p-4 sm:p-6 md:p-8">

            <!-- STEP 1: Personal Information -->
            @if ($step === 1)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 1 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Personal Information</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Sex <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                                <input type="radio" wire:model="gender" value="Male" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Male</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer min-h-[44px]">
                                <input type="radio" wire:model="gender" value="Female" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Female</span>
                            </label>
                        </div>
                        @error('gender') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- ===== PHONE INPUT ===== --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>

                        <div wire:ignore class="phone-wrapper">
                            <input
                                type="tel"
                                id="phone-input"
                                class="phone-input"
                                autocomplete="tel"
                                inputmode="tel"
                            >
                        </div>

                        @error('phone_number_1')
                            <span class="text-red-500 text-sm mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cascading Address -->
                    <div class="space-y-4 pt-2 border-t border-[#123524]/10">
                        <label class="block text-sm font-semibold text-[#123524]">Current Address <span class="text-red-500">*</span></label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-[#123524]/60 uppercase tracking-wide font-semibold mb-1.5">Region</label>
                                <select wire:model.live="regionCode"
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                    <option value="">Select Region</option>
                                    @foreach ($this->regions as $region)
                                        <option value="{{ $region->code }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                                @error('regionCode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-[#123524]/60 uppercase tracking-wide font-semibold mb-1.5">Province / District</label>
                                <select wire:model.live="provinceCode" @disabled(!$regionCode)
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition disabled:opacity-50">
                                    <option value="">Select Province</option>
                                    @foreach ($this->provinces as $province)
                                        <option value="{{ $province->code }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                                @error('provinceCode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-[#123524]/60 uppercase tracking-wide font-semibold mb-1.5">City / Municipality</label>
                                <select wire:model.live="cityCode" @disabled(!$provinceCode)
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition disabled:opacity-50">
                                    <option value="">Select City/Municipality</option>
                                    @foreach ($this->cities as $city)
                                        <option value="{{ $city->code }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('cityCode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs text-[#123524]/60 uppercase tracking-wide font-semibold mb-1.5">Barangay</label>
                                <select wire:model.live="barangayCode" @disabled(!$cityCode)
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition disabled:opacity-50">
                                    <option value="">Select Barangay</option>
                                    @foreach ($this->barangays as $barangay)
                                        <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                                    @endforeach
                                </select>
                                @error('barangayCode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-[#123524]/60 uppercase tracking-wide font-semibold mb-1.5">House No. / Street</label>
                            <input type="text" wire:model="street_address" placeholder="e.g. Blk 4 Lot 12, Rizal St."
                                class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            @error('street_address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 2: Civil Status & Program -->
            @if ($step === 2)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 2 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Civil Status & Educational Background</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Civil Status <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 gap-2">
                            @foreach (['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated', 'single-parent' => 'Single Parent'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model="civil_status" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('civil_status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">College Program/Degree Completed <span class="text-red-500">*</span></label>
                        <select wire:model="course_id"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            <option value="">Select program</option>
                            @foreach ($courses as $id => $title)
                                <option value="{{ $id }}">{{ $title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Year Graduated <span class="text-red-500">*</span></label>
                        <select wire:model="batch_id"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                            <option value="">Select batch/year</option>
                            @foreach ($batches as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('batch_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <!-- STEP 3: Employment Data -->
            @if ($step === 3)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 3 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Employment Data</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Current Employment Status <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach (['employed' => 'Employed', 'unemployed' => 'Unemployed', 'self-employed' => 'Self-employed', 'other' => 'Other'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_status" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('employment_status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if ($employment_status === 'employed')
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Current Job Position</label>
                            <input type="text" wire:model="current_job_position"
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Are you employed in a field related to your degree?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach (['yes' => 'Yes', 'no' => 'No', 'partially-related' => 'Partially related'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="employed_related_to_degree" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of Employment</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractual-project-based' => 'Contractual/Project-based', 'freelance' => 'Freelance', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="employment_type" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of Organization/Company</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['private-company' => 'Private company', 'government-agency' => 'Government agency', 'non-government-organization' => 'Non-government organization', 'educational-institution' => 'Educational institution', 'self-employed-business' => 'Self-employed/business', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="organization_type" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Current Area of Employment</label>
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_area" value="philippines" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">Philippines</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                    <input type="radio" wire:model.live="employment_area" value="abroad" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                    <span class="text-sm text-[#123524]">Abroad</span>
                                </label>
                            </div>
                        </div>

                        @if ($employment_area === 'abroad')
                            <div>
                                <label class="block text-sm font-semibold text-[#123524] mb-2">If abroad, please specify the country</label>
                                <input type="text" wire:model="abroad_country"
                                    class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                @error('abroad_country') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">How long after graduation did you obtain your first job?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['1-3-months' => '1-3 Months', '4-6-months' => '4-6 Months', 'more-than-6-months' => 'More than 6 Months', 'more-than-1-year' => 'More than 1 year', 'not-yet-employed' => 'I have not yet been employed'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="months_to_first_job" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- STEP 4: Further Studies -->
            @if ($step === 4)
                <div class="mb-5 sm:mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 4 of 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#123524] mt-3">Further Studies & Career Development</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Have you pursued further studies after graduating from CSAV? <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="1" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="0" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                <span class="text-sm text-[#123524]">No</span>
                            </label>
                        </div>
                    </div>

                    @if ($is_pursued_further_studies)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">If yes, what level of study are you currently pursuing or have completed? <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (['Certificate', 'Bachelor', 'Master', 'Post Doctorate'] as $level)
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[40px]">
                                        <input type="radio" wire:model="level_of_study" value="{{ $level }}" class="text-[#123524] focus:ring-[#D4A537] h-4 w-4">
                                        <span class="text-sm text-[#123524]">{{ $level }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('level_of_study') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            @endif

            <!-- Navigation -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-8 pt-6 border-t border-[#123524]/10">
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

    {{-- ===== PHONE INPUT STYLES + SCRIPT ===== --}}
    <style>
        .phone-wrapper .iti {
            width: 100%;
            display: block;
        }

        .phone-wrapper .iti__tel-input {
            width: 100% !important;
            height: 48px !important;
            padding: 0.75rem 1rem !important;
            padding-left: 92px !important;
            border-radius: 0.75rem !important;
            border: 1px solid rgba(18, 53, 36, 0.15) !important;
            color: #123524 !important;
            background: #fff !important;
            font-size: 16px !important; /* prevents iOS auto-zoom on focus */
            transition: box-shadow 0.15s, border-color 0.15s;
        }

        .phone-wrapper .iti__tel-input:focus {
            outline: none !important;
            border-color: transparent !important;
            box-shadow: 0 0 0 2px #D4A537 !important;
        }

        .phone-wrapper .iti__selected-flag {
            padding: 0 8px 0 12px !important;
            border-radius: 0.75rem 0 0 0.75rem !important;
        }

        .phone-wrapper .iti__selected-flag:hover {
            background: rgba(18, 53, 36, 0.05) !important;
        }

        .phone-wrapper .iti__selected-dial-code {
            color: #123524 !important;
            font-weight: 500;
        }

        .iti__country-list {
            border-radius: 0.75rem !important;
            border: 1px solid rgba(18, 53, 36, 0.12) !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
            z-index: 9999 !important;
            max-width: 90vw;
        }

        @media (max-width: 480px) {
            .phone-wrapper .iti__tel-input {
                padding-left: 80px !important;
            }
        }
    </style>

    <script>
    document.addEventListener('livewire:init', () => {
        const ITI_JS    = 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js';
        const ITI_UTILS = 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js';

        let iti = null;

        function initPhone() {
            const input = document.querySelector('#phone-input');
            if (!input || input.closest('.iti')) return;

            if (iti) {
                try { iti.destroy(); } catch (e) {}
                iti = null;
            }

            iti = window.intlTelInput(input, {
                initialCountry: 'ph',
                preferredCountries: ['ph', 'us', 'ae', 'sg', 'jp', 'kr', 'gb'],
                separateDialCode: true,
                utilsScript: ITI_UTILS,
                autoPlaceholder: 'aggressive',
            });

            const sync = () => {
                if (!iti) return;
                const number = iti.getNumber();
                @this.set('phone_number_1', number || '');
            };

            input.addEventListener('input', sync);
            input.addEventListener('countrychange', sync);
            input.addEventListener('blur', sync);

            @if($phone_number_1)
                iti.setNumber(@js($phone_number_1));
            @endif
        }

        function boot() {
            if (window.intlTelInput) {
                initPhone();
            } else {
                const s = document.createElement('script');
                s.src = ITI_JS;
                s.onload = initPhone;
                document.head.appendChild(s);
            }
        }

        boot();

        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                const input = document.querySelector('#phone-input');
                if (input && !input.closest('.iti')) {
                    initPhone();
                }
            }, 60);
        });
    });
</script>
</div>
