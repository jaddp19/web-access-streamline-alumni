<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen py-8 px-4">

    <div class="max-w-2xl mx-auto">

        {{-- ========== HEADER ========== --}}
        <div class="mb-6">
            <a href="{{ route('alumni.profile') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#1877F2] hover:underline mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to profile
            </a>
            <h1 class="text-2xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">
                Add Work Experience
            </h1>
            <p class="text-sm text-black/60 dark:text-white/60 mt-1">
                Tell us about your current or previous employment.
            </p>
        </div>

        {{-- ========== CARD ========== --}}
        <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5 sm:p-6">

            {{-- Top-of-card flashes --}}
            @if (session('error'))
                <div
                    class="mb-5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 text-sm font-medium flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('company_created'))
                <div
                    class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('company_created') }}</span>
                </div>
            @endif

            <form wire:submit="saveWorkHistory" class="space-y-5">

                {{-- Position --}}
                <div>
                    <label for="work_name" class="block text-sm font-semibold text-black dark:text-white mb-2">
                        Job Title / Position <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="work_name" wire:model="work_name" maxlength="255"
                        placeholder="e.g. Junior Software Developer"
                        class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#3A3B3C] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                        @error('work_name') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                    @error('work_name')
                        <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Company --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="company_id" class="block text-sm font-semibold text-black dark:text-white">
                            Company <span class="text-red-500">*</span>
                        </label>
                        <button type="button" wire:click="toggleNewCompanyForm"
                            class="text-xs font-semibold text-[#1877F2] hover:underline">
                            {{ $showNewCompanyForm ? 'Cancel' : '+ Add new company' }}
                        </button>
                    </div>

                    @if ($showNewCompanyForm)
                        {{-- ========== NEW COMPANY FORM ========== --}}
                        <div
                            class="space-y-4 p-4 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10">

                            {{-- Logo --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-black/70 dark:text-white/70 mb-2 uppercase tracking-wide">
                                    Company Logo
                                </label>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-16 h-16 rounded-xl bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if ($new_company_logo)
                                            <img src="{{ $new_company_logo->temporaryUrl() }}" alt="Preview"
                                                loading="lazy" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-black/20 dark:text-white/20" fill="none"
                                                stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <input type="file" wire:model="new_company_logo"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="block w-full text-xs text-black/70 dark:text-white/70
                                                file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                                                file:text-xs file:font-semibold
                                                file:bg-[#1877F2] file:text-white
                                                hover:file:bg-[#166FE5]
                                                file:cursor-pointer cursor-pointer">
                                        <p class="text-[11px] text-black/40 dark:text-white/40 mt-1">
                                            JPG, PNG, or WebP. Max 2MB.
                                        </p>
                                    </div>
                                </div>
                                <div wire:loading wire:target="new_company_logo"
                                    class="mt-2 text-xs text-[#1877F2] font-semibold">
                                    Uploading…
                                </div>
                                @error('new_company_logo')
                                    <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Name --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-black/70 dark:text-white/70 mb-1.5 uppercase tracking-wide">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="new_company_name" maxlength="255"
                                    placeholder="e.g. Acme Corporation"
                                    class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                                    @error('new_company_name') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                @error('new_company_name')
                                    <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-black/70 dark:text-white/70 mb-2 uppercase tracking-wide">
                                    Address
                                </label>

                                {{-- Address Type Toggle --}}
                                <div class="flex flex-wrap gap-x-5 gap-y-2 mb-3">
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[36px]">
                                        <input type="radio" wire:model.live="new_company_address_type"
                                            value="philippines" class="text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                                        <span class="text-sm text-black dark:text-white">Philippines</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer min-h-[36px]">
                                        <input type="radio" wire:model.live="new_company_address_type" value="abroad"
                                            class="text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                                        <span class="text-sm text-black dark:text-white">Abroad</span>
                                    </label>
                                </div>

                                {{-- ===== PHILIPPINES ===== --}}
                                @if ($new_company_address_type === 'philippines')
                                    <div class="space-y-3">

                                        {{-- Region --}}
                                        <select wire:model.live="new_company_region_code"
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                @error('new_company_region_code') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                            <option value="">Select Region *</option>
                                            @foreach ($this->regions as $region)
                                                <option value="{{ $region->code }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_company_region_code')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        {{-- Province --}}
                                        <select wire:model.live="new_company_province_code"
                                            @disabled(!$new_company_region_code)
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed
                @error('new_company_province_code') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                            <option value="">Select Province *</option>
                                            @foreach ($this->newCompanyProvinces as $province)
                                                <option value="{{ $province->code }}">{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_company_province_code')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        {{-- City --}}
                                        <select wire:model="new_company_city_code" @disabled(!$new_company_province_code)
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed
                @error('new_company_city_code') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                            <option value="">Select City / Municipality *</option>
                                            @foreach ($this->newCompanyCities as $city)
                                                <option value="{{ $city->code }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_company_city_code')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        {{-- Street Address --}}
                                        <input type="text" wire:model="new_company_street_address" maxlength="500"
                                            placeholder="Street address (optional)"
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                @error('new_company_street_address') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                        @error('new_company_street_address')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                {{-- ===== ABROAD ===== --}}
                                @if ($new_company_address_type === 'abroad')
                                    <div class="space-y-3">

                                        {{-- Country --}}
                                        <input type="text" wire:model="new_company_intl_country" maxlength="255"
                                            placeholder="Country * (e.g. United Arab Emirates)"
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                @error('new_company_intl_country') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                        @error('new_company_intl_country')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        {{-- State --}}
                                        <input type="text" wire:model="new_company_intl_state" maxlength="255"
                                            placeholder="State / Province / Emirate (optional)"
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                @error('new_company_intl_state') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                        @error('new_company_intl_state')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        {{-- City --}}
                                        <input type="text" wire:model="new_company_intl_city" maxlength="255"
                                            placeholder="City * (e.g. Dubai, Los Angeles)"
                                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                @error('new_company_intl_city') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                        @error('new_company_intl_city')
                                            <span class="block mt-1 text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Description --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-black/70 dark:text-white/70 mb-1.5 uppercase tracking-wide">
                                    Description
                                </label>
                                <textarea wire:model="new_company_desc" rows="3" maxlength="2000"
                                    placeholder="Short description — industry, size, focus…"
                                    class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition resize-y
                                    @error('new_company_desc') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror"></textarea>
                                @error('new_company_desc')
                                    <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Create button --}}
                            <div class="flex justify-end pt-2">
                                <button type="button" wire:click="createCompany" wire:loading.attr="disabled"
                                    wire:target="createCompany,new_company_logo"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#123524] hover:bg-[#0d2819] text-white text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading.remove wire:target="createCompany" class="w-4 h-4"
                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <svg wire:loading wire:target="createCompany" class="w-4 h-4 animate-spin"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="createCompany">Create Company</span>
                                    <span wire:loading wire:target="createCompany">Creating…</span>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- ========== COMPANY SELECT ========== --}}
                        <select wire:model="company_id" id="company_id"
                            class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#3A3B3C] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                            @error('company_id') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                            <option value="">Select a company</option>
                            @foreach ($this->companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                {{-- Date Hired --}}
                <div>
                    <label for="date_hired" class="block text-sm font-semibold text-black dark:text-white mb-2">
                        Date Hired <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date_hired" wire:model="date_hired"
                        max="{{ now()->format('Y-m-d') }}"
                        class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#3A3B3C] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                        @error('date_hired') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                    @error('date_hired')
                        <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Current Job Toggle --}}
                <label
                    class="flex items-start gap-3 cursor-pointer p-3 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10">
                    <input type="checkbox" wire:model.live="is_current_job"
                        class="mt-0.5 rounded border-black/20 dark:border-white/20 text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                    <div class="flex-1">
                        <span class="block text-sm font-semibold text-black dark:text-white">
                            This is my current job
                        </span>
                        <span class="block text-xs text-black/50 dark:text-white/50 mt-0.5">
                            Leave unchecked if this is a previous position.
                        </span>
                    </div>
                </label>

                {{-- ========== EMPLOYMENT DETAILS ========== --}}
                @if ($is_current_job)
                    <div
                        class="space-y-5 p-4 sm:p-5 rounded-xl bg-[#F0F2F5] dark:bg-[#3A3B3C]/60 border border-black/5 dark:border-white/10">

                        <h3 class="text-sm font-bold text-black dark:text-white uppercase tracking-wide">
                            Employment Details
                        </h3>

                        {{-- Civil Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                Civil Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="civil_status"
                                class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                                @error('civil_status') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                <option value="">Select civil status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                                <option value="single-parent">Single Parent</option>
                            </select>
                            @error('civil_status')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Related to Degree --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                Is your job related to your degree? <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach (['yes' => 'Yes', 'no' => 'No', 'partially-related' => 'Partially related'] as $value => $label)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg bg-white dark:bg-[#242526] border border-black/10 dark:border-white/10 hover:border-[#1877F2] transition min-h-[42px]">
                                        <input type="radio" wire:model="employed_related_to_degree"
                                            value="{{ $value }}"
                                            class="text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                                        <span class="text-sm text-black dark:text-white">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('employed_related_to_degree')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Employment Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                Type of Employment <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="employment_type"
                                class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                                @error('employment_type') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                <option value="">Select type</option>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                                <option value="contractual-project-based">Contractual / Project-based</option>
                                <option value="freelance">Freelance</option>
                                <option value="other">Other</option>
                            </select>
                            @error('employment_type')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Organization Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                Type of Organization <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="organization_type"
                                class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                                @error('organization_type') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                <option value="">Select organization type</option>
                                <option value="private-company">Private company</option>
                                <option value="government-agency">Government agency</option>
                                <option value="non-government-organization">Non-government organization</option>
                                <option value="educational-institution">Educational institution</option>
                                <option value="self-employed-business">Self-employed / Business</option>
                                <option value="other">Other</option>
                            </select>
                            @error('organization_type')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Employment Area --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                Employment Area <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer min-h-[42px]">
                                    <input type="radio" wire:model.live="employment_area" value="philippines"
                                        class="text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                                    <span class="text-sm text-black dark:text-white">Philippines</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer min-h-[42px]">
                                    <input type="radio" wire:model.live="employment_area" value="abroad"
                                        class="text-[#1877F2] focus:ring-[#1877F2] h-4 w-4">
                                    <span class="text-sm text-black dark:text-white">Abroad</span>
                                </label>
                            </div>
                            @error('employment_area')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Abroad Country --}}
                        @if ($employment_area === 'abroad')
                            <div>
                                <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="abroad_country" maxlength="255"
                                    placeholder="e.g. United Arab Emirates"
                                    class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm placeholder:text-black/40 dark:placeholder:text-white/40 focus:outline-none focus:ring-2 focus:border-transparent transition
                                    @error('abroad_country') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                @error('abroad_country')
                                    <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Months to First Job --}}
                        <div>
                            <label class="block text-sm font-semibold text-black dark:text-white mb-2">
                                How long after graduation did you get your first job? <span
                                    class="text-red-500">*</span>
                            </label>
                            <select wire:model="months_to_first_job"
                                class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-[#242526] text-black dark:text-white text-sm focus:outline-none focus:ring-2 focus:border-transparent transition
                                @error('months_to_first_job') border-red-400 focus:ring-red-400/50 @else border-black/10 dark:border-white/10 focus:ring-[#1877F2] @enderror">
                                <option value="">Select duration</option>
                                <option value="1-3-months">1 - 3 Months</option>
                                <option value="4-6-months">4 - 6 Months</option>
                                <option value="more-than-6-months">More than 6 Months</option>
                                <option value="more-than-1-year">More than 1 Year</option>
                            </select>
                            @error('months_to_first_job')
                                <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- ========== ACTIONS ========== --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-black/5 dark:border-white/10">
                    <a href="{{ route('alumni.profile') }}"
                        class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl border border-black/10 dark:border-white/10 text-sm font-semibold text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">
                        Cancel
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="saveWorkHistory"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1877F2] hover:bg-[#166FE5] text-white text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveWorkHistory">Save Work Experience</span>
                        <span wire:loading wire:target="saveWorkHistory">Saving…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
