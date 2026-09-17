<div>
    <!-- Form Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Card -->
        <div class="flex flex-col rounded-2xl border border-black/5 bg-white shadow-sm">

            <!-- Header -->
            <div class="px-6 py-5 grid gap-3 md:flex md:justify-between md:items-center border-b border-black/5">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-700/10 flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Edit Company</h2>
                        <p class="text-sm text-black/50">
                            Last updated {{ $company->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-x-2">
                    <a href="{{ route('super-admin.company.view') }}"
                        class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-[#123524]/15 text-[#123524] hover:bg-[#123524]/5 transition">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>
            <!-- End Header -->

            <!-- Form Body -->
            <form wire:submit="save" class="px-6 py-6">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Company Name --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-1.5">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="company_name"
                            class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-black/15 text-black
                                   focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524]"
                            placeholder="e.g. Accenture Philippines">
                        @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Logo --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-1.5">Company Logo</label>

                        <div class="flex items-start gap-4">
                            {{-- Preview --}}
                            <div class="w-20 h-20 rounded-xl border border-black/10 bg-[#F7F5EF] flex items-center justify-center overflow-hidden shrink-0">
                                @if ($company_logo)
                                    {{-- Newly uploaded (unsaved) --}}
                                    <img src="{{ $company_logo->temporaryUrl() }}" class="w-full h-full object-contain">
                                @elseif ($company->company_logo && ! $remove_logo)
                                    {{-- Existing saved logo --}}
                                    <img src="{{ Storage::url($company->company_logo) }}" class="w-full h-full object-contain">
                                @else
                                    {{-- No logo --}}
                                    <svg class="w-8 h-8 text-black/20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 18h16.5M3.75 6.75h.008v.008H3.75V6.75zm0 3.75h.008v.008H3.75V10.5zm0 3.75h.008v.008H3.75v-.008z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Upload Control --}}
                            <div class="flex-1">
                                <input type="file" wire:model="company_logo" accept="image/*"
                                    class="block w-full text-xs text-black/70 file:mr-3 file:py-2 file:px-3
                                           file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                           file:bg-[#123524] file:text-white hover:file:bg-[#0d2819] file:cursor-pointer
                                           border border-black/15 rounded-lg">
                                <p class="mt-1.5 text-xs text-black/50">PNG, JPG, or WEBP · max 2MB</p>

                                <div wire:loading wire:target="company_logo" class="mt-1 text-xs text-[#123524]">
                                    Uploading...
                                </div>

                                @error('company_logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                @if ($company->company_logo && ! $remove_logo && ! $company_logo)
                                    <button type="button" wire:click="$toggle('remove_logo')"
                                        class="mt-2 text-xs font-semibold text-red-600 hover:underline">
                                        Remove current logo
                                    </button>
                                @endif

                                @if ($remove_logo)
                                    <p class="mt-2 text-xs text-red-600 font-semibold">
                                        Logo will be removed on save.
                                        <button type="button" wire:click="$toggle('remove_logo')" class="underline ml-1">Undo</button>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Company Address --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-1.5">Address</label>
                        <textarea wire:model.blur="company_address" rows="2"
                            class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-black/15 text-black
                                   focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524]"
                            placeholder="Building, street, city, province"></textarea>
                        @error('company_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#123524] mb-1.5">Description</label>
                        <textarea wire:model.blur="company_desc" rows="4"
                            class="w-full py-2.5 px-3.5 text-sm rounded-lg border border-black/15 text-black
                                   focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524]"
                            placeholder="Short description of the company..."></textarea>
                        @error('company_desc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- Actions --}}
                <div class="mt-8 pt-5 border-t border-black/5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <a href="{{ route('super-admin.company.view') }}"
                       class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold rounded-lg border border-[#123524]/15 text-[#123524] hover:bg-[#123524]/5 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold rounded-lg bg-[#123524] text-white hover:bg-[#0d2819] transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Update Company</span>
                        <span wire:loading wire:target="save">Updating...</span>
                    </button>
                </div>
            </form>
            <!-- End Form Body -->

        </div>
        <!-- End Card -->
    </div>
    <!-- End Form Section -->
</div>