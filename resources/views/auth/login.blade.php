@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Login</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div>
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div>
                <label>
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                    >
                    Remember me
                </label>
            </div>

            <button type="submit">
                Login
            </button>
        </form>
    </div>
@endsection