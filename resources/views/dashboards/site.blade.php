@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Site Management Dashboard</h1>

        <p>
            Welcome, {{ auth()->user()->name }}.
        </p>

        <p>
            You are logged in as a Site Administrator.
        </p>
    </div>
        <p>
            <a href="{{ route('site.organization-admins.index') }}">
                Manage Organisation Administrators
            </a>
        </p>

    <form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>
@endsection