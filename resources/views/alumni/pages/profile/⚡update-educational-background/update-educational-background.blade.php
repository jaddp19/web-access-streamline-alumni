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

    @if (session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 font-semibold rounded-xl p-4 text-sm flex items-start gap-2">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="update" class="space-y-6">

            <!-- Batch Year -->
            <div>
                <label class="flex items-center gap-2 text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Batch Year
                </label>
                <input type="number" wire:model.defer="batch_year"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                    placeholder="e.g. 2026"
                    inputmode="numeric" min="1950" max="{{ date('Y') + 1 }}" step="1">
                @error('batch_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Quick-pick year chips -->
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach ($this->recentYears as $year)
                        <button type="button" wire:click="pickYear({{ $year }})"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition
                                {{ $batch_year === $year
                                    ? 'bg-[#123524] text-white border-[#123524]'
                                    : 'bg-white text-black/60 border-black/10 hover:border-[#123524]/40 hover:text-black' }}">
                            {{ $year }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Degree Program -->
            <div>
                <label class="flex items-center gap-2 text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                    </svg>
                    Degree Program
                </label>
                <select wire:model.live="course_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    <option value="">Select Degree Program</option>
                    @foreach($this->courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_title }}</option>
                    @endforeach
                </select>
                @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Live preview of selected course -->
                @if ($this->selectedCourse)
                    <div class="mt-3 flex items-center gap-3 bg-[#123524]/5 border border-[#123524]/10 rounded-xl p-4">
                        <div class="w-10 h-10 rounded-lg bg-[#D4A537]/20 flex items-center justify-center text-[#123524] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-black truncate">{{ $this->selectedCourse->department->dept_name ?? 'No department set' }}</p>
                            <p class="text-xs text-black/60 flex items-center gap-1.5 mt-0.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                    {{ $this->selectedCourse->course_type === 'board' ? 'bg-[#1C6B45]/10 text-[#1C6B45]' : 'bg-black/5 text-black/50' }}">
                                    {{ $this->selectedCourse->course_type === 'board' ? 'Board Program' : 'Non-Board Program' }}
                                </span>
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Visibility Toggle -->
            <div class="flex items-center justify-between bg-[#F1EFE7] border border-black/10 rounded-xl px-5 py-4">
                <div class="flex items-start gap-3 pr-4">
                    <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[#123524] shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-black">Show my education background to other alumni</p>
                        <p class="text-xs text-black/60 mt-0.5">If turned off, your course, department, and batch year will be hidden from other alumni viewing your profile.</p>
                    </div>
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
