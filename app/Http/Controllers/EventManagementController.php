<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventManagementController extends Controller
{
    public function index(Request $request): View
    {
        $organizations = Organization::orderBy('name')
            ->get();

        $selectedOrganization = null;
        $groups = collect();
        $selectedGroup = null;
        $events = collect();

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

        if (
            $selectedOrganization &&
            $request->filled('group_id')
        ) {
            $selectedGroup = Group::where(
                    'organization_id',
                    $selectedOrganization->id
                )
                ->findOrFail(
                    $request->input('group_id')
                );

            $events = $selectedGroup
                ->events()
                ->with([
                    'eventType',
                    'location',
                ])
                ->orderBy('start_datetime')
                ->paginate(5)
                ->withQueryString();
        }

        return view('events.manage.index', [
            'organizations' => $organizations,
            'selectedOrganization' => $selectedOrganization,
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'events' => $events,
        ]);
    }
}