<div class="min-h-screen flex bg-[#F7F5EF]">

    <!-- ========== LEFT PANEL — BRANDING ========== -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#123524]">

        <div class="absolute inset-0">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#D4A537]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] bg-[#D4A537]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image: radial-gradient(#D4A537 1px, transparent 1px); background-size: 28px 28px;"></div>
        </div>

        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRC0rZblAbfgOgTt6ujr71K-2jf9gY65zHSUOVdPIFeKy1BENm-131U9vw&s=10"
             alt="Campus"
             class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay">

        <div class="relative z-10 flex flex-col w-full px-14 py-16">

            <a href="/" class="flex items-center gap-x-3">
                <div class="w-12 h-12 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md shrink-0">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                         alt="CSAV Logo"
                         class="w-full h-full object-contain">
                </div>
                <span class="text-white font-bold text-lg" style="font-family: 'Fraunces', serif;">
                    Colegio de Sta. Ana de Victorias
                </span>
            </a>

            <div class="space-y-8 max-w-md mt-14 xl:mt-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 rounded-full backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4A537]"></span>
                    <span class="text-white/90 text-xs font-bold tracking-[0.2em]">CSAV ALUMNI SYSTEM</span>
                </div>

                <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight" style="font-family: 'Fraunces', serif;">
                    Join the
                    <span class="text-[#D4A537]">CSAV Alumni Network</span>
                </h1>

                <p class="text-white/70 leading-relaxed">
                    Create your alumni account to update your career profile, stay connected with your batch, and help shape the future of Colegio de Sta. Ana de Victorias.
                </p>

                <ul class="space-y-3 text-sm text-white/70">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded-full bg-[#D4A537]/20 text-[#D4A537] flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        Build your alumni profile and tracer record
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded-full bg-[#D4A537]/20 text-[#D4A537] flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        Stay updated on events and announcements
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded-full bg-[#D4A537]/20 text-[#D4A537] flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        Connect with fellow CSAV graduates
                    </li>
                </ul>
            </div>

            <p class="mt-auto pt-10 text-xs text-white/40">
                Your information is handled under the CSAV Privacy Policy.
            </p>
        </div>
    </div>
    <!-- ========== END LEFT PANEL ========== -->

    <!-- ========== RIGHT PANEL — FORM ========== -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 sm:px-12 py-12 relative">

        <div class="absolute inset-0 -z-10 lg:hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-[#123524]/5 via-transparent to-[#D4A537]/10"></div>
        </div>

        <div class="w-full max-w-md">

            <div class="flex lg:hidden justify-center mb-8">
                <a href="/" class="w-14 h-14 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                         alt="CSAV Logo"
                         class="w-full h-full object-contain">
                </a>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                    Create your account
                </h2>
                <p class="text-[#123524]/60 mt-2">
                    Register as an alumnus/alumna. Your account will automatically receive the
                    <strong class="text-[#123524]">Alumni</strong> role.
                </p>
            </div>

            <form wire:submit="register" class="space-y-5">

                {{-- Global error --}}
                @error('form')
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Honeypot --}}
                <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label for="website">Website</label>
                    <input wire:model="website" type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- First name --}}
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-[#123524] mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="first_name" type="text" id="first_name" name="first_name"
                            autocomplete="given-name" placeholder="Juan" minlength="2" maxlength="255" required
                            class="w-full px-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                            @error('first_name') border-red-400 @else border-[#123524]/15 @enderror">
                        @error('first_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Last name --}}
                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-[#123524] mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="last_name" type="text" id="last_name" name="last_name"
                            autocomplete="family-name" placeholder="Dela Cruz" minlength="2" maxlength="255" required
                            class="w-full px-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                            @error('last_name') border-red-400 @else border-[#123524]/15 @enderror">
                        @error('last_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Middle name --}}
                <div>
                    <label for="middle_name" class="block text-sm font-semibold text-[#123524] mb-2">
                        Middle Name <span class="text-[#123524]/40 font-normal">(optional)</span>
                    </label>
                    <input wire:model="middle_name" type="text" id="middle_name" name="middle_name"
                        autocomplete="additional-name" placeholder="Santos" maxlength="255"
                        class="w-full px-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                        @error('middle_name') border-red-400 @else border-[#123524]/15 @enderror">
                    @error('middle_name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- School ID --}}
                <div>
                    <label for="school_id" class="block text-sm font-semibold text-[#123524] mb-2">
                        School ID <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="school_id" type="text" id="school_id" name="school_id"
                        autocomplete="off" autocapitalize="none" spellcheck="false"
                        placeholder="Enter your school ID" maxlength="30" required
                        class="w-full px-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                        @error('school_id') border-red-400 @else border-[#123524]/15 @enderror">
                    <p class="text-[11px] text-[#123524]/50 mt-2">
                        Use the school ID on your alumni records.
                    </p>
                    @error('school_id')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#123524] mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 012.25 17.25V6.75zm0 0l9.75 6.75 9.75-6.75" />
                            </svg>
                        </div>
                        <input wire:model="email" type="email" id="email" name="email"
                            autocomplete="email" autocapitalize="none" spellcheck="false"
                            placeholder="you@example.com" maxlength="255" required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                            @error('email') border-red-400 @else border-[#123524]/15 @enderror">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-semibold text-[#123524] mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input wire:model="password" :type="showPassword ? 'text' : 'password'"
                            id="password" name="password" autocomplete="new-password"
                            placeholder="Create a strong password" minlength="12" maxlength="72" required
                            class="w-full px-4 py-3 pr-11 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                            @error('password') border-red-400 @else border-[#123524]/15 @enderror">

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#123524]/40 hover:text-[#123524] transition"
                            tabindex="-1">
                            <svg x-show="!showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-2 grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] text-[#123524]/50">
                        <span>12+ characters</span>
                        <span>Upper &amp; lowercase</span>
                        <span>At least one number</span>
                        <span>At least one symbol</span>
                    </div>

                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div x-data="{ showPassword: false }">
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#123524] mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input wire:model="password_confirmation" :type="showPassword ? 'text' : 'password'"
                            id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                            placeholder="Re-enter your password" minlength="12" maxlength="72" required
                            class="w-full px-4 py-3 pr-11 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition
                            @error('password_confirmation') border-red-400 @else border-[#123524]/15 @enderror">

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#123524]/40 hover:text-[#123524] transition"
                            tabindex="-1">
                            <svg x-show="!showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    @error('password_confirmation')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Terms --}}
                <div>
                    <label class="flex items-start gap-3 text-xs text-[#123524]/70 leading-relaxed">
                        <input wire:model="terms" type="checkbox" id="terms" name="terms" required
                            class="mt-0.5 w-4 h-4 rounded border-[#123524]/25 text-[#123524] focus:ring-[#D4A537]">
                        <span>
                            I agree to the
                            <a href="{{ route('terms-and-conditions') }}" target="_blank" rel="noopener noreferrer"
                                class="font-semibold text-[#123524] underline underline-offset-2 hover:text-[#D4A537]">
                                Terms and Conditions
                            </a>
                            and
                            <a href="{{ route('privacy-policy') }}" target="_blank" rel="noopener noreferrer"
                                class="font-semibold text-[#123524] underline underline-offset-2 hover:text-[#D4A537]">
                                Privacy Policy
                            </a>.
                        </span>
                    </label>
                    @error('terms')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- One registration per month note --}}
                <div class="flex items-start gap-3 rounded-xl border border-[#D4A537]/30 bg-[#D4A537]/10 px-4 py-3">
                    <svg class="w-5 h-5 text-[#B8860B] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <p class="text-xs text-[#123524]/70 leading-relaxed">
                        <strong class="text-[#123524]">One registration per month.</strong>
                        This connection can only create one alumni account every 30 days, so please review your details before submitting.
                    </p>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="register"
                    wire:confirm="You can only register once a month. Please review your details before continuing. Continue?"
                    class="group w-full py-3.5 px-6 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="register">Create Account</span>
                    <span wire:loading wire:target="register">Creating account…</span>
                    <svg wire:loading.remove wire:target="register" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-[#123524]/60">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-[#123524] hover:text-[#D4A537] transition-colors">
                    Sign in
                </a>
            </p>
        </div>
    </div>
    <!-- ========== END RIGHT PANEL ========== -->
</div>