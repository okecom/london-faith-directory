@extends('layouts.app')

@section('title', 'Archived Events')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Archived Events
    </h1>


    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>
                {{ $group->name }}
            </strong>
        </p>

    </div>


    @if (session('success'))

        <div
            class="success-message"
            role="status"
        >
            {{ session('success') }}
        </div>

    @endif


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
                        <th>Archived</th>
                        <th>Action</th>
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
                                {{ $event->deleted_at
                                    ->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'events.manage.restore',
                                        [
                                            'group' =>
                                                $group,
                                            'event' =>
                                                $event->id,
                                        ]
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="button button-small"
                                    >
                                        Restore
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{ $events->links() }}

    @else

        <div class="no-results">

            <h2>No archived events</h2>

            <p>
                This group currently has
                no archived events.
            </p>

        </div>

    @endif


    <div class="page-actions">

        <a
            href="{{ route(
                'events.manage.index',
                [
                    'organization_id' =>
                        $group->organization_id,

                    'group_id' =>
                        $group->id,
                ]
            ) }}"
            class="text-link"
        >
            Back to Events
        </a>

    </div>

</div>

@endsection