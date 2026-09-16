<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OrganizationDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $user->load('organization');

        return view('dashboards.organization', [
            'user' => $user,
            'organization' => $user->organization,
        ]);
    }
}