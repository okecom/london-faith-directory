<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
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

    public function create(
        Organization $organization
    ): View {
        return view('groups.manage.create', [
            'organization' => $organization,
        ]);
    }   


    public function store(
        Request $request,
        Organization $organization
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
                'contact_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'telephone' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],
            ],
            [
                'name.required' =>
                    'Please enter the group name.',
            ]
        );

        /*
        * Prevent duplicate active group names
        * within the same organisation.
        */
        $duplicateExists = Group::where(
                'organization_id',
                $organization->id
            )
            ->where('name', $validated['name'])
            ->exists();

        if ($duplicateExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'This organisation already has a group with that name.',
                ]);
        }

        $group = Group::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'description' =>
                $validated['description'] ?? null,
            'contact_name' =>
                $validated['contact_name'] ?? null,
            'telephone' =>
                $validated['telephone'] ?? null,
            'email' =>
                $validated['email'] ?? null,

            /*
            * Only the automatically created Head Office
            * group receives this status.
            */
            'is_head_office' => false,
        ]);

        return redirect()
            ->route('groups.manage.index', [
                'organization_id' => $organization->id,
            ])
            ->with(
                'success',
                'Group #' .
                $group->id .
                ' (' .
                $group->name .
                ') was created successfully.'
            );
    }

public function show(Group $group): View
{
    $group->load('organization');

    return view('groups.manage.show', [
        'group' => $group,
    ]);
}


public function edit(Group $group): View
{
    $group->load('organization');

    return view('groups.manage.edit', [
        'group' => $group,
    ]);
}


public function update(
    Request $request,
    Group $group
): RedirectResponse {
    $validated = $request->validate(
        [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'telephone' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
        ],
        [
            'name.required' =>
                'Please enter the group name.',
        ]
    );

    /*
     * Head Office keeps its reserved name.
     */
    if ($group->is_head_office) {
        $validated['name'] = 'Head Office';
    }

    /*
     * Check for another active group with
     * the same name in this organisation.
     */
    $duplicateExists = Group::where(
            'organization_id',
            $group->organization_id
        )
        ->where('name', $validated['name'])
        ->where('id', '!=', $group->id)
        ->exists();

    if ($duplicateExists) {
        return back()
            ->withInput()
            ->withErrors([
                'name' =>
                    'This organisation already has a group with that name.',
            ]);
    }

    $group->update([
        'name' => $validated['name'],
        'description' =>
            $validated['description'] ?? null,
        'contact_name' =>
            $validated['contact_name'] ?? null,
        'telephone' =>
            $validated['telephone'] ?? null,
        'email' =>
            $validated['email'] ?? null,
    ]);

    return redirect()
        ->route('groups.manage.index', [
            'organization_id' =>
                $group->organization_id,
        ])
        ->with(
            'success',
            'Group #' .
            $group->id .
            ' (' .
            $group->name .
            ') was updated successfully.'
        );
}

}