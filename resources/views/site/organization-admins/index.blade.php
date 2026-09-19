@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Organisation Administrators</h1>

        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <p>
            <a href="{{ route('site.organization-admins.create') }}">
                Create Organisation Administrator
            </a>
        </p>

        <p>
            <a href="{{ route('site.dashboard') }}">
                Back to Site Management Dashboard
            </a>
        </p>

        @if ($administrators->count())
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Organisation</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($administrators as $administrator)
                        <tr>
                            <td>{{ $administrator->name }}</td>

                            <td>{{ $administrator->email }}</td>

                            <td>
                                {{ $administrator->organization?->name ?? 'Not assigned' }}
                            </td>

                            <td>
                                {{ $administrator->is_active ? 'Active' : 'Deactivated' }}
                            </td>

                            <td>
                                @if ($administrator->is_active)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'site.organization-admins.deactivate',
                                            $administrator
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit">
                                            Deactivate
                                        </button>
                                    </form>

                                    <p>
                                       <a href="{{ route(
                                            'site.organization-admins.replace',
                                            $administrator
                                        ) }}">
                                            Replace
                                        </a>
                                    </p>

                                @else

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'site.organization-admins.reactivate',
                                            $administrator
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit">
                                            Reactivate
                                        </button>
                                    </form>

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $administrators->links() }}
        @else
            <p>No Organisation Administrators have been created.</p>
        @endif
    </div>
@endsection