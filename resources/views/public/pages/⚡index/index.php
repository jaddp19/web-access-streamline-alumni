<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function alumni()
    {
        return User::role('alumni')->count();
    }

    #[Computed]
    public function departments()
    {
        return Cache::remember('stats.departments', 300, fn () =>
            Department::where('is_active', true)->count()
        );
    }

    #[Computed]
    public function programs()
    {
        return Cache::remember('stats.programs', 300, fn () =>
            Course::where('is_active', true)->count()
        );
    }

};