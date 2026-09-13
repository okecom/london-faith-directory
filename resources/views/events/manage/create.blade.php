@extends('layouts.app')

@section('title', 'Create Event')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Create Event
    </h1>

    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>{{ $group->name }}</strong>
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
            'events.manage.store',
            $group
        ) }}"
        class="organisation-form"
    >

        @csrf


        <div class="form-group">

            <label for="name">
                Event Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
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

                <option value="">
                    Select an event type
                </option>

                @foreach ($eventTypes as $eventType)

                    <option
                        value="{{ $eventType->id }}"
                        @selected(
                            old('event_type_id') ==
                            $eventType->id
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

                <option value="">
                    Select a London location
                </option>

                @foreach ($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        @selected(
                            old('location_id') ==
                            $location->id
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
                value="{{ old('start_datetime') }}"
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
                value="{{ old('end_datetime') }}"
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
                value="{{ old('venue_name') }}"
                placeholder="e.g. Community Hall"
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
                value="{{ old('address') }}"
            >

            <small class="form-help">
                Enter the event venue address.
                It may be different from the organisation's
                main address.
            </small>

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
                    $group->contact_name
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
                    $group->telephone
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
                    $group->email
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
                value="{{ old('website') }}"
                placeholder="https://example.org"
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
            >{{ old('description') }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button"
            >
                Create Event
            </button>

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
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection