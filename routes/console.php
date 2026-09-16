<?php

use App\Models\TracerStudy;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $oneYearAgo = now()->subYear();

    // Alumni whose tracer study is a year or more old — remind them to update
    TracerStudy::where('updated_at', '<=', $oneYearAgo)
        ->with('user')
        ->get()
        ->each(function ($tracerStudy) {
            if ($tracerStudy->user) {
                EmailTemplateService::send(
                    'please-update-your-alumni-profile',
                    $tracerStudy->user->email,
                    ['name' => $tracerStudy->user->name]
                );
            }
        });

    // Alumni who never submitted a tracer study at all — remind them too
    User::role('alumni')
        ->whereDoesntHave('tracerStudy')
        ->get()
        ->each(function ($user) {
            EmailTemplateService::send(
                'please-update-your-alumni-profile',
                $user->email,
                ['name' => $user->name]
            );
        });
})->yearly()->name('annual-tracer-reminder')->withoutOverlapping();
