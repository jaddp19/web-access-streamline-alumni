<?php

use App\Models\Company;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    #[Url] public string $tab = 'name';

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['name', 'contact', 'email', 'company'], true)) {
            return;
        }

        $this->tab = $tab;
    }

    #[Computed]
    public function nameGroups()
    {
        $groups = DB::table('users')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'user_profiles.batch_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('users.first_name')
            ->whereNotNull('users.last_name')
            ->where('users.first_name', '!=', '')
            ->where('users.last_name', '!=', '')
            ->groupBy('users.first_name', 'users.last_name', 'user_profiles.batch_id')
            ->having('total', '>', 1)
            ->orderByDesc('total')
            ->get();

        return $groups->map(function ($group) {
            $users = User::query()
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.first_name', $group->first_name)
                ->where('users.last_name', $group->last_name)
                ->where('user_profiles.batch_id', $group->batch_id)
                ->with(['userProfile.batch'])
                ->get([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.created_at',
                    'user_profiles.contact_number_1',
                    'user_profiles.is_verified',
                ]);

            return [
                'key'   => $group->first_name . ' ' . $group->last_name,
                'batch' => $users->first()?->userProfile?->batch?->batch_name,
                'count' => $group->total,
                'users' => $users,
            ];
        });
    }

    #[Computed]
    public function contactGroups()
    {
        $groups = DB::table('user_profiles')
            ->select('contact_number_1', DB::raw('COUNT(*) as total'))
            ->whereNotNull('contact_number_1')
            ->where('contact_number_1', '!=', '')
            ->groupBy('contact_number_1')
            ->having('total', '>', 1)
            ->orderByDesc('total')
            ->get();

        return $groups->map(function ($group) {
            $profiles = UserProfile::query()
                ->where('contact_number_1', $group->contact_number_1)
                ->with(['user:id,name,email', 'batch:id,batch_name'])
                ->get();

            return [
                'key'      => $group->contact_number_1,
                'count'    => $group->total,
                'profiles' => $profiles,
            ];
        });
    }

    #[Computed]
    public function emailGroups()
    {
        $groups = DB::table('users')
            ->select('email', DB::raw('COUNT(*) as total'))
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->groupBy('email')
            ->having('total', '>', 1)
            ->get();

        return $groups->map(function ($group) {
            $users = User::query()
                ->where('email', $group->email)
                ->with('userProfile.batch')
                ->get(['id', 'name', 'email', 'created_at']);

            return [
                'key'   => $group->email,
                'count' => $group->total,
                'users' => $users,
            ];
        });
    }

    #[Computed]
    public function totalIssues(): int
    {
        return $this->nameGroups->count()
            + $this->contactGroups->count()
            + $this->emailGroups->count();
    }
};