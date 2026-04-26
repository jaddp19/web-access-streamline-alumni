<div class="max-w-5xl mx-auto p-10 bg-green-50 rounded-3xl border border-gray-200 shadow-2xl">

    <!-- Profile Header -->
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 pb-8">
        <!-- Avatar -->
        <div class="w-28 h-28 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-3xl shadow">
            {{ strtoupper(substr($this->alumni->name, 0, 1)) }}
        </div>
        <!-- Name + Email -->
        <div class="text-center md:text-left">
            <h2 class="text-4xl font-bold text-gray-900">{{ $this->alumni->name }}</h2>
            <p class="text-gray-600 text-lg">{{ $this->alumni->email }}</p>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="grid md:grid-cols-2 gap-12 mt-10">

        <!-- Personal Information -->
        <div class="space-y-6">
            <h3 class="text-2xl font-extrabold text-green-700 border-b border-green-700 pb-2">Personal Information</h3>
            <ul class="space-y-3 text-gray-800">
                <li><span class="font-semibold">Permanent Address:</span> {{ $this->alumniProfile->permanent_address ?? 'Not assigned' }}</li>
                <li><span class="font-semibold">Current Address:</span> {{ $this->alumniProfile->current_address ?? 'Not assigned' }}</li>
                <li><span class="font-semibold">Gender:</span> {{ $this->alumniProfile ? ucfirst($this->alumniProfile->gender) : 'Not assigned' }}</li>
                <li><span class="font-semibold">Contact Number:</span> {{ $this->alumniProfile ? '(+63)'.$this->alumniProfile->phone_number_1 : 'Not assigned' }}</li>
            </ul>
            <div class="pt-4">
                <a href="{{ route('alumni.profile.update', $this->alumni->id) }}"
                   class="inline-block bg-green-700 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-600 transition">
                   Update Personal Info
                </a>
            </div>
        </div>

        <!-- Education Background -->
        <div class="space-y-6">
            <h3 class="text-2xl font-extrabold text-green-700 border-b pb-2">Education Background</h3>
            <ul class="space-y-3 text-gray-800">
                <li><span class="font-semibold">Course:</span> {{ $this->educationalBackground->degreeProgram->program_name ?? 'Not assigned' }}</li>
                <li><span class="font-semibold">Department:</span> {{ $this->educationalBackground->degreeProgram->department->department_name ?? 'Not assigned' }}</li>
                <li><span class="font-semibold">Batch Year:</span> {{ $this->educationalBackground->batch->batch_year ?? 'Not assigned' }}</li>
            </ul>
            @if ($this->alumniProfile)
                <div class="pt-4">
                    <a href="{{ route('alumni.profile.update-educational', $this->alumniProfile->id) }}"
                       class="inline-block bg-green-700 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-600 transition">
                       Update Education Background
                    </a>
                </div>
            @else
                <div class="mt-4 text-gray-700 italic rounded-xl border border-gray-200 bg-green-50 p-4">
                    No alumni profile found. Please update your personal information first.
                </div>
            @endif
        </div>
    </div>
</div>
