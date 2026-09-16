<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboards.user');
    }
}