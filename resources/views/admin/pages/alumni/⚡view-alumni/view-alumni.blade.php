<div>
    <!-- Table Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto rounded-xl">
        <!-- Card -->
        <div class="flex flex-col rounded-xl border border-white/30 p-4 sm:p-6 lg:p-8 bg-green-50 shadow-2xl">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-md">
                <div class="min-w-full inline-block align-middle">
                    <div class="rounded-xl shadow-2xs overflow-hidden">

                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center">
                            <div>
                                <h2 class="text-xl font-extrabold text-green-700">
                                    Alumni
                                </h2>
                                <p class="text-sm text-black">
                                    Manage alumni in your department
                                </p>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Table -->
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead class="bg-green-700">
                                <tr>
                                    <th class="ps-2 sm:ps-6 py-3 text-start font-extrabold uppercase text-white">
                                        Name
                                    </th>
                                    <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-extrabold uppercase text-white">
                                        Email
                                    </th>
                                    <th class="hidden sm:table-cell px-2 sm:px-6 py-3 text-start font-extrabold uppercase text-white">
                                        Degree Program
                                    </th>
                                    <th class="hidden md:table-cell px-2 sm:px-6 py-3 text-start font-extrabold uppercase text-white">
                                        Batch Year
                                    </th>
                                    <th class="px-2 sm:px-6 py-3 text-end"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/30">
                                @forelse ($this->educationalBackgrounds as $background)
                                    <tr>
                                        <td class="px-2 sm:px-6 py-3">
                                            <span class="block font-semibold text-black">
                                                {{ $background->alumniProfile?->user?->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                            <span class="block text-black">
                                                {{ $background->alumniProfile?->user?->email ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="hidden sm:table-cell px-2 sm:px-6 py-3">
                                            <span class="block text-black">
                                                {{ $background->degreeProgram?->program_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="hidden md:table-cell px-2 sm:px-6 py-3">
                                            <span class="text-black">
                                                {{ $background->batch?->batch_year ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-2 sm:px-6 py-1.5 text-end">
                                            <a href="{{ route('admin.alumni.update', $background->alumniProfile?->user?->id) }}"
                                                class="text-black hover:underline">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-2 sm:px-6 py-4 text-center text-black">
                                            No alumni found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->

                        <!-- Footer -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-white/30">
                            <div>
                                <p class="text-sm text-black">
                                    <span class="font-semibold text-black">
                                        {{ $this->educationalBackgrounds->total() }}
                                    </span>
                                    results
                                </p>
                            </div>
                            <div>
                                <div class="inline-flex gap-x-2">

                                    {{-- Prev Button --}}
                                    @if ($this->educationalBackgrounds->onFirstPage())
                                        <button disabled
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-medium rounded-md border text-gray-400 cursor-not-allowed">
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15l-6-6 6-6" />
                                            </svg>
                                            Prev
                                        </button>
                                    @else
                                        <button wire:click="previousPage"
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-1 text-sm font-medium rounded-md hover:shadow-md transition-all duration-200 bg-green-700 text-white hover:bg-green-600">
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15l-6-6 6-6" />
                                            </svg>
                                            Prev
                                        </button>
                                    @endif

                                    {{-- Next Button --}}
                                    @if ($this->educationalBackgrounds->hasMorePages())
                                        <button wire:click="nextPage"
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-0 text-sm font-medium rounded-md hover:shadow-md transition-all duration-200 bg-green-700 text-white hover:bg-green-600">
                                            Next
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 3l6 6-6 6" />
                                            </svg>
                                        </button>
                                    @else
                                        <button disabled
                                            class="px-4 py-2 inline-flex items-center justify-center gap-x-0 text-sm font-medium rounded-md border text-gray-400 cursor-not-allowed">
                                            Next
                                            <svg class="w-4 h-4 flex-shrink-0 align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 3l6 6-6 6" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    <!-- End Table Section -->
</div>
