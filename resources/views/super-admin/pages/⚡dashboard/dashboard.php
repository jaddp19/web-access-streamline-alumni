<?php

// TODO: AlumniProfile model was removed. Reimplement against UserProfile + WorkHistory.
// TODO: ProgramHead model was removed. Reimplement as a pivot/relation on User.
use App\Models\User;
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
        // TODO: AlumniProfile model was removed. Replace with User::role('alumni')->count() or similar.
        return 0;
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
        return User::role('alumni')->where('status', 'active')->count();
    }

};