<div class="max-w-2xl mx-auto p-6 bg-green-50 backdrop-blur-md rounded-2xl border border-white/30 shadow-2xl text-black space-y-6">

    <!-- Profile Information -->
    <h2 class="text-2xl font-bold">Profile Information</h2>

    <div class="space-y-2">
        <p><span class="font-semibold">Name:</span> {{ $this->alumni->name }}</p>
        <p><span class="font-semibold">Email:</span> {{ $this->alumni->email }}</p>
        
        @if ($this->alumniProfile)
            <p><span class="font-semibold">Permanent Address:</span> {{ $this->alumniProfile->permanent_address }}</p>
            <p><span class="font-semibold">Current Address:</span> {{ $this->alumniProfile->current_address }}</p>
            <p><span class="font-semibold">Gender:</span> {{ ucfirst($this->alumniProfile->gender) }}</p>
            <p><span class="font-semibold">Contact Number:</span> (+63){{ $this->alumniProfile->phone_number_1 }}</p>
        @else
            @if ($this->alumni->hasRole(['super-admin', 'alumni']))
            <p><span class="font-semibold">Permanent Address:</span> not assigned</p>
            <p><span class="font-semibold">Current Address:</span> not assigned</p>
            <p><span class="font-semibold">Gender:</span> not assigned</p>
            <p><span class="font-semibold">Contact Number 1:</span> not assigned</p>
            <p><span class="font-semibold">Contact Number 2:</span> not assigned</p>
            @endif
        @endif
    </div>
    <!-- Update Button -->
    <div class="mt-4">
        <a href="{{ route('alumni.profile.update', $this->alumni->id) }}"
            class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
            Update Personal Info
        </a>
    </div>

    <h2 class="text-2xl font-bold">Education Background</h2>

    <div class="space-y-2">
        @if ($this->educationalBackground)
            <p><span class="font-semibold">Course:</span> {{ $this->educationalBackground->degreeProgram->program_name }}</p>
            <p><span class="font-semibold">Department:</span> {{ $this->educationalBackground->degreeProgram->department->department_name }}</p>
            <p><span class="font-semibold">Batch Year:</span> {{ $this->educationalBackground->batch->batch_year }}</p>
        @else
            @if ($this->alumni->hasRole(['super-admin', 'alumni']))
            <p><span class="font-semibold">Course:</span> not assigned</p>
            <p><span class="font-semibold">Department:</span> not assigned</p>
            <p><span class="font-semibold">Batch Year:</span> not assigned</p>
            @endif
        @endif
    </div>

    <!-- Update Button -->
    @if ($this->alumniProfile)
        <div class="mt-4">
            <a href="{{ route('alumni.profile.update-educational', $this->alumniProfile->id) }}"
                class="bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition">
                Update Education Background
            </a>
        </div>
    @else
        <div class="mt-4 text-black italic rounded-lg border border-transparent bg-green-100 p-4">
            No alumni profile found. Please update your personal information first.
        </div>
    @endif

</div>
