@extends('layouts.app')

@section('title', 'Edit Group')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Edit Group
    </h1>

    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Organisation record:
            #{{ $group->organization_id }}
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
            'groups.manage.update',
            $group
        ) }}"
        class="organisation-form"
    >

        @csrf
        @method('PUT')


        <div class="form-group">

            <label for="name">
                Group Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old(
                    'name',
                    $group->name
                ) }}"
                @if ($group->is_head_office)
                    readonly
                @endif
                required
            >

            @if ($group->is_head_office)

                <small class="form-help">
                    The Head Office group name
                    cannot be changed.
                </small>

            @endif

        </div>


        <div class="form-group">

            <label for="contact_name">
                Contact Name
            </label>

            <input
                type="text"
                name="contact_name"
                id="contact_name"
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
                name="telephone"
                id="telephone"
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
                name="email"
                id="email"
                value="{{ old(
                    'email',
                    $group->email
                ) }}"
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
            >{{ old(
                'description',
                $group->description
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
                    'groups.manage.index',
                    [
                        'organization_id' =>
                            $group->organization_id
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