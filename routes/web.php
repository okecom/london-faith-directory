<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventManagementController;
use App\Http\Controllers\GroupManagementController;
use App\Http\Controllers\OrganizationManagementController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\OrganizationDashboardController;
use App\Http\Controllers\OrganizationAdminController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\MediaManagementController;
use App\Http\Controllers\MediaController;


// --------------------------------------------------
// PUBLIC ROUTES
// --------------------------------------------------
Route::get(
    '/',
    [OrganizationController::class, 'search']
)->name('organizations.search');


Route::get(
    '/organizations/results',
    [OrganizationController::class, 'results']
)->name('organizations.results');

Route::get(
    '/organizations/{organization}',
    [OrganizationController::class, 'show']
)->name('organizations.show');

Route::get(
    '/events',
    [EventController::class, 'search']
)->name('events.search');


Route::get(
    '/events/results',
    [EventController::class, 'results']
)->name('events.results');

Route::get(
    '/events/{event}',
    [EventController::class, 'show']
)->name('events.show');

Route::get(
    '/media/{media}',
    [MediaController::class, 'show']
)->name('media.show');

Route::get(
    '/media/{media}/access',
    [MediaController::class, 'access']
)->name('media.access');


// --------------------------------------------------
// MANAGED ROUTES
// --------------------------------------------------

Route::middleware([
    'auth',
    'management',
    'password.changed',
])->group(function () {


// Organizations routes
            Route::get(
                '/manage/organizations/archived',
                [OrganizationManagementController::class, 'archived']
            )->name('organizations.manage.archived');


            Route::patch(
                '/manage/organizations/{organization}/restore',
                [OrganizationManagementController::class, 'restore']
            )->name('organizations.manage.restore');


            Route::get(
                '/manage/organizations',
                [OrganizationManagementController::class, 'index']
            )->name('organizations.manage.index');


            Route::get(
                '/manage/organizations/create',
                [OrganizationManagementController::class, 'create']
            )->name('organizations.manage.create');


            Route::post(
                '/manage/organizations',
                [OrganizationManagementController::class, 'store']
            )->name('organizations.manage.store');


            Route::get(
                '/manage/organizations/{organization}/edit',
                [OrganizationManagementController::class, 'edit']
            )->name('organizations.manage.edit');


            Route::put(
                '/manage/organizations/{organization}',
                [OrganizationManagementController::class, 'update']
            )->name('organizations.manage.update');

            Route::delete(
                '/manage/organizations/{organization}',
                [OrganizationManagementController::class, 'destroy']
            )->name('organizations.manage.destroy');

     // Group routes      
            Route::get(
                '/manage/groups',
                [GroupManagementController::class, 'index']
            )->name('groups.manage.index');

            Route::get(
                '/manage/organizations/{organization}/groups/create',
                [GroupManagementController::class, 'create']
            )->name('groups.manage.create');

            Route::post(
                '/manage/organizations/{organization}/groups',
                [GroupManagementController::class, 'store']
            )->name('groups.manage.store');

            Route::get(
                '/manage/groups/{group}',
                [GroupManagementController::class, 'show']
            )->name('groups.manage.show');

            Route::get(
                '/manage/groups/{group}/edit',
                [GroupManagementController::class, 'edit']
            )->name('groups.manage.edit');

            Route::put(
                '/manage/groups/{group}',
                [GroupManagementController::class, 'update']
            )->name('groups.manage.update');

            Route::delete(
                '/manage/groups/{group}',
                [GroupManagementController::class, 'destroy']
            )->name('groups.manage.destroy');


            Route::get(
                '/manage/organizations/{organization}/groups/archived',
                [GroupManagementController::class, 'archived']
            )->name('groups.manage.archived');


            Route::patch(
                '/manage/organizations/{organization}/groups/{group}/restore',
                [GroupManagementController::class, 'restore']
            )->name('groups.manage.restore');

// Events routes
            Route::get(
                '/manage/events',
                [EventManagementController::class, 'index']
            )->name('events.manage.index');

            Route::get(
                '/manage/groups/{group}/events/create',
                [EventManagementController::class, 'create']
            )->name('events.manage.create');

            Route::post(
                '/manage/groups/{group}/events',
                [EventManagementController::class, 'store']
            )->name('events.manage.store');

            Route::get(
                '/manage/events/{event}',
                [EventManagementController::class, 'show']
            )->name('events.manage.show');

            Route::get(
                '/manage/events/{event}/edit',
                [EventManagementController::class, 'edit']
            )->name('events.manage.edit');

            Route::put(
                '/manage/events/{event}',
                [EventManagementController::class, 'update']
            )->name('events.manage.update');

            Route::delete(
                '/manage/events/{event}',
                [EventManagementController::class, 'destroy']
            )->name('events.manage.destroy');


            Route::get(
                '/manage/groups/{group}/events/archived',
                [EventManagementController::class, 'archived']
            )->name('events.manage.archived');


            Route::patch(
                '/manage/groups/{group}/events/{event}/restore',
                [EventManagementController::class, 'restore']
            )->name('events.manage.restore');


         // Media routes

            Route::get(
                '/manage/media',
                [MediaManagementController::class, 'index']
            )->name('media.manage.index');

            Route::get(
                '/manage/groups/{group}/media/create',
                [MediaManagementController::class, 'create']
            )->name('media.manage.create');

            Route::post(
                '/manage/groups/{group}/media',
                [MediaManagementController::class, 'store']
            )->name('media.manage.store');

            Route::get(
                '/manage/media/{media}',
                [MediaManagementController::class, 'show']
            )->name('media.manage.show');

            Route::get(
                '/manage/media/{media}/edit',
                [MediaManagementController::class, 'edit']
            )->name('media.manage.edit');

            Route::put(
                '/manage/media/{media}',
                [MediaManagementController::class, 'update']
            )->name('media.manage.update');

            Route::delete(
                '/manage/media/{media}',
                [MediaManagementController::class, 'destroy']
            )->name('media.manage.destroy');

            Route::get(
                '/manage/groups/{group}/media/archived',
                [MediaManagementController::class, 'archived']
            )->name('media.manage.archived');

            Route::patch(
                '/manage/groups/{group}/media/{media}/restore',
                [MediaManagementController::class, 'restore']
            )->name('media.manage.restore');



});

