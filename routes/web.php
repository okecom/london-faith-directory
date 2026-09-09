<?php
use App\Http\Controllers\OrganizationManagementController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

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

