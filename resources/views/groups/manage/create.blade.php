@extends('layouts.app')

@section('title', 'Create Group')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Create Group
    </h1>

    <div class="selected-organisation">

        <h2>
            {{ $organization->name }}
        </h2>

        <p>
            Organisation record:
            #{{ $organization->id }}
        </p>

    </div>


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

                    <li>
                        {{ $error }}
                    </li>

                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'groups.manage.store',
            $organization
        ) }}"
        class="organisation-form"
    >

        @csrf


        <div class="form-group">

            <label for="name">
                Group Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                placeholder="e.g. Youth Group"
                required
            >

        </div>


        <div class="form-group">

            <label for="contact_name">
                Contact Name
            </label>

            <input
                type="text"
                name="contact_name"
                id="contact_name"
                value="{{ old('contact_name') }}"
            >

        </div>


        <div class="form-group">

            <label for="telephone">
                Telephone
            </label>

            <input
                type="tel"
                name="telephone"
                id="telephone"
                value="{{ old('telephone') }}"
            >

        </div>


        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
            >

        </div>


        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                name="description"
                id="description"
                rows="6"
                placeholder="Describe the purpose of this group."
            >{{ old('description') }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button"
            >
                Create Group
            </button>

            <a
                href="{{ route(
                    'groups.manage.index',
                    [
                        'organization_id' =>
                            $organization->id
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