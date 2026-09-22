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
        return Cache::remember('landing:stats', now()->addMinutes(5), function () {
            return [
                'alumni'      => User::role('alumni')->count(),
                'departments' => Department::where('is_active', true)->count(),
                'programs'    => Course::where('is_active', true)->count(),
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