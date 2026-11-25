<?php

use App\Models\Batch;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    #[Locked]
    public User $user;

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_year = '';
    public string $school_id_suffix = '';
    public ?int $batch_id = null;

    public function mount(User $user): void
    {
        Gate::authorize('update', $user);
        $this->user = $user->load([
            'userProfile:id,user_id,batch_id',
        ]);

        // Hydrate name parts. If empty (legacy user), split from `name`.
        $first = $user->first_name;
        $middle = $user->middle_name;
        $last = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split = preg_split('/\s+/', trim($user->name));
            $first = $split[0] ?? '';
            $last = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2
                ? implode(' ', array_slice($split, 1, -1))
                : '';
        }

        $this->first_name = $first ?? '';
        $this->middle_name = $middle ?? '';
        $this->last_name = $last ?? '';
        $this->email = $user->email ?? '';

        // Split the stored "YYYY-NNNN" back into year + suffix.
        if ($user->school_id && str_contains($user->school_id, '-')) {
            [$year, $suffix] = explode('-', $user->school_id, 2);
            $this->school_year = $year;
            $this->school_id_suffix = $suffix;
        }

        $this->batch_id = $user->userProfile?->batch_id;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    #[Computed]
    public function schoolId(): string
    {
        if (blank($this->school_year) || blank($this->school_id_suffix)) {
            return '';
        }

        return $this->school_year.'-'.$this->school_id_suffix;
    }

    // =========================================================
    //  UPDATE (Disabled)
    // =========================================================

    public function updateAlumni()
    {
        Gate::authorize('update', $this->user);

        // Since fields are disabled, we just redirect back
        return redirect()->route('admin.alumni.view');
    }
};