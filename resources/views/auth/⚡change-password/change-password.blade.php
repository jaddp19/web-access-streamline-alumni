<div class="min-h-screen flex bg-[#F7F5EF]">

    <div class="w-full flex items-center justify-center px-6 sm:px-12 py-16">
        <div class="w-full max-w-md">

            <div class="flex justify-center mb-8">
                <div class="w-14 h-14 rounded-full bg-white ring-2 ring-[#D4A537]/60 p-1.5 shadow-md">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png"
                        alt="CSAV Logo" class="w-full h-full object-contain">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-black/5 p-8">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">
                        Create a New Password
                    </h1>
                    <p class="text-sm text-[#123524]/60 mt-2">
                        For your security, please replace your temporary password with a new one before continuing.
                    </p>
                </div>
                
                {{-- ========== GENERAL ERROR SUMMARY ========== --}}
                @if ($errors->any())
                    <div class="mb-6 flex items-start gap-2.5 px-3.5 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <div class="min-w-0">
                            <p class="font-semibold">Please fix the following:</p>
                            <ul class="mt-1 list-disc list-inside space-y-0.5 text-xs font-normal">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-5">

                    <div>
                        <label for="password" class="block text-sm font-semibold text-[#123524] mb-2">
                            New Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input type="password" wire:model="password" id="password"
                                placeholder="••••••••" autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        </div>

                        {{-- Hint text with live bullet list --}}
                        <ul class="mt-2 space-y-0.5 text-[11px] text-[#123524]/50">
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 rounded-full bg-[#123524]/30 shrink-0"></span>
                                At least 8 characters (max 72)
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 rounded-full bg-[#123524]/30 shrink-0"></span>
                                At least one symbol
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 rounded-full bg-[#123524]/30 shrink-0"></span>
                                At least one letter
                            </li>
                        </ul>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#123524] mb-2">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#123524]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="password" wire:model="password_confirmation" id="password_confirmation"
                                placeholder="••••••••" autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-3 rounded-xl border text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        </div>
                    </div>

                    <button type="submit"
                        wire:loading.attr="disabled" wire:target="save"
                        class="group w-full py-3.5 px-6 bg-[#123524] hover:bg-[#0d2819] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Save New Password</span>
                        <span wire:loading wire:target="save">Saving…</span>
                        <svg wire:loading.remove wire:target="save"
                            class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </form>

                <div class="flex items-center justify-center gap-1.5 text-xs text-[#123524]/40 mt-6">
                    <span>Not you?</span>
                    <span class="inline-flex items-center
                        [&_button]:!inline-flex [&_button]:!items-center [&_button]:!w-auto
                        [&_button]:!bg-transparent [&_button]:!border-0 [&_button]:!shadow-none
                        [&_button]:!p-0 [&_button]:!rounded-none
                        [&_button]:!text-xs [&_button]:!font-medium [&_button]:!text-[#123524]/60
                        [&_button]:!underline [&_button]:hover:!text-[#123524]
                        [&_a]:!inline-flex [&_a]:!items-center [&_a]:!w-auto
                        [&_a]:!bg-transparent [&_a]:!border-0 [&_a]:!shadow-none
                        [&_a]:!p-0 [&_a]:!rounded-none
                        [&_a]:!text-xs [&_a]:!font-medium [&_a]:!text-[#123524]/60
                        [&_a]:!underline [&_a]:hover:!text-[#123524]">
                        <livewire:auth::logout />
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>