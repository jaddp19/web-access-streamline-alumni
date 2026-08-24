<?php

namespace App\Providers;

use App\Models\UserProfile;
use App\Policies\AlumniPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        UserProfile::class => AlumniPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
