<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    // =========================================================
    //  STATS — all three cached in one place, 5 min TTL
    // =========================================================

    #[Computed]
    public function stats(): array
    {
        // 👇 bumped the cache key to 'landing:stats:v2' so it ignores the old wrong count
        return Cache::remember('landing:stats:v2', now()->addMinutes(5), function () {
            return [
                // Added the whereHas constraint here
                'alumni' => User::role('alumni')
                    ->whereHas('userProfile', fn ($q) => $q->where('is_approved', true))
                    ->count(),
                    
                'departments' => Department::where('is_active', true)->count(),
                'programs' => Course::where('is_active', true)->count(),
            ];
        });
    }

    // =========================================================
    //  INDIVIDUAL ACCESSORS
    // =========================================================

    #[Computed]
    public function alumni(): int
    {
        return $this->stats['alumni'];
    }

    #[Computed]
    public function departments(): int
    {
        return $this->stats['departments'];
    }

    #[Computed]
    public function programs(): int
    {
        return $this->stats['programs'];
    }
};
