<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupManagementController extends Controller
{
    public function index(Request $request): View
    {
        $organizations = Organization::orderBy('name')
            ->get();

        $selectedOrganization = null;
        $groups = collect();

        if ($request->filled('organization_id')) {

            $selectedOrganization = Organization::findOrFail(
                $request->input('organization_id')
            );

            $groups = $selectedOrganization
                ->groups()
                ->orderByDesc('is_head_office')
                ->orderBy('name')
                ->get();
        }

        return view('groups.manage.index', [
            'organizations' => $organizations,
            'selectedOrganization' => $selectedOrganization,
            'groups' => $groups,
        ]);
    }
}