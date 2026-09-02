<?php

// TODO: AlumniProfile model was removed. Reimplement against UserProfile + WorkHistory.
// TODO: ProgramHead model was removed. Reimplement as a pivot/relation on User.
use App\Models\User;
use App\Models\UserProfile;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public $alumniByDept;

    public function mount()
    {
        // TODO: AlumniProfile + educational_backgrounds + degree_programs are gone. Rebuild against the new schema.
        $this->alumniByDept = [];
    }

    #[Computed]
    public function users()
    {
        return User::count();
    }

    #[Computed]
    public function alumni()
    {
        return User::role('alumni')->count();
    }

    #[Computed]
    public function programHeads()
    {
        // TODO: ProgramHead model was removed. Rebuild against the new schema.
        return 0;
    }

    #[Computed]
    public function active()
    {
        // "Active" = alumni whose user_profiles.is_verified is true.
        // There's no `status` column on users; is_verified is the closest
        // existing signal in the current schema.
        $verifiedUserIds = UserProfile::where('is_verified', true)->pluck('user_id');

        return User::role('alumni')->whereIn('id', $verifiedUserIds)->count();
    }
};
