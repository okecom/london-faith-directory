@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Replace Organisation Administrator</h1>

        <p>
            <strong>Organisation:</strong>
            {{ $organization->name }}
        </p>

        <p>
            <strong>Current Administrator:</strong>
            {{ $administrator->name }}
        </p>

        <p>
            Replacing this administrator will deactivate their access
            and create a new active administrator for this organisation.
        </p>

        @if ($errors->any())
            <div>
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
                'site.organization-admins.store-replacement',
                $administrator
            ) }}"
        >
            @csrf

            <div>
                <label for="name">New Administrator Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div>
                <label for="email">New Administrator Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div>
                <label for="password">
                    Temporary Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <div>
                <label for="password_confirmation">
                    Confirm Temporary Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                >
            </div>

            <button type="submit">
                Replace Administrator
            </button>
        </form>

        <p>
            <a href="{{ route('site.organization-admins.index') }}">
                Cancel
            </a>
        </p>
    </div>
@endsection