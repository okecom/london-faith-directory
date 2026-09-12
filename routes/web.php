<?php
use App\Http\Controllers\GroupManagementController;
use App\Http\Controllers\OrganizationManagementController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;




Route::get(
    '/manage/organizations/archived',
    [OrganizationManagementController::class, 'archived']
)->name('organizations.manage.archived');


Route::patch(
    '/manage/organizations/{organization}/restore',
    [OrganizationManagementController::class, 'restore']
)->name('organizations.manage.restore');

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