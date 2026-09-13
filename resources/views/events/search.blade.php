@extends('layouts.app')

@section('title', 'Event Finder')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Event Finder
    </h1>

    <p class="page-introduction">
        Find upcoming events by event type
        and denomination.
    </p>


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
        method="GET"
        action="{{ route('events.results') }}"
        class="organisation-form"
    >

        <div class="form-group">

            <label for="event_type_id">
                Event Type
            </label>

            <select
                name="event_type_id"
                id="event_type_id"
                required
            >

                <option value="">
                    Select an event type
                </option>

                @foreach ($eventTypes as $eventType)

                    <option
                        value="{{ $eventType->id }}"
                        @selected(
                            old(
                                'event_type_id',
                                request('event_type_id')
                            ) == $eventType->id
                        )
                    >
                        {{ $eventType->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="form-group">

            <label for="denomination_id">
                Denomination
            </label>

            <select
                name="denomination_id"
                id="denomination_id"
                required
            >

                <option value="">
                    Select a denomination
                </option>

                @foreach ($denominations as $denomination)

                    <option
                        value="{{ $denomination->id }}"
                        @selected(
                            old(
                                'denomination_id',
                                request('denomination_id')
                            ) == $denomination->id
                        )
                    >
                        {{ $denomination->name }}
                        —
                        {{ $denomination->religion->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <button
            type="submit"
            class="button"
        >
            Search Events
        </button>

    </form>

</div>

@endsection