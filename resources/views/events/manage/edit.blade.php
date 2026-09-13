@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Edit Event
    </h1>

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


    @if ($errors->any())

        <div class="error-message" role="alert">

            <strong>
                Please correct the following:
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'events.manage.update',
            $event
        ) }}"
        class="organisation-form"
    >

        @csrf
        @method('PUT')


        <div class="form-group">

            <label for="name">
                Event Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old(
                    'name',
                    $event->name
                ) }}"
                required
            >

        </div>


        <div class="form-group">

            <label for="event_type_id">
                Event Type
            </label>

            <select
                id="event_type_id"
                name="event_type_id"
                required
            >

                @foreach ($eventTypes as $eventType)

                    <option
                        value="{{ $eventType->id }}"
                        @selected(
                            old(
                                'event_type_id',
                                $event->event_type_id
                            ) == $eventType->id
                        )
                    >
                        {{ $eventType->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="form-group">

            <label for="location_id">
                London Location
            </label>

            <select
                id="location_id"
                name="location_id"
                required
            >

                @foreach ($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        @selected(
                            old(
                                'location_id',
                                $event->location_id
                            ) == $location->id
                        )
                    >
                        {{ $location->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="form-group">

            <label for="start_datetime">
                Start Date and Time
            </label>

            <input
                type="datetime-local"
                id="start_datetime"
                name="start_datetime"
                value="{{ old(
                    'start_datetime',
                    $event->start_datetime
                        ->format('Y-m-d\TH:i')
                ) }}"
                required
            >

        </div>


        <div class="form-group">

            <label for="end_datetime">
                End Date and Time
            </label>

            <input
                type="datetime-local"
                id="end_datetime"
                name="end_datetime"
                value="{{ old(
                    'end_datetime',
                    $event->end_datetime
                        ?->format('Y-m-d\TH:i')
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="venue_name">
                Venue Name
            </label>

            <input
                type="text"
                id="venue_name"
                name="venue_name"
                value="{{ old(
                    'venue_name',
                    $event->venue_name
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="address">
                Event Address
            </label>

            <input
                type="text"
                id="address"
                name="address"
                value="{{ old(
                    'address',
                    $event->address
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="contact_name">
                Contact Name
            </label>

            <input
                type="text"
                id="contact_name"
                name="contact_name"
                value="{{ old(
                    'contact_name',
                    $event->contact_name
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="telephone">
                Telephone
            </label>

            <input
                type="tel"
                id="telephone"
                name="telephone"
                value="{{ old(
                    'telephone',
                    $event->telephone
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old(
                    'email',
                    $event->email
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="website">
                Website
            </label>

            <input
                type="url"
                id="website"
                name="website"
                value="{{ old(
                    'website',
                    $event->website
                ) }}"
            >

        </div>


        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="6"
            >{{ old(
                'description',
                $event->description
            ) }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button button-edit"
            >
                Save Changes
            </button>

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
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection