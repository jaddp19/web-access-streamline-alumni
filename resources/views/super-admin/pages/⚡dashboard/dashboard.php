<?php

use App\Models\AlumniProfile;
use App\Models\ProgramHead;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public $alumniByDept;

    public function mount()
    {
        $this->alumniByDept = AlumniProfile::query()
            ->join('educational_backgrounds', 'alumni_profiles.id', '=', 'educational_backgrounds.alumni_profile_id')
            ->join('degree_programs', 'educational_backgrounds.degree_program_id', '=', 'degree_programs.id')
            ->join('departments', 'degree_programs.department_id', '=', 'departments.id')
            ->selectRaw('departments.department_name as dept, COUNT(alumni_profiles.id) as total')
            ->groupBy('departments.department_name')
            ->pluck('total', 'dept')
            ->toArray();
    }


    #[Computed]
    public function users()
    {
        return User::count();
    }

    #[Computed]
    public function alumni()
    {
        return AlumniProfile::count();
    }

    #[Computed]
    public function programHeads()
    {
        return ProgramHead::count();
    }

};