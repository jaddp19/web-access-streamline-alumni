<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


Route::livewire('/', 'public::pages.index')->name('home');
Route::livewire('/about', 'public::pages.about-page')->name('about');
Route::livewire('/contact', 'public::pages.contact-page')->name('contact');
Route::livewire('/departments', 'public::pages.department-page')->name('departments');

Route::livewire('/login', 'auth::login')->name('login');
Route::livewire('/form', 'auth::form.form-answer')->name('form');
Route::livewire('/privacy-policy', 'auth::privacy-policy')->name('privacy-policy');
Route::livewire('/terms-and-condition', 'auth::terms-and-condition')->name('terms-and-conditions');

//forms
Route::middleware('auth')->group(function () {
    Route::livewire('/form', 'auth::form.form-answer')->name('form');
    Route::livewire('/update-form', 'auth::form.update-form')->name('update-form');
});

Route::middleware(['auth', 'role:registrar'])->prefix('super-admin')->group(function () {
    Route::livewire('/dashboard', 'super-admin::pages.dashboard')->name('super-admin.dashboard');
    Route::livewire('/settings', 'super-admin::pages.settings')->name('super-admin.settings');

    Route::livewire('/roles/view', 'super-admin::pages.role.view-role')->name('view-role');
    Route::livewire('/roles/create', 'super-admin::pages.role.create-role')->name('create-role');
    Route::livewire('/roles/update/{role}', 'super-admin::pages.role.update-role')->name('update-role');

    Route::livewire('/user/view', 'super-admin::pages.user.view-user')->name('super-admin.user.view');

    //single view alumni
    Route::livewire('/alumni/view/{user}', 'super-admin::pages.user.view-single-user')->name('super-admin.alumni.view-single');

    Route::livewire('/user/create', 'super-admin::pages.user.create-user')->name('super-admin.user.create');
    Route::livewire('/users/update/{user}', 'super-admin::pages.user.update-user')->name('super-admin.user.update');

    Route::livewire('/admin/assign/view', 'super-admin::pages.program-head.view-assign-admin')->name('super-admin.assign.view');
    Route::livewire('/admin/assign/create', 'super-admin::pages.program-head.create-assign-admin')->name('super-admin.assign.create');
    Route::livewire('/admin/assign/update/{department}', 'super-admin::pages.program-head.update-assign-admin')->name('super-admin.assign.update');

    Route::livewire('/department/view', 'super-admin::pages.department.view-department')->name('super-admin.department.view');
    Route::livewire('/courses/view', 'super-admin::pages.course.view-course')->name('super-admin.courses.view');
    Route::livewire('/course/create', 'super-admin::pages.course.create-course')->name('super-admin.course.create');
    Route::livewire('/course/update/{course}', 'super-admin::pages.course.update-course')->name('super-admin.course.update');

    Route::livewire('/department/create', 'super-admin::pages.department.create-department')->name('super-admin.department.create');
    Route::livewire('/department/update/{department}', 'super-admin::pages.department.update-department')->name('super-admin.department.update');

    Route::livewire('/batch/view', 'super-admin::pages.batch.view-batch')->name('super-admin.batch.view');
    Route::livewire('/batch/create', 'super-admin::pages.batch.create-batch')->name('super-admin.batch.create');
    Route::livewire('/batch/update/{batch}', 'super-admin::pages.batch.update-batch')->name('super-admin.batch.update');

    Route::livewire('/email/view', 'super-admin::pages.email.view-email')->name('super-admin.email.view');
    Route::livewire('/email/create', 'super-admin::pages.email.create-email')->name('super-admin.email.create');
    Route::livewire('/email/update/{email}', 'super-admin::pages.email.update-email')->name('super-admin.email.update');

    Route::livewire('/company/view', 'super-admin::pages.company.view-company')->name('super-admin.company.view');
    Route::livewire('/company/create', 'super-admin::pages.company.create-company')->name('super-admin.company.create');
    Route::livewire('/company/update/{company}', 'super-admin::pages.company.update-company')->name('super-admin.company.update');

    Route::livewire('/post/view', 'super-admin::pages.post.view-post')->name('super-admin.post.view');
    Route::livewire('/post/create', 'super-admin::pages.post.create-post')->name('super-admin.post.create');
    Route::livewire('/post/update{post}', 'super-admin::pages.post.update-post')->name('super-admin.post.update');

    Route::livewire('/verification-queue', 'super-admin::verification.verification-queue')->name('super-admin.verification-queue');
});

Route::middleware(['auth', 'role:program head|registrar'])->prefix('admin')->group(function () {
    Route::livewire('/dashboard', 'admin::pages.dashboard')->name('admin.dashboard');
    Route::livewire('/settings', 'admin::pages.settings')->name('admin.settings');

    Route::livewire('/alumni/view', 'admin::pages.alumni.view-alumni')->name('admin.alumni.view');
    Route::livewire('/alumni/view/{user}', 'admin::pages.alumni.view-single-alumni')->name('admin.alumni.view-single');
    Route::livewire('/alumni/create', 'admin::pages.alumni.create-alumni')->name('admin.alumni.create');
    Route::livewire('/alumni/update/{user}', 'admin::pages.alumni.update-alumni')->name('admin.alumni.update');

    Route::livewire('/post/view-post', 'admin::pages.post.view-post')->name('admin.post.view');
    Route::livewire('/post/create-post', 'admin::pages.post.create-post')->name('admin.post.create');
    Route::livewire('/post/update-post{post}', 'admin::pages.post.update-post')->name('admin.post.update');


    Route::livewire('/verification-queue', 'admin::pages.verification-queue')->name('admin.verification-queue');

});

Route::middleware(['auth', 'role:alumni|registrar'])->prefix('alumni')->group(function () {
    Route::livewire('/dashboard', 'alumni::pages.dashboard')->name('alumni.dashboard');

    Route::livewire('/profile/view', 'alumni::pages.profile.view-profile')->name('alumni.profile');
    Route::livewire('/profile/update/{user}', 'alumni::pages.profile.update-profile')->name('alumni.profile.update');
    Route::livewire('/profile/update-educational/{alumni}', 'alumni::pages.profile.update-educational-background')->name('alumni.profile.update-educational');
    Route::livewire('/profile/create-employment', 'alumni::pages.profile.create-work-history')->name('alumni.profile.create-employment');
    Route::livewire('/profile/update-employment/{history}', 'alumni::pages.profile.update-work-history')->name('alumni.profile.update-employment');
    
    Route::livewire('/settings', 'alumni::pages.settings')->name('alumni.settings');
    Route::livewire('/notification', 'alumni::pages.notification.view-notification')->name('alumni.notification');
    Route::livewire('/notification/{post}', 'alumni::pages.notification.view-single-notification')->name('alumni.view-notification');
    Route::livewire('/message', 'alumni::pages.message.alumni-message')->name('alumni.message');
});

Broadcast::routes();
