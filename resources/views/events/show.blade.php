@extends('layouts.app')

@section('title', $event->name)

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        {{ $event->name }}
    </h1>


    <div class="selected-organisation">

        <h2>
            {{ $event->group->organization->name }}
        </h2>

        <p>
            Group:
            {{ $event->group->name }}
        </p>

    </div>


    <div class="group-details">

        <dl>

            <dt>Event Type</dt>
            <dd>
                {{ $event->eventType->name }}
            </dd>

            <dt>Religion</dt>
            <dd>
                {{ $event->group
                    ->organization
                    ->denomination
                    ->religion
                    ->name }}
            </dd>

            <dt>Denomination</dt>
            <dd>
                {{ $event->group
                    ->organization
                    ->denomination
                    ->name }}
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
                {{ $event->end_datetime
                    ? $event->end_datetime
                        ->format('d/m/Y H:i')
                    : 'Not specified' }}
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

            <dt>Contact</dt>
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

            <dt>Description</dt>
            <dd>
                {{ $event->description
                    ?: 'No description provided.' }}
            </dd>

        </dl>

    </div>


    <div class="page-actions">

        <a
            href="{{ route('events.search') }}"
            class="text-link"
        >
            Back to Event Finder
        </a>

    </div>

</div>

@endsection