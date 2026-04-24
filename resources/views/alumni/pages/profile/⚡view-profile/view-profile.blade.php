<div class="max-w-2xl mx-auto p-6 bg-white/10 backdrop-blur-md rounded-2xl border border-white/30 shadow-lg text-white space-y-6">

    <!-- Profile Information -->
    <h2 class="text-2xl font-bold">Profile Information</h2>

    <div class="space-y-2">
        <p><span class="font-semibold">Name:</span> {{ $this->alumni->name }}</p>
        <p><span class="font-semibold">Email:</span> {{ $this->alumni->email }}</p>
    </div>
    <!-- Update Button -->
    <div class="mt-4">
        <a href="{{ route('alumni.name-password.update', $this->alumni->id) }}"
            class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
            Update Name and Password
        </a>
    </div>

    <h2 class="text-2xl font-bold">Education Background</h2>

    @if ($this->alumniProfile)
        <div class="space-y-2">
            <p><span class="font-semibold">Address:</span> {{ $this->alumniProfile->permanent_address }}</p>
            <p><span class="font-semibold">Year Graduated:</span> {{ $this->alumniProfile->batch->batch_year }}</p>
        </div>
    @else
        @if ($this->alumni->hasRole('super-admin'))
            <div class="text-gray-400 italic">No alumni profile info available.</div>
        @endif
    @endif
    
        <!-- Update Button -->
        <div class="mt-4">
            <a href="{{ route('alumni.profile.update', $this->alumni->id) }}"
                class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
                Update Personal Info
            </a>
        </div>
</div>
