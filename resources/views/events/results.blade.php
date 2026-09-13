@extends('layouts.app')

@section('title', 'Event Search Results')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Event Search Results
    </h1>


    <div class="selected-organisation">

        <p>
            <strong>Event Type:</strong>
            {{ $eventType->name }}
        </p>

        <p>
            <strong>Religion:</strong>
            {{ $denomination->religion->name }}
        </p>

        <p>
            <strong>Denomination:</strong>
            {{ $denomination->name }}
        </p>

    </div>


    @if ($events->count())

        <div class="results-table-wrapper">

            <table class="results-table">

                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Organisation</th>
                        <th>Group</th>
                        <th>Location</th>
                        <th>Date / Time</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($events as $event)

                        <tr>

                            <td>
                                <a
                                    href="{{ route(
                                        'events.show',
                                        $event
                                    ) }}"
                                >
                                    {{ $event->name }}
                                </a>
                            </td>

                            <td>
                                {{ $event->group
                                    ->organization
                                    ->name }}
                            </td>

                            <td>
                                {{ $event->group->name }}
                            </td>

                            <td>
                                {{ $event->location->name }}
                            </td>

                            <td>
                                {{ $event->start_datetime
                                    ->format('d/m/Y H:i') }}
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
                No upcoming events match
                your selected event type
                and denomination.
            </p>

        </div>

    @endif


    <div class="page-actions">

        <a
            href="{{ route('events.search') }}"
            class="text-link"
        >
            New Event Search
        </a>

    </div>

</div>

@endsection