@extends('layouts.app')

@section('title', 'Organisation Finder')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Organisation Finder
    </h1>

    <p class="page-introduction">
        Find religious organisations in London by
        religion, denomination and location.
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
        class="search-form"
        method="GET"
        action="{{ route('organizations.results') }}"
    >

        {{-- Religion --}}

        <div class="form-group">

            <label for="religion_id">
                Religion
            </label>

            <select
                name="religion_id"
                id="religion_id"
                required
            >

                <option value="">
                    Select a religion
                </option>

                @foreach ($religions as $religion)

                    <option
                        value="{{ $religion->id }}"
                        @selected(
                            old('religion_id') == $religion->id
                        )
                    >
                        {{ $religion->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Denomination --}}

        <div class="form-group">

            <label for="denomination_id">
                Denomination
            </label>

            <select
                name="denomination_id"
                id="denomination_id"
                required
                disabled
            >

                <option value="">
                    First select a religion
                </option>

                @foreach ($denominations as $denomination)

                    <option
                        value="{{ $denomination->id }}"
                        data-religion-id="{{ $denomination->religion_id }}"
                    >
                        {{ $denomination->name }}
                    </option>

                @endforeach

            </select>

            <small class="form-help">
                The available denominations depend on
                the religion selected above.
            </small>

        </div>


        {{-- Location --}}

        <div class="form-group">

            <label for="location">
                London borough
            </label>

            <input
                type="text"
                name="location"
                id="location"
                list="location-list"
                value="{{ old('location') }}"
                placeholder="Select or type a London borough"
                autocomplete="off"
                required
            >

            <datalist id="location-list">

                @foreach ($locations as $location)

                    <option value="{{ $location->name }}">

                @endforeach

            </datalist>

            <small class="form-help">
                Start typing or select a location from
                the available London boroughs.
            </small>

        </div>


        <button
            type="submit"
            class="button"
        >
            Find Organisations
        </button>

    </form>

</div>

@endsection

@push('scripts')

<script>

    const religionSelect =
        document.getElementById('religion_id');

    const denominationSelect =
        document.getElementById('denomination_id');

    const allDenominationOptions =
        Array.from(
            denominationSelect.querySelectorAll(
                'option[data-religion-id]'
            )
        );


    function updateDenominations() {

        const religionId = religionSelect.value;

        denominationSelect.innerHTML = '';


        const placeholder =
            document.createElement('option');

        placeholder.value = '';


        if (!religionId) {

            placeholder.textContent =
                'First select a religion';

            denominationSelect.appendChild(
                placeholder
            );

            denominationSelect.disabled = true;

            return;
        }


        placeholder.textContent =
            'Select a denomination';

        denominationSelect.appendChild(
            placeholder
        );


        const matchingOptions =
            allDenominationOptions.filter(
                option =>
                    option.dataset.religionId ===
                    religionId
            );


        matchingOptions.forEach(option => {

            denominationSelect.appendChild(
                option.cloneNode(true)
            );

        });


        denominationSelect.disabled = false;
    }


    religionSelect.addEventListener(
        'change',
        updateDenominations
    );

    updateDenominations();

</script>

@endpush