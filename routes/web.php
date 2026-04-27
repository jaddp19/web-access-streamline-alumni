<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


Route::livewire('/', 'public::pages.index')->name('home');
Route::livewire('/about', 'public::pages.about-page')->name('about');
Route::livewire('/contact', 'public::pages.contact-page')->name('contact');
Route::livewire('/departments', 'public::pages.department-page')->name('departments');

Route::livewire('/login', 'auth::login')->name('login');
Route::livewire('/register', 'auth::register')->name('register');
Route::livewire('/pop-up', 'auth::pop-up')->name('pop-up');

Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->group(function(){
    Route::livewire('/dashboard', 'super-admin::pages.dashboard')->name('super-admin.dashboard');

    Route::livewire('/roles/view', 'super-admin::pages.role.view-role')->name('view-role');
    Route::livewire('/roles/create', 'super-admin::pages.role.create-role')->name('create-role');
    Route::livewire('/roles/update/{role}', 'super-admin::pages.role.update-role')->name('update-role');

    Route::livewire('/user/view', 'super-admin::pages.user.view-user')->name('super-admin.user.view');
    Route::livewire('/alumni/view', 'super-admin::pages.user.view-alumni')->name('super-admin.alumni.view');
    Route::livewire('/admin/view', 'super-admin::pages.user.view-admin')->name('super-admin.admin.view');

    Route::livewire('/user/create', 'super-admin::pages.user.create-user')->name('super-admin.user.create');
    Route::livewire('/users/update/{user}', 'super-admin::pages.user.update-user')->name('super-admin.user.update');

    Route::livewire('/admin/assign/view', 'super-admin::pages.program-head.view-assign-admin')->name('super-admin.assign.view');
    Route::livewire('/admin/assign/create', 'super-admin::pages.program-head.create-assign-admin')->name('super-admin.assign.create');
    Route::livewire('/admin/assign/update/{programHead}', 'super-admin::pages.program-head.update-assign-admin')->name('super-admin.assign.update');

    Route::livewire('/department/view', 'super-admin::pages.department.view-department')->name('super-admin.department.view');
    Route::livewire('/courses/view', 'super-admin::pages.course.view-course')->name('super-admin.courses.view');
    Route::livewire('/course/create', 'super-admin::pages.course.create-course')->name('super-admin.course.create');
    Route::livewire('/course/update/{program}', 'super-admin::pages.course.update-course')->name('super-admin.course.update');

    Route::livewire('/department/create', 'super-admin::pages.department.create-department')->name('super-admin.department.create');
    Route::livewire('/department/update/{department}', 'super-admin::pages.department.update-department')->name('super-admin.department.update');

    Route::livewire('/batch/view', 'super-admin::pages.batch.view-batch')->name('super-admin.batch.view');
    Route::livewire('/batch/create', 'super-admin::pages.batch.create-batch')->name('super-admin.batch.create');
    Route::livewire('/batch/update/{batch}', 'super-admin::pages.batch.update-batch')->name('super-admin.batch.update');

    Route::livewire('/request/view', 'super-admin::pages.request.view-request')->name('super-admin.request.view');

    Route::livewire('/email/view', 'super-admin::pages.email.view-email')->name('super-admin.email.view');
    Route::livewire('/email/create', 'super-admin::pages.email.create-email')->name('super-admin.email.create');
    Route::livewire('/email/update/{email}', 'super-admin::pages.email.update-email')->name('super-admin.email.update');

});

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->group(function(){
    Route::livewire('/dashboard', 'admin::pages.dashboard')->name('admin.dashboard');

    Route::livewire('/alumni/view', 'admin::pages.alumni.view-alumni')->name('admin.alumni.view');
    Route::livewire('/alumni/create', 'admin::pages.alumni.create-alumni')->name('admin.alumni.create');
    Route::livewire('/alumni/update/{user}', 'admin::pages.alumni.update-alumni')->name('admin.alumni.update');
});

Route::middleware(['auth', 'role:alumni|super-admin'])->prefix('alumni')->group(function(){
    Route::livewire('/dashboard', 'alumni::pages.dashboard')->name('alumni.dashboard');

    Route::livewire('/profile/view', 'alumni::pages.profile.view-profile')->name('alumni.profile');
    Route::livewire('/profile/update/{user}', 'alumni::pages.profile.update-profile')->name('alumni.profile.update');
    Route::livewire('/profile/update-educational/{alumni}', 'alumni::pages.profile.update-educational-background')->name('alumni.profile.update-educational');

});

Broadcast::routes();