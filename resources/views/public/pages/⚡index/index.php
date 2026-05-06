<?php

use App\Models\Department;
use App\Models\User;
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
        return Department::count();
    }
};