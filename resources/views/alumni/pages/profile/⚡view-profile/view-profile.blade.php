<div class="bg-[#F0F2F5] dark:bg-[#18191A] min-h-screen">

    {{-- ========== FB-STYLE COVER + PROFILE HEADER ========== --}}
    <div class="bg-white dark:bg-[#242526]">
        <div class="max-w-[1100px] mx-auto">

            {{-- Cover photo --}}
            <div class="h-48 sm:h-64 lg:h-80 bg-gradient-to-r from-[#123524] via-[#1C6B45] to-[#123524] relative overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]"></div>
                    <div class="absolute right-20 top-10 w-32 h-32 rounded-full bg-white/30"></div>
                    <div class="absolute right-60 -bottom-10 w-40 h-40 rounded-full bg-[#D4A537]"></div>
                </div>
            </div>

            {{-- Profile strip --}}
            <div class="px-4 pb-4 -mt-16 sm:-mt-20 relative">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <img src="{{ $this->avatarUrl }}"
                            alt="{{ $this->alumni->name }}"
                            class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-4 ring-white dark:ring-[#242526] shadow-lg bg-[#D4A537]">
                        <div class="pb-2">
                            <h1 class="text-3xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">{{ $this->alumni->name }}</h1>
                            <p class="text-black/60 dark:text-white/60 text-sm mt-0.5">
                                @if ($this->userProfile?->batch)
                                    Alumni &middot; {{ $this->userProfile->batch->batch_name }}
                                @else
                                    Alumni Member
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pb-2">
                        @if ($this->userProfile?->is_verified)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Verified
                            </span>
                        @endif
                        <a href="{{ route('alumni.profile.update', $this->alumni->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#1877F2] text-white text-xs font-semibold hover:bg-[#166FE5] transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                            Edit profile
                        </a>
                    </div>
                </div>

                {{-- Tabs (FB-style) --}}
                <div class="mt-6 border-t border-black/10 dark:border-white/10">
                    <ul class="flex items-center gap-1 overflow-x-auto">
                        <li><a href="#" class="inline-block px-4 py-3 text-sm font-semibold text-[#1877F2] border-b-[3px] border-[#1877F2] -mb-px">Timeline</a></li>
                        <li><a href="#" class="inline-block px-4 py-3 text-sm font-semibold text-[#1877F2] border-b-[3px] border-[#1877F2] -mb-px">About</a></li>
                        <li><a href="{{ route('alumni.message') }}" class="inline-block px-4 py-3 text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-white/5 rounded-t-lg transition">Posts</a></li>
                        <li><a href="{{ route('alumni.settings') }}" class="inline-block px-4 py-3 text-sm font-semibold text-black/60 dark:text-white/60 hover:bg-[#F0F2F5] dark:hover:bg-white/5 rounded-t-lg transition">Settings</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1100px] mx-auto px-4 pt-4">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold rounded-xl p-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 font-semibold rounded-xl p-4 text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ========== TWO-COLUMN CONTENT ========== --}}
    <div class="max-w-[1100px] mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- ===== LEFT COLUMN ===== --}}
        <aside class="lg:col-span-5 space-y-4">
            {{-- Intro card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4">
                <h2 class="text-xl font-bold text-black dark:text-white mb-3" style="font-family: 'Fraunces', serif;">Contact Info</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-3 text-black/80 dark:text-white/80 py-1.5">
                        <svg class="w-5 h-5 text-black/50 dark:text-white/50 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" /></svg>
                        <span class="font-medium">{{ $this->alumni->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-black/80 dark:text-white/80 py-1.5">
                        <svg class="w-5 h-5 text-black/50 dark:text-white/50 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        <span class="font-medium">
                            @if ($this->contact['phone_number_1'])
                                (+63){{ $this->contact['phone_number_1'] }}
                            @else
                                <span class="text-black/40 dark:text-white/40">Add contact number</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-black/80 dark:text-white/80 py-1.5">
                        <svg class="w-5 h-5 text-black/50 dark:text-white/50 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <span class="font-medium">
                            @if ($this->contact['address'])
                                <a href="https://www.google.com/maps?q={{ $this->contact['latitude'] }},{{ $this->contact['longitude'] }}"
                                    target="_blank" rel="noopener"
                                    class="text-[#1877F2] hover:underline">
                                    {{ $this->contact['address'] }}
                                </a>
                            @elseif ($this->contact['latitude'] && $this->contact['longitude'])
                                <a href="https://www.google.com/maps?q={{ $this->contact['latitude'] }},{{ $this->contact['longitude'] }}"
                                    target="_blank" rel="noopener"
                                    class="text-[#1877F2] hover:underline">
                                    View location on map
                                </a>
                            @else
                                <span class="text-black/40 dark:text-white/40">Location not set</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-black/80 dark:text-white/80 py-1.5">
                        <svg class="w-5 h-5 text-black/50 dark:text-white/50 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                        <span class="font-medium">
                            @if ($this->course)
                                {{ $this->course->course_title }}
                            @else
                                <span class="text-black/40 dark:text-white/40">Add your program</span>
                            @endif
                        </span>
                    </div>
                </div>

                <a href="{{ route('alumni.profile.update', $this->alumni->id) }}" class="mt-4 block w-full text-center py-2 rounded-lg bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-sm font-semibold text-black dark:text-white transition">
                    Edit bio
                </a>
            </div>

            {{-- Education quick card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-4">
                <h2 class="text-xl font-bold text-black dark:text-white mb-3" style="font-family: 'Fraunces', serif;">Education</h2>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-lg bg-[#1877F2]/10 dark:bg-[#1877F2]/15 flex items-center justify-center text-[#1877F2] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-black dark:text-white text-sm">Colegio de Sta. Ana de Victorias</p>
                        <p class="text-xs text-black/60 dark:text-white/60 mt-0.5">
                            {{ $this->course->course_title ?? 'Program not set' }}
                        </p>
                        <p class="text-xs text-black/50 dark:text-white/50 mt-1">
                            @if ($this->userProfile?->batch)
                                Class of {{ $this->userProfile->batch->batch_name }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ $this->userProfile ? route('alumni.profile.update-educational', $this->userProfile->id) : route('alumni.profile.update', $this->alumni->id) }}" class="mt-3 block w-full text-center py-2 rounded-lg bg-[#F0F2F5] dark:bg-[#3A3B3C] hover:bg-[#E4E6EB] dark:hover:bg-[#4E4F50] text-sm font-semibold text-black dark:text-white transition">
                    Edit education
                </a>
            </div>
        </aside>

        {{-- ===== RIGHT COLUMN ===== --}}
        <main class="lg:col-span-7 space-y-4">

            {{-- Personal info card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Personal Information</h2>
                    <a href="{{ route('alumni.profile.update', $this->alumni->id) }}" class="text-[#1877F2] text-sm font-semibold hover:underline">Edit</a>
                </div>

                <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Gender</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->contact['gender'] ? ucfirst($this->contact['gender']) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Contact Number</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->contact['phone_number_1'] ? '(+63)'.$this->contact['phone_number_1'] : '—' }}</p>
                    </div>
                    @if ($this->contact['phone_number_2'])
                        <div>
                            <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Alternate Number</p>
                            <p class="font-medium text-black dark:text-white mt-1">(+63){{ $this->contact['phone_number_2'] }}</p>
                        </div>
                    @endif
                </div>

                {{-- Location --}}
                <div class="mt-5 pt-4 border-t border-black/5 dark:border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Location</p>
                        @if ($this->contact['latitude'] && $this->contact['longitude'])
                            <a href="https://www.google.com/maps?q={{ $this->contact['latitude'] }},{{ $this->contact['longitude'] }}"
                                target="_blank" rel="noopener"
                                class="text-[#1877F2] text-xs font-semibold hover:underline">
                                Open in Google Maps
                            </a>
                        @endif
                    </div>

                    @if ($this->contact['latitude'] && $this->contact['longitude'])
                        <div wire:ignore
                            x-data="profileLocationMap(@js($this->contact['latitude']), @js($this->contact['longitude']))"
                            x-init="init()">
                            <div id="profile-location-map" class="w-full h-56 rounded-xl overflow-hidden border border-black/10 dark:border-white/10"></div>
                        </div>
                        <p class="text-sm font-medium text-black/70 dark:text-white/70 mt-2">
                            @if ($this->contact['address'])
                                {{ $this->contact['address'] }}
                            @else
                                {{ $this->contact['latitude'] }}, {{ $this->contact['longitude'] }}
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-black/40 dark:text-white/40 italic">No location set yet.</p>
                    @endif
                </div>
            </div>

            {{-- Education details card --}}
            <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-black dark:text-white" style="font-family: 'Fraunces', serif;">Education</h2>
                    @if ($this->userProfile)
                        <a href="{{ route('alumni.profile.update-educational', $this->userProfile->id) }}" class="text-[#1877F2] text-sm font-semibold hover:underline">Edit</a>
                    @endif
                </div>

                <div class="grid sm:grid-cols-3 gap-x-6 gap-y-4">
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Course</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->course->course_title ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Department</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->course->department->dept_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-black/50 dark:text-white/50 font-semibold uppercase tracking-wide">Batch</p>
                        <p class="font-medium text-black dark:text-white mt-1">{{ $this->userProfile?->batch?->batch_name ?? '—' }}</p>
                    </div>
                </div>

                @unless ($this->userProfile)
                    <div class="mt-4 text-sm text-black/60 dark:text-white/60 italic bg-[#F0F2F5] dark:bg-[#3A3B3C] border border-black/5 dark:border-white/10 rounded-xl p-4">
                        No profile found. Please update your personal information first.
                    </div>
                @endunless
            </div>

        </main>

    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function profileLocationMap(lat, lng) {
        return {
            map: null,
            init() {
                const container = document.getElementById('profile-location-map');

                if (container._leaflet_id) {
                    return;
                }

                this.map = L.map(container, {
                    zoomControl: true,
                    dragging: true,
                    scrollWheelZoom: false,
                }).setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                L.marker([lat, lng]).addTo(this.map);

                setTimeout(() => this.map.invalidateSize(), 100);
            }
        }
    }
</script>
@endpush
