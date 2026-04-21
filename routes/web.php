<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'public::pages.index')->name('home');
Route::livewire('/about', 'public::pages.about-page')->name('about');
Route::livewire('/contact', 'public::pages.contact-page')->name('contact');
Route::livewire('/events', 'public::pages.event-page')->name('events');
Route::livewire('/login', 'auth::login')->name('login');

Route::middleware('auth', 'role:super-admin')->prefix('super-admin')->group(function(){
    Route::livewire('/dashboard', 'super-admin::pages.dashboard')->name('super-admin.dashboard');

    Route::livewire('/roles/view', 'super-admin::pages.role.view-role')->name('view-role');
    Route::livewire('/roles/create', 'super-admin::pages.role.create-role')->name('create-role');
    Route::livewire('/roles/updte/{role}', 'super-admin::pages.role.update-role')->name('update-role');

    Route::livewire('/user/view', 'super-admin::pages.user.view-user')->name('admin.user.view');
    Route::livewire('/user/create', 'super-admin::pages.user.create-user')->name('admin.user.create');
    Route::livewire('/users/edit/{user}', 'super-admin::pages.user.update-user')->name('admin.user.edit');


});

Route::middleware('auth', 'role:admin|super-admin')->prefix('admin')->group(function(){
    Route::livewire('/dashboard', 'admin::pages.dashboard')->name('admin.dashboard');

});

Route::middleware('auth', 'role:alumni|super-admin')->prefix('alumni')->group(function(){
    Route::livewire('/dashboard', 'alumni::pages.dashboard')->name('alumni.dashboard');

});
