<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;

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

    public function create(Group $group): View
    {
        $group->load('organization');

        $eventTypes = EventType::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

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
            'group_id' => $group->id,
            'event_type_id' => $validated['event_type_id'],
            'location_id' => $validated['location_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'contact_name' => $validated['contact_name'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        return redirect()
            ->route('events.manage.index', [
                'organization_id' => $group->organization_id,
                'group_id' => $group->id,
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
        $event->load('group.organization');

        $eventTypes = EventType::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

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
    
}