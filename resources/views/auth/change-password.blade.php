@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Change Temporary Password</h1>

        <p>
            You must change your temporary password before
            continuing to your Organisation Dashboard.
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
            action="{{ route('password.change.update') }}"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="current_password">
                    Temporary Password
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                >
            </div>

            <div>
                <label for="password">
                    New Password
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
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                >
            </div>

            <button type="submit">
                Change Password
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit">
                Logout
            </button>
        </form>
    </div>
@endsection