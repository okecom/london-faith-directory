<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize(
            'viewAny',
            Group::class
        );

        $organizationsQuery =
            Organization::orderBy('name');

        /*
         * Organisation Administrators may select
         * only their assigned organisation.
         */
        if (
            $request->user()->role ===
            User::ROLE_ORGANISATION_ADMIN
        ) {
            $organizationsQuery->where(
                'id',
                $request->user()->organization_id
            );
        }

        $organizations =
            $organizationsQuery->get();

        $selectedOrganization = null;
        $groups = collect();

        if ($request->filled('organization_id')) {
            $selectedOrganization =
                Organization::findOrFail(
                    $request->input(
                        'organization_id'
                    )
                );

            /*
             * Protect against changing
             * organization_id in the query string.
             */
            $this->authorize(
                'view',
                $selectedOrganization
            );

            $groups = $selectedOrganization
                ->groups()
                ->orderByDesc('is_head_office')
                ->orderBy('name')
                ->get();
        }

        return view('groups.manage.index', [
            'organizations' =>
                $organizations,

            'selectedOrganization' =>
                $selectedOrganization,

            'groups' =>
                $groups,
        ]);
    }


    public function create(
        Organization $organization
    ): View {
        $this->authorize(
            'create',
            [
                Group::class,
                $organization,
            ]
        );

        return view('groups.manage.create', [
            'organization' => $organization,
        ]);
    }


    public function store(
        Request $request,
        Organization $organization
    ): RedirectResponse {
        /*
         * Authorize before validating or creating
         * any submitted data.
         */
        $this->authorize(
            'create',
            [
                Group::class,
                $organization,
            ]
        );

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
         * Check active AND archived groups because
         * the database unique constraint still
         * includes soft-deleted records.
         */
        $duplicateGroup = Group::withTrashed()
            ->where(
                'organization_id',
                $organization->id
            )
            ->where(
                'name',
                $validated['name']
            )
            ->first();


        if ($duplicateGroup) {
            /*
             * If the group exists but is archived,
             * tell the user to restore it instead
             * of attempting to create another copy.
             */
            if ($duplicateGroup->trashed()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'name' =>
                            'A group with this name is archived. Restore it from Archived Groups instead.',
                    ]);
            }


            /*
             * Otherwise an active group already
             * exists with the same name.
             */
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'This organisation already has a group with that name.',
                ]);
        }


        $group = Group::create([
            'organization_id' =>
                $organization->id,

            'name' =>
                $validated['name'],

            'description' =>
                $validated['description'] ?? null,

            'contact_name' =>
                $validated['contact_name'] ?? null,

            'telephone' =>
                $validated['telephone'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            /*
             * Only the automatically created
             * Head Office group receives
             * Head Office status.
             */
            'is_head_office' =>
                false,
        ]);


        return redirect()
            ->route('groups.manage.index', [
                'organization_id' =>
                    $organization->id,
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
        $this->authorize(
            'view',
            $group
        );

        $group->load('organization');

        return view('groups.manage.show', [
            'group' => $group,
        ]);
    }


    public function edit(Group $group): View
    {
        $this->authorize(
            'update',
            $group
        );

        $group->load('organization');

        return view('groups.manage.edit', [
            'group' => $group,
        ]);
    }


    public function update(
        Request $request,
        Group $group
    ): RedirectResponse {
        /*
         * Authorize before validating or changing
         * any submitted data.
         */
        $this->authorize(
            'update',
            $group
        );

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
         * The Head Office group keeps its
         * reserved name.
         */
        if ($group->is_head_office) {
            $validated['name'] = 'Head Office';
        }


        /*
         * Check active AND archived groups for
         * another record using the same name.
         *
         * Exclude the current group itself.
         */
        $duplicateGroup = Group::withTrashed()
            ->where(
                'organization_id',
                $group->organization_id
            )
            ->where(
                'name',
                $validated['name']
            )
            ->where(
                'id',
                '!=',
                $group->id
            )
            ->first();


        if ($duplicateGroup) {
            if ($duplicateGroup->trashed()) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'name' =>
                            'A group with this name is archived. Restore it from Archived Groups instead.',
                    ]);
            }


            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'This organisation already has a group with that name.',
                ]);
        }


        $group->update([
            'name' =>
                $validated['name'],

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


    public function destroy(
        Group $group
    ): RedirectResponse {
        /*
         * First establish that this administrator
         * owns the Group.
         */
        $this->authorize(
            'delete',
            $group
        );

        /*
         * The Head Office business rule remains
         * independent of ownership authorization.
         */
        abort_if(
            $group->is_head_office,
            403,
            'The Head Office group cannot be archived.'
        );


        $organizationId =
            $group->organization_id;

        $groupName =
            $group->name;

        $groupId =
            $group->id;


        $group->delete();


        return redirect()
            ->route('groups.manage.index', [
                'organization_id' =>
                    $organizationId,
            ])
            ->with(
                'success',
                'Group #' .
                $groupId .
                ' (' .
                $groupName .
                ') was archived successfully.'
            );
    }


    public function archived(
        Organization $organization
    ): View {
        /*
         * The parent Organisation itself must belong
         * to the Organisation Administrator.
         *
         * Site Administrators pass through the
         * OrganizationPolicy before() method.
         */
        $this->authorize(
            'view',
            $organization
        );

        $groups = Group::onlyTrashed()
            ->where(
                'organization_id',
                $organization->id
            )
            ->orderBy('name')
            ->get();


        return view('groups.manage.archived', [
            'organization' =>
                $organization,

            'groups' =>
                $groups,
        ]);
    }


    public function restore(
        Organization $organization,
        int $group
    ): RedirectResponse {
        /*
         * Protect the parent Organisation before
         * looking up its archived Group.
         */
        $this->authorize(
            'view',
            $organization
        );

        /*
         * Constraining the lookup by organization_id
         * prevents a Group ID from another Organisation
         * being substituted into this nested URL.
         */
        $group = Group::onlyTrashed()
            ->where(
                'organization_id',
                $organization->id
            )
            ->findOrFail($group);

        $this->authorize(
            'restore',
            $group
        );


        $group->restore();


        return redirect()
            ->route('groups.manage.archived', [
                'organization' =>
                    $organization,
            ])
            ->with(
                'success',
                'Group #' .
                $group->id .
                ' (' .
                $group->name .
                ') was restored successfully.'
            );
    }
}