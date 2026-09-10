<div class="min-h-screen bg-[#F7F5EF] py-12 px-4">
    <div class="max-w-2xl mx-auto">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                Alumni Tracer Study
            </h1>
            <p class="text-[#123524]/60 mt-2">Section {{ $step }} of {{ $totalSteps }}</p>
        </div>

        <!-- Progress bar -->
        <div class="flex items-center gap-2 mb-8">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i <= $step ? 'bg-[#123524]' : 'bg-[#123524]/15' }}"></div>
            @endfor
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[#123524]/10 p-8">

            <!-- STEP 1: Personal Information -->
            @if ($step === 1)
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 1 of 4</span>
                    <h2 class="text-xl font-bold text-[#123524] mt-3">Personal Information</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Sex <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="gender" value="Male" class="text-[#123524] focus:ring-[#D4A537]">
                                <span class="text-sm text-[#123524]">Male</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="gender" value="Female" class="text-[#123524] focus:ring-[#D4A537]">
                                <span class="text-sm text-[#123524]">Female</span>
                            </label>
                        </div>
                        @error('gender') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Mobile Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="phone_number_1" placeholder="09XX XXX XXXX"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        @error('phone_number_1') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                        <textarea wire:model="current_address" rows="3" placeholder="House No., Street, Barangay, City/Municipality"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"></textarea>
                        @error('current_address') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Pin your location on the map <span class="text-red-500">*</span></label>
                        <div wire:ignore class="h-64 w-full rounded-xl border border-[#123524]/15 overflow-hidden" id="map"></div>
                        <p class="text-xs text-[#123524]/60 text-center">Drag the marker to your exact location</p>
                    </div>
                </div>
                </div>
            @endif

            <!-- STEP 2: Civil Status & Program -->
            @if ($step === 2)
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 2 of 4</span>
                    <h2 class="text-xl font-bold text-[#123524] mt-3">Civil Status & Educational Background</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Civil Status <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @foreach (['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated', 'single-parent' => 'Single Parent'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="civil_status" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
                                    <span class="text-sm text-[#123524]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('civil_status') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">College Program/Degree Completed <span class="text-red-500">*</span></label>
                        <select wire:model="course_id"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
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
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
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
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 3 of 4</span>
                    <h2 class="text-xl font-bold text-[#123524] mt-3">Employment Data</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Current Employment Status <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @foreach (['employed' => 'Employed', 'unemployed' => 'Unemployed', 'self-employed' => 'Self-employed', 'other' => 'Other'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="employment_status" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
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
                                class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Are you employed in a field related to your degree?</label>
                            <div class="space-y-2">
                                @foreach (['yes' => 'Yes', 'no' => 'No', 'partially-related' => 'Partially related'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="employed_related_to_degree" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of Employment</label>
                            <div class="space-y-2">
                                @foreach (['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractual-project-based' => 'Contractual/Project-based', 'freelance' => 'Freelance', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="employment_type" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Type of Organization/Company</label>
                            <div class="space-y-2">
                                @foreach (['private-company' => 'Private company', 'government-agency' => 'Government agency', 'non-government-organization' => 'Non-government organization', 'educational-institution' => 'Educational institution', 'self-employed-business' => 'Self-employed/business', 'other' => 'Other'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="organization_type" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
                                        <span class="text-sm text-[#123524]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">Current Area of Employment</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="employment_area" value="philippines" class="text-[#123524] focus:ring-[#D4A537]">
                                    <span class="text-sm text-[#123524]">Philippines</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="employment_area" value="abroad" class="text-[#123524] focus:ring-[#D4A537]">
                                    <span class="text-sm text-[#123524]">Abroad</span>
                                </label>
                            </div>
                        </div>

                        @if ($employment_area === 'abroad')
                            <div>
                                <label class="block text-sm font-semibold text-[#123524] mb-2">If abroad, please specify the country</label>
                                <input type="text" wire:model="abroad_country"
                                    class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 text-[#123524] focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                                @error('abroad_country') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">How long after graduation did you obtain your first job?</label>
                            <div class="space-y-2">
                                @foreach (['1-3-months' => '1-3 Months', '4-6-months' => '4-6 Months', 'more-than-6-months' => 'More than 6 Months', 'more-than-1-year' => 'More than 1 year', 'not-yet-employed' => 'I have not yet been employed'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="months_to_first_job" value="{{ $value }}" class="text-[#123524] focus:ring-[#D4A537]">
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
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 bg-[#123524] text-white text-xs font-bold rounded-full">Section 4 of 4</span>
                    <h2 class="text-xl font-bold text-[#123524] mt-3">Further Studies & Career Development</h2>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-2">Have you pursued further studies after graduating from CSAV? <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="1" class="text-[#123524] focus:ring-[#D4A537]">
                                <span class="text-sm text-[#123524]">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="is_pursued_further_studies" value="0" class="text-[#123524] focus:ring-[#D4A537]">
                                <span class="text-sm text-[#123524]">No</span>
                            </label>
                        </div>
                    </div>

                    @if ($is_pursued_further_studies)
                        <div>
                            <label class="block text-sm font-semibold text-[#123524] mb-2">If yes, what level of study are you currently pursuing or have completed? <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                @foreach (['Certificate', 'Bachelor', 'Master', 'Post Doctorate'] as $level)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="level_of_study" value="{{ $level }}" class="text-[#123524] focus:ring-[#D4A537]">
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
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-[#123524]/10">
                @if ($step > 1)
                    <button type="button" wire:click="previousStep"
                        class="px-6 py-2.5 rounded-xl border border-[#123524]/20 text-[#123524] font-semibold hover:bg-[#123524]/5 transition">
                        Back
                    </button>
                @else
                    <span></span>
                @endif

                @if ($step < $totalSteps)
                    <button type="button" wire:click="nextStep"
                        class="px-6 py-2.5 rounded-xl bg-[#123524] hover:bg-[#0d2819] text-white font-semibold shadow-md transition">
                        Next
                    </button>
                @else
                    <button type="button" wire:click="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#D4A537] hover:bg-[#bf9330] text-[#123524] font-bold shadow-md transition">
                        Submit
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Map Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #map { z-index: 1; }
</style>

<script>
    document.addEventListener('livewire:init', () => {
        const map = L.map('map').setView([10.45, 123.88], 13); // Default to Victorias/ Negros Occidental area

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([10.45, 123.88], { draggable: true }).addTo(map);

        marker.on('dragend', function (event) {
            const position = marker.getLatLng();
            @this.set('latitude', position.lat);
            @this.set('longitude', position.lng);
        });

        // If we have existing coordinates, move the marker
        @if($latitude && $longitude)
            marker.setLatLng([{{ $latitude }}, {{ $longitude }}]);
            map.setView([{{ $latitude }}, {{ $longitude }}], 13);
        @endif
    });
</script>