// --------------------------------------------------
// AUTHENTICATED PASSWORD CHANGE ROUTES
// INSERT THE NEW CODE HERE
// --------------------------------------------------

Route::middleware('auth')->group(function () {
    Route::get(
        '/change-password',
        [ChangePasswordController::class, 'edit']
    )->name('password.change.edit');

    Route::put(
        '/change-password',
        [ChangePasswordController::class, 'update']
    )->name('password.change.update');
});


// --------------------------------------------------
// SITE ADMIN ROUTES
// --------------------------------------------------


Route::middleware([
    'auth',
    'role:' . \App\Models\User::ROLE_SITE_ADMIN,
])->group(function () {
    Route::get('/site/dashboard', [SiteDashboardController::class, 'index'])
        ->name('site.dashboard');

   Route::get(
        '/site/organization-admins',
        [OrganizationAdminController::class, 'index']
    )->name('site.organization-admins.index');

    Route::get(
        '/site/organization-admins/create',
        [OrganizationAdminController::class, 'create']
    )->name('site.organization-admins.create');

    Route::post(
        '/site/organization-admins',
        [OrganizationAdminController::class, 'store']
    )->name('site.organization-admins.store'); 
    
    Route::patch(
        '/site/organization-admins/{user}/deactivate',
        [OrganizationAdminController::class, 'deactivate']
    )->name('site.organization-admins.deactivate');

    Route::patch(
        '/site/organization-admins/{user}/reactivate',
        [OrganizationAdminController::class, 'reactivate']
    )->name('site.organization-admins.reactivate');


    Route::get(
        '/site/organization-admins/{user}/replace',
        [OrganizationAdminController::class, 'replace']
    )->name('site.organization-admins.replace');

    Route::post(
        '/site/organization-admins/{user}/replace',
        [OrganizationAdminController::class, 'storeReplacement']
    )->name('site.organization-admins.store-replacement');

});



// --------------------------------------------------
// REGISTERED USER ROUTES
// --------------------------------------------------


Route::middleware([
    'auth',
    'role:' . \App\Models\User::ROLE_REGISTERED_USER,
])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
        ->name('user.dashboard');
});




// --------------------------------------------------
// ORGANISATION ADMIN ROUTES
// --------------------------------------------------


Route::middleware([
    'auth',
    'role:' . \App\Models\User::ROLE_ORGANISATION_ADMIN,
    'password.changed',
])->group(function () {
    Route::get(
        '/organisation/dashboard',
        [OrganizationDashboardController::class, 'index']
    )->name('organisation.dashboard');
});
