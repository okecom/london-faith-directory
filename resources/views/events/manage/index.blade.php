@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')

<div class="page-card">

    @if (session('success'))

        <div
            class="success-message"
            role="status"
        >
            {{ session('success') }}
        </div>

    @endif

    <h1 class="page-heading">
        Manage Events
    </h1>

    <p class="page-introduction">
        Select an organisation and one of its groups
        to view scheduled events.
    </p>


    <form
        method="GET"
        action="{{ route('events.manage.index') }}"
        class="event-management-search"
    >

        <div class="form-group">

            <label for="organization_id">
                Organisation
            </label>

            <select
                name="organization_id"
                id="organization_id"
                required
                onchange="this.form.submit()"
            >

                <option value="">
                    Select an organisation
                </option>

                @foreach ($organizations as $organization)

                    <option
                        value="{{ $organization->id }}"
                        @selected(
                            request('organization_id') ==
                            $organization->id
                        )
                    >
                        #{{ $organization->id }}
                        -
                        {{ $organization->name }}
                    </option>

                @endforeach

            </select>

        </div>


        @if ($selectedOrganization)

            <div class="form-group">

                <label for="group_id">
                    Group
                </label>

                <select
                    name="group_id"
                    id="group_id"
                    required
                >

                    <option value="">
                        Select a group
                    </option>

                    @foreach ($groups as $group)

                        <option
                            value="{{ $group->id }}"
                            @selected(
                                request('group_id') ==
                                $group->id
                            )
                        >
                            {{ $group->name }}

                            @if ($group->is_head_office)
                                (Head Office)
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>


            <button
                type="submit"
                class="button"
            >
                View Events
            </button>

        @endif

    </form>


    @if ($selectedGroup)

    <div class="page-actions">

        <a
            href="{{ route(
                'events.manage.create',
                $selectedGroup
            ) }}"
            class="button"
        >
            + Create New Event
        </a>

        <a
            href="{{ route(
                'events.manage.archived',
                $selectedGroup
            ) }}"
            class="button button-secondary"
        >
            Archived Events
        </a>


    </div>
        <div class="selected-organisation">

            <h2>
                {{ $selectedOrganization->name }}
            </h2>

            <p>
                Group:
                <strong>
                    {{ $selectedGroup->name }}
                </strong>
            </p>

        </div>


        @if ($events->count())

            <div class="results-table-wrapper">

                <table class="results-table">

                    <thead>
                        <tr>
                            <th>Event No.</th>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Starts</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($events as $event)

                            <tr>

                                <td>
                                    {{ $event->id }}
                                </td>

                                <td>
                                    {{ $event->name }}
                                </td>

                                <td>
                                    {{ $event->eventType->name }}
                                </td>

                                <td>
                                    {{ $event->location->name }}
                                </td>

                                <td>
                                    {{ $event->start_datetime
                                        ->format('d/m/Y H:i') }}
                                </td>

                                <td>

                                    <div class="crud-actions">

                                        <a
                                            href="{{ route(
                                                'events.manage.show',
                                                $event
                                            ) }}"
                                            class="button button-small"
                                        >
                                            Show
                                        </a>

                                        <a
                                            href="{{ route(
                                                'events.manage.edit',
                                                $event
                                            ) }}"
                                            class="button button-small button-edit"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'events.manage.destroy',
                                                $event
                                            ) }}"
                                            class="delete-form"
                                            onsubmit="return confirm(
                                                @js(
                                                    'Are you sure you want to archive "' .
                                                    $event->name .
                                                    '"? It will no longer appear in the active event list.'
                                                )
                                            );"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="button button-small button-delete"
                                            >
                                                Archive
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{ $events->links() }}

        @else

            <div class="no-results">

                <h2>No events found</h2>

                <p>
                    This group does not currently have
                    any active scheduled events.
                </p>

            </div>

        @endif

    @endif

</div>

@endsection