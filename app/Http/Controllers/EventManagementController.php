<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Group;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize(
            'viewAny',
            Event::class
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
        $selectedGroup = null;
        $events = collect();

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

        if (
            $selectedOrganization &&
            $request->filled('group_id')
        ) {
            /*
             * Constrain the Group to the selected
             * Organisation before using it.
             */
            $selectedGroup = Group::where(
                'organization_id',
                $selectedOrganization->id
            )
                ->findOrFail(
                    $request->input('group_id')
                );

            $this->authorize(
                'view',
                $selectedGroup
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
            'organizations' =>
                $organizations,

            'selectedOrganization' =>
                $selectedOrganization,

            'groups' =>
                $groups,

            'selectedGroup' =>
                $selectedGroup,

            'events' =>
                $events,
        ]);
    }


    public function create(Group $group): View
    {
        /*
         * The Group determines which Organisation
         * will own the new Event.
         */
        $this->authorize(
            'create',
            [
                Event::class,
                $group,
            ]
        );

        $group->load('organization');

        $eventTypes =
            EventType::orderBy('name')->get();

        $locations =
            Location::orderBy('name')->get();

        return view('events.manage.create', [
            'group' => $group,
            'eventTypes' => $eventTypes,
            'locations' => $locations,
        ]);
    }


    public function store(
        Request $request,
        Group $group
    ): RedirectResponse {
        /*
         * Authorize before validating or creating
         * any submitted Event data.
         */
        $this->authorize(
            'create',
            [
                Event::class,
                $group,
            ]
        );

        $validated = $request->validate([
            'event_type_id' => [
                'required',
                'exists:event_types,id',
            ],

            'location_id' => [
                'required',
                'exists:locations,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'start_datetime' => [
                'required',
                'date',
            ],

            'end_datetime' => [
                'nullable',
                'date',
                'after:start_datetime',
            ],

            'venue_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
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

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],
        ]);


        $event = Event::create([
            'group_id' =>
                $group->id,

            'event_type_id' =>
                $validated['event_type_id'],

            'location_id' =>
                $validated['location_id'],

            'name' =>
                $validated['name'],

            'description' =>
                $validated['description'] ?? null,

            'start_datetime' =>
                $validated['start_datetime'],

            'end_datetime' =>
                $validated['end_datetime'] ?? null,

            'venue_name' =>
                $validated['venue_name'] ?? null,

            'address' =>
                $validated['address'] ?? null,

            'contact_name' =>
                $validated['contact_name'] ?? null,

            'telephone' =>
                $validated['telephone'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'website' =>
                $validated['website'] ?? null,
        ]);


        return redirect()
            ->route('events.manage.index', [
                'organization_id' =>
                    $group->organization_id,

                'group_id' =>
                    $group->id,
            ])
            ->with(
                'success',
                'Event #' .
                $event->id .
                ' (' .
                $event->name .
                ') was created successfully.'
            );
    }


    public function show(Event $event): View
    {
        $this->authorize(
            'view',
            $event
        );

        $event->load([
            'group.organization',
            'eventType',
            'location',
        ]);

        return view('events.manage.show', [
            'event' => $event,
        ]);
    }


    public function edit(Event $event): View
    {
        $this->authorize(
            'update',
            $event
        );

        $event->load('group.organization');

        $eventTypes =
            EventType::orderBy('name')->get();

        $locations =
            Location::orderBy('name')->get();

        return view('events.manage.edit', [
            'event' => $event,
            'eventTypes' => $eventTypes,
            'locations' => $locations,
        ]);
    }


    public function update(
        Request $request,
        Event $event
    ): RedirectResponse {
        /*
         * Authorize before validating or changing
         * any submitted data.
         */
        $this->authorize(
            'update',
            $event
        );

        $validated = $request->validate([
            'event_type_id' => [
                'required',
                'exists:event_types,id',
            ],

            'location_id' => [
                'required',
                'exists:locations,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'start_datetime' => [
                'required',
                'date',
            ],

            'end_datetime' => [
                'nullable',
                'date',
                'after:start_datetime',
            ],

            'venue_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
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

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],
        ]);


        $event->update($validated);


        return redirect()
            ->route('events.manage.index', [
                'organization_id' =>
                    $event->group->organization_id,

                'group_id' =>
                    $event->group_id,
            ])
            ->with(
                'success',
                'Event #' .
                $event->id .
                ' (' .
                $event->name .
                ') was updated successfully.'
            );
    }


    public function destroy(
        Event $event
    ): RedirectResponse {
        /*
         * Protect direct Event ID manipulation.
         */
        $this->authorize(
            'delete',
            $event
        );

        $organizationId =
            $event->group->organization_id;

        $groupId =
            $event->group_id;

        $eventId =
            $event->id;

        $eventName =
            $event->name;


        $event->delete();


        return redirect()
            ->route('events.manage.index', [
                'organization_id' =>
                    $organizationId,

                'group_id' =>
                    $groupId,
            ])
            ->with(
                'success',
                'Event #' .
                $eventId .
                ' (' .
                $eventName .
                ') was archived successfully.'
            );
    }


    public function archived(
        Group $group
    ): View {
        /*
         * The Group determines ownership of the
         * archived Event collection.
         */
        $this->authorize(
            'view',
            $group
        );

        $group->load('organization');

        $events = Event::onlyTrashed()
            ->where(
                'group_id',
                $group->id
            )
            ->with([
                'eventType',
                'location',
            ])
            ->orderBy('start_datetime')
            ->paginate(5);

        return view('events.manage.archived', [
            'group' => $group,
            'events' => $events,
        ]);
    }


    public function restore(
        Group $group,
        int $event
    ): RedirectResponse {
        /*
         * Protect the parent Group before looking
         * up the archived Event.
         */
        $this->authorize(
            'view',
            $group
        );

        /*
         * Constrain the archived Event to the Group
         * in the URL. This prevents substituting an
         * Event ID belonging to another Group.
         */
        $event = Event::onlyTrashed()
            ->where(
                'group_id',
                $group->id
            )
            ->findOrFail($event);

        $this->authorize(
            'restore',
            $event
        );


        $event->restore();


        return redirect()
            ->route('events.manage.archived', [
                'group' => $group,
            ])
            ->with(
                'success',
                'Event #' .
                $event->id .
                ' (' .
                $event->name .
                ') was restored successfully.'
            );
    }
}