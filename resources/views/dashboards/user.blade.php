@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>User Dashboard</h1>

        <p>
            Welcome, {{ auth()->user()->name }}.
        </p>

        <p>
            You are logged in as a registered user.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit">
                Logout
            </button>
        </form>
    </div>
@endsection