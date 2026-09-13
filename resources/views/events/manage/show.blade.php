@extends('layouts.app')

@section('title', 'Event Details')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        {{ $event->name }}
    </h1>

    <p class="page-introduction">
        Event record #{{ $event->id }}
    </p>


    <div class="selected-organisation">

        <h2>
            {{ $event->group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>
                {{ $event->group->name }}
            </strong>
        </p>

    </div>


    <div class="group-details">

        <dl>

            <dt>Event Type</dt>
            <dd>
                {{ $event->eventType->name }}
            </dd>


            <dt>London Location</dt>
            <dd>
                {{ $event->location->name }}
            </dd>


            <dt>Start</dt>
            <dd>
                {{ $event->start_datetime
                    ->format('d/m/Y H:i') }}
            </dd>


            <dt>End</dt>
            <dd>
                @if ($event->end_datetime)

                    {{ $event->end_datetime
                        ->format('d/m/Y H:i') }}

                @else

                    Not specified

                @endif
            </dd>


            <dt>Venue</dt>
            <dd>
                {{ $event->venue_name
                    ?: 'Not provided' }}
            </dd>


            <dt>Address</dt>
            <dd>
                {{ $event->address
                    ?: 'Not provided' }}
            </dd>


            <dt>Contact Name</dt>
            <dd>
                {{ $event->contact_name
                    ?: 'Not provided' }}
            </dd>


            <dt>Telephone</dt>
            <dd>
                {{ $event->telephone
                    ?: 'Not provided' }}
            </dd>


            <dt>Email</dt>
            <dd>
                {{ $event->email
                    ?: 'Not provided' }}
            </dd>


            <dt>Website</dt>
            <dd>

                @if ($event->website)

                    <a
                        href="{{ $event->website }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $event->website }}
                    </a>

                @else

                    Not provided

                @endif

            </dd>


            <dt>Description</dt>
            <dd>
                {{ $event->description
                    ?: 'No description provided.' }}
            </dd>

        </dl>

    </div>


    <div class="page-actions">

        <a
            href="{{ route(
                'events.manage.edit',
                $event
            ) }}"
            class="button button-edit"
        >
            Edit Event
        </a>

        <a
            href="{{ route(
                'events.manage.index',
                [
                    'organization_id' =>
                        $event->group
                            ->organization_id,

                    'group_id' =>
                        $event->group_id,
                ]
            ) }}"
            class="text-link"
        >
            Back to Events
        </a>

    </div>

</div>

@endsection