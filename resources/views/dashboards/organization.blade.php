@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Organisation Dashboard</h1>

        <p>
            Welcome, {{ $user->name }}.
        </p>

        @if ($organization)
            <h2>{{ $organization->name }}</h2>

            <p>
                You are the administrator for this organisation.
            </p>
        @else
            <p>
                No organisation has been assigned to this account.
            </p>
        @endif

        <form method="POST" action="{{ route('logout') }}">
    @csrf

            <button type="submit">
                Logout
            </button>
        </form>
    </div>
@endsection