<div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ========== HEADER (BENTO STYLE) ========== -->
    <div class="relative overflow-hidden bg-[#123524] rounded-3xl p-8 mb-5">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[#D4A537]/10"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 rounded-full bg-[#D4A537]/10"></div>

        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#D4A537] flex items-center justify-center text-[#123524] shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div>
                <p class="text-white/50 text-sm">Edit Profile</p>
                <h1 class="text-2xl font-bold text-white" style="font-family: 'Fraunces', serif;">Personal Information</h1>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 font-semibold rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (! $hasProfile)
        <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 font-semibold rounded-xl p-4 text-sm">
            You don't have a profile yet. Fill out the form below to set it up.
        </div>
    @endif

    <!-- ========== FORM CARD ========== -->
    <div class="bg-white border border-black/10 rounded-3xl p-8">
        <form wire:submit.prevent="saveProfile" class="space-y-5">

            <!-- Avatar -->
            <div class="flex items-center gap-5">
                <img src="{{ $avatarFile ? $avatarFile->temporaryUrl() : ($currentAvatar ? Storage::url($currentAvatar) : 'https://ui-avatars.com/api/?name=' . urlencode($name)) }}"
                    class="w-20 h-20 rounded-2xl object-cover border border-black/10 shrink-0">

                <div class="flex-1">
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Avatar</label>
                    <input type="file" wire:model="avatarFile" accept="image/*"
                        class="block w-full text-sm text-black/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#D4A537] file:text-[#123524] hover:file:bg-[#E5B94A] file:cursor-pointer cursor-pointer">
                    <div wire:loading wire:target="avatarFile" class="text-xs text-black/40 mt-1">Uploading...</div>
                    @error('avatarFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Name + Email -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Name</label>
                    <input type="text" wire:model.defer="name"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Gender</label>
                <select wire:model.defer="gender"
                    class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Phone Numbers -->
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Phone Number 1</label>
                    <input type="text" wire:model.defer="phone_number_1"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        placeholder="Enter 10-digit number"
                        inputmode="numeric" pattern="[0-9]*" maxlength="10">
                    @error('phone_number_1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold mb-2">Phone Number 2</label>
                    <input type="text" wire:model.defer="phone_number_2"
                        class="w-full px-4 py-2.5 rounded-xl border border-black/10 bg-[#F1EFE7] text-black placeholder:text-black/40 focus:outline-none focus:border-[#123524] focus:ring-1 focus:ring-[#123524] transition"
                        placeholder="Optional 10-digit number"
                        inputmode="numeric" pattern="[0-9]*" maxlength="10">
                    @error('phone_number_2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Location (map, one-click) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs text-black/60 uppercase tracking-wide font-semibold">Your Location</label>
                    <button type="button" id="use-my-location-btn"
                        class="inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg bg-[#123524] text-white py-1.5 px-3 hover:bg-[#1c4a34] transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-16H3l9 16z" transform="rotate(0)" />
                            <circle cx="12" cy="12" r="3" stroke-linecap="round" />
                        </svg>
                        Use My Current Location
                    </button>
                </div>
                <p class="text-xs text-black/40 mb-2">Tap the button above, or click anywhere on the map to drop your pin.</p>

                <div wire:ignore
                    x-data="locationMap(@js($latitude), @js($longitude))"
                    x-init="init()">
                    <div id="location-map" class="w-full h-72 rounded-xl overflow-hidden border border-black/10"></div>
                </div>

                @error('latitude') <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> @enderror

                <div id="location-address-wrap" class="{{ $address ? '' : 'hidden' }}">
                    <p class="text-sm font-semibold text-[#123524] bg-[#D4A537]/15 border border-[#D4A537]/30 rounded-xl px-4 py-2.5 mt-3 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span id="location-address-text">{{ $address }}</span>
                    </p>
                </div>

                <div id="location-loading" class="hidden text-xs text-black/40 mt-2">Looking up address...</div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-black/5">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-[#D4A537] text-[#123524] hover:bg-[#E5B94A] transition py-2.5 px-5">
                    Save Changes
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

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function locationMap(lat, lng) {
        return {
            map: null,
            marker: null,
            init() {
                const container = document.getElementById('location-map');

                if (container._leaflet_id) {
                    return;
                }

                const startLat = lat ?? 10.3157;
                const startLng = lng ?? 123.8854;

                this.map = L.map(container).setView([startLat, startLng], (lat && lng) ? 15 : 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                if (lat && lng) {
                    this.marker = L.marker([lat, lng]).addTo(this.map);
                }

                this.map.on('click', (e) => this.dropPin(e.latlng.lat, e.latlng.lng));

                const btn = document.getElementById('use-my-location-btn');

                if (btn.dataset.boundLocation) {
                    setTimeout(() => this.map.invalidateSize(), 100);
                    return;
                }
                btn.dataset.boundLocation = 'true';

                btn.addEventListener('click', () => {
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported by your browser.');
                        return;
                    }

                    if (!window.isSecureContext) {
                        alert('Location access requires HTTPS. This page is not running on a secure connection.');
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const { latitude, longitude } = position.coords;
                            this.map.setView([latitude, longitude], 16);
                            this.dropPin(latitude, longitude);
                        },
                        (error) => {
                            console.error('Geolocation error:', error.code, error.message);
                            let msg = 'Unable to retrieve your location.';
                            if (error.code === error.PERMISSION_DENIED) msg = 'Location access was denied. Please allow it in your browser site settings.';
                            if (error.code === error.POSITION_UNAVAILABLE) msg = 'Location information is unavailable right now.';
                            if (error.code === error.TIMEOUT) msg = 'Location request timed out. Try again or click the map instead.';
                            alert(msg);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                });

                setTimeout(() => this.map.invalidateSize(), 100);
            },
            dropPin(lat, lng) {
                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    this.marker = L.marker([lat, lng]).addTo(this.map);
                }

                this.reverseGeocode(lat, lng);
            },
            reverseGeocode(lat, lng) {
                const loadingEl = document.getElementById('location-loading');
                const wrapEl = document.getElementById('location-address-wrap');
                const textEl = document.getElementById('location-address-text');

                if (loadingEl) loadingEl.classList.remove('hidden');

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                    headers: { 'Accept-Language': 'en' }
                })
                    .then((res) => res.json())
                    .then((data) => {
                        const addr = data.address || {};

                        // Build: Province, City/Municipality, Barangay
                        const parts = [
                            addr.state,
                            addr.city || addr.town || addr.municipality,
                            addr.village || addr.suburb || addr.neighbourhood || addr.quarter,
                        ].filter(Boolean);

                        const formatted = parts.length ? parts.join(', ') : (data.display_name ?? `${lat}, ${lng}`);

                        if (wrapEl) wrapEl.classList.remove('hidden');
                        if (textEl) textEl.textContent = formatted;

                        this.$wire.setLocation(lat, lng, formatted);
                    })
                    .catch(() => {
                        // Fall back to raw coordinates if the lookup fails.
                        const fallback = `${lat}, ${lng}`;
                        if (wrapEl) wrapEl.classList.remove('hidden');
                        if (textEl) textEl.textContent = fallback;
                        this.$wire.setLocation(lat, lng, fallback);
                    })
                    .finally(() => {
                        if (loadingEl) loadingEl.classList.add('hidden');
                    });
            }
        }
    }
</script>
@endpush
