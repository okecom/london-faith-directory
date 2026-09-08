<?php

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