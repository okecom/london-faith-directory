@extends('layouts.app')

@section('title', 'Edit Organisation')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Edit Organisation
    </h1>

    <p class="page-introduction">
        Update organisation record
        #{{ $organization->id }}.
    </p>


    @if ($errors->any())

        <div
            class="error-message"
            role="alert"
        >

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
            'organizations.manage.update',
            $organization
        ) }}"
        enctype="multipart/form-data"
        class="organisation-form"
    >

        @csrf
        @method('PUT')


        {{-- Organisation name --}}

        <div class="form-group">

            <label for="name">
                Organisation Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old(
                    'name',
                    $organization->name
                ) }}"
                required
            >

        </div>


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
                            old(
                                'religion_id',
                                $organization
                                    ->denomination
                                    ->religion_id
                            ) == $religion->id
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
            >

                <option value="">
                    Select a denomination
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

        </div>


        {{-- Location --}}

        <div class="form-group">

            <label for="location_id">
                London Location
            </label>

            <select
                name="location_id"
                id="location_id"
                required
            >

                <option value="">
                    Select a London location
                </option>

                @foreach ($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        @selected(
                            old(
                                'location_id',
                                $organization->location_id
                            ) == $location->id
                        )
                    >
                        {{ $location->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Address --}}

        <div class="form-group">

            <label for="address">
                Address
            </label>

            <input
                type="text"
                name="address"
                id="address"
                value="{{ old(
                    'address',
                    $organization->address
                ) }}"
                required
            >

        </div>


        {{-- Telephone --}}

        <div class="form-group">

            <label for="telephone">
                Telephone
            </label>

            <input
                type="tel"
                name="telephone"
                id="telephone"
                value="{{ old(
                    'telephone',
                    $organization->telephone
                ) }}"
            >

        </div>


        {{-- Email --}}

        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old(
                    'email',
                    $organization->email
                ) }}"
            >

        </div>


        {{-- Head / Leader --}}

        <div class="form-group">

            <label for="head">
                Head / Leader
            </label>

            <input
                type="text"
                name="head"
                id="head"
                value="{{ old(
                    'head',
                    $organization->head
                ) }}"
            >

        </div>


        {{-- Website --}}

        <div class="form-group">

            <label for="website">
                Website
            </label>

            <input
                type="url"
                name="website"
                id="website"
                value="{{ old(
                    'website',
                    $organization->website
                ) }}"
                placeholder="https://example.org"
            >

        </div>


        {{-- Current photo --}}

        @if ($organization->photo)

            <div class="form-group">

                <label>
                    Current Photo
                </label>

                <div class="edit-current-photo">

                    <img
                        src="{{ asset(
                            $organization->photo
                        ) }}"
                        alt="Current photo of {{ $organization->name }}"
                    >

                </div>

            </div>

        @endif


        {{-- Replacement photo --}}

        <div class="form-group">

            <label for="photo">
                Replace Photo
            </label>

            <input
                type="file"
                name="photo"
                id="photo"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <small class="form-help">
                Leave this empty to keep the current
                photo. Maximum size 2 MB.
            </small>

        </div>


        {{-- Description --}}

        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                name="description"
                id="description"
                rows="6"
            >{{ old(
                'description',
                $organization->description
            ) }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button"
            >
                Save Changes
            </button>

            <a
                href="{{ route(
                    'organizations.manage.index'
                ) }}"
                class="text-link"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection

@push('scripts')

<script>

    const religionSelect =
        document.getElementById('religion_id');

    const denominationSelect =
        document.getElementById('denomination_id');

    const selectedDenomination =
        "{{ old(
            'denomination_id',
            $organization->denomination_id
        ) }}";

    const denominationOptions =
        Array.from(
            denominationSelect.querySelectorAll(
                'option[data-religion-id]'
            )
        );


    function updateDenominations() {

        const religionId =
            religionSelect.value;

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


        denominationOptions
            .filter(
                option =>
                    option.dataset.religionId ===
                    religionId
            )
            .forEach(option => {

                const newOption =
                    option.cloneNode(true);

                if (
                    newOption.value ===
                    selectedDenomination
                ) {
                    newOption.selected = true;
                }

                denominationSelect.appendChild(
                    newOption
                );

            });


        denominationSelect.disabled = false;
    }


    religionSelect.addEventListener(
        'change',
        function () {

            /*
             * When the user changes religion,
             * reset the denomination.
             */
            denominationSelect.dataset.changed =
                'true';

            updateDenominations();

            denominationSelect.value = '';
        }
    );


    updateDenominations();

</script>

@endpush