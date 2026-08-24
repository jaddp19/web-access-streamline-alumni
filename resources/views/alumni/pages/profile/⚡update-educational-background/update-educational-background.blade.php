<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER (BENTO STYLE) ========== -->
    <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Edit Profile</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Educational Background</h1>
            </div>
        </div>
    </div>

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="update" class="space-y-5">

            <!-- Batch Year + Degree Program -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Batch Year</label>
                    <input type="number" wire:model.defer="batch_year"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        placeholder="e.g. 2026"
                        inputmode="numeric" min="1950" max="{{ date('Y') + 1 }}" step="1">
                    @error('batch_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Degree Program</label>
                    <select wire:model.defer="degree_program_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                        <option value="">Select Degree Program</option>
                        @foreach($this->degreePrograms as $program)
                            <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                    @error('degree_program_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Visibility Toggle -->
            <div class="flex items-center justify-between bg-[#F1EFE7] border border-black/10 rounded-xl px-5 py-4">
                <div class="pr-4">
                    <p class="font-semibold text-sm text-black">Show my education background to other alumni</p>
                    <p class="text-xs text-black/60 mt-0.5">If turned off, your course, department, and batch year will be hidden from other alumni viewing your profile.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" wire:model.defer="is_public" class="sr-only peer">
                    <div class="w-11 h-6 bg-black/20 rounded-full peer peer-checked:bg-[#1C6B45] transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                </label>
            </div>
            @error('is_public') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Update Background
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
</div>
