<div>
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">
            <!-- Card -->
            <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm p-6 sm:p-8">

                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('super-admin.email.view') }}"
                        class="inline-flex items-center gap-x-2 text-sm font-semibold text-[#123524]/70 hover:text-[#123524] transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        <span>Back to Email Templates</span>
                    </a>
                </div>

                <!-- Title -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Update Email Template</h2>
                        <p class="text-sm text-black/50">The slug will be regenerated from the subject</p>
                    </div>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="update" class="space-y-5">
                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-xs uppercase tracking-wide font-semibold text-black/60 mb-2">Subject</label>
                        <input wire:model.defer="subject" type="text" id="subject"
                            class="w-full px-4 py-2.5 rounded-xl border border-[#123524]/15 bg-[#F7F5EF] text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition">
                        @error('subject') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-xs uppercase tracking-wide font-semibold text-black/60 mb-2">Message</label>
                        <textarea wire:model.defer="message" id="message" rows="8"
                            class="w-full px-4 py-3 rounded-xl border border-[#123524]/15 bg-[#F7F5EF] text-[#123524] placeholder-[#123524]/30 focus:outline-none focus:ring-2 focus:ring-[#D4A537] focus:border-transparent transition"></textarea>
                        @error('message') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Update Button -->
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-x-2 px-6 py-2.5 bg-[#123524] text-white text-sm font-semibold rounded-xl hover:bg-[#0d2819] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Update Template
                    </button>
                </form>

                @if (session('success'))
                    <div class="mt-5 flex items-start gap-2.5 px-4 py-3 bg-[#123524]/5 border border-[#123524]/10 rounded-xl text-[#123524] text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
