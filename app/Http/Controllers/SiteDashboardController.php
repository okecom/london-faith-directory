<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SiteDashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboards.site');
    }
}