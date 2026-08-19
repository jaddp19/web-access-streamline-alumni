<?php

use App\Models\EducationalBackground;
use App\Models\ProgramHead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-admin')] class extends Component
{
    use WithPagination;

    public ?int $departmentId = null;

    public function mount()
    {
        // Super-admins see everyone; program heads (admin) only see their own department
        if (Auth::user()->hasRole('super-admin')) {
            $this->departmentId = null;
        } else {
            $this->departmentId = ProgramHead::where('user_id', Auth::id())
                ->value('department_id');
        }
    }

    /**
     * Computed property: total number of alumni visible to this admin.
     */
    #[Computed]
    public function totalUsersCount()
    {
        return User::role('alumni')
            ->when($this->departmentId, function ($query) {
                $query->whereHas('alumniProfile.educationalBackground.degreeProgram', function ($q) {
                    $q->where('department_id', $this->departmentId);
                });
            })
            ->count();
    }

    /**
     * Computed property: paginated educational backgrounds, scoped by department.
     */
    #[Computed]
    public function educationalBackgrounds()
    {
        return EducationalBackground::with(['alumniProfile.user', 'degreeProgram', 'batch'])
            ->when($this->departmentId, function ($query) {
                $query->whereHas('degreeProgram', function ($q) {
                    $q->where('department_id', $this->departmentId);
                });
            })
            ->select('id', 'alumni_profile_id', 'degree_program_id', 'batch_id')
            ->latest()
            ->paginate(5);
    }
};
