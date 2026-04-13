<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public::pages.index')->name('home');

Route::prefix('super-admin')->group(function(){
    Route::livewire('/dashboard', 'super-admin::pages.dashboard')->name('super-admin.dashboard');

});

Route::prefix('admin')->group(function(){
    Route::livewire('/dashboard', 'admin::pages.dashboard')->name('admin.dashboard');

});

Route::prefix('alumni')->group(function(){
    Route::livewire('/dashboard', 'alumni::pages.dashboard')->name('alumni.dashboard');

});
