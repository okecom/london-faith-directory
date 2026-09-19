@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Organisation Administrator</h1>

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
            action="{{ route('site.organization-admins.store') }}"
        >
            @csrf

            <div>
                <label for="name">Administrator Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div>
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div>
                <label for="organization_id">Organisation</label>

                <select
                    id="organization_id"
                    name="organization_id"
                    required
                >
                    <option value="">Select Organisation</option>

                    @foreach ($organizations as $organization)
                        <option
                            value="{{ $organization->id }}"
                            @selected(
                                old('organization_id') == $organization->id
                            )
                        >
                            {{ $organization->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="password">Temporary Password</label>

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
                Create Administrator
            </button>
        </form>

        <p>
            <a href="{{ route('site.organization-admins.index') }}">
                Back to Organisation Administrators
            </a>
        </p>
    </div>
@endsection