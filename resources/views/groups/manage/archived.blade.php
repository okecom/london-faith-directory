@extends('layouts.app')

@section('title', 'Archived Groups')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Archived Groups
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


    @if (session('success'))

        <div
            class="success-message"
            role="status"
        >
            {{ session('success') }}
        </div>

    @endif


    @if ($groups->count())

        <div class="results-table-wrapper">

            <table class="results-table">

                <thead>
                    <tr>
                        <th>Group No.</th>
                        <th>Group Name</th>
                        <th>Archived</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($groups as $group)

                        <tr>

                            <td>
                                {{ $group->id }}
                            </td>

                            <td>
                                {{ $group->name }}
                            </td>

                            <td>
                                {{ $group->deleted_at
                                    ->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'groups.manage.restore',
                                        [
                                            'organization' =>
                                                $organization,
                                            'group' =>
                                                $group->id,
                                        ]
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="button button-small"
                                    >
                                        Restore
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="no-results">

            <h2>No archived groups</h2>

            <p>
                This organisation currently has
                no archived groups.
            </p>

        </div>

    @endif


    <div class="page-actions">

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
            Back to Groups
        </a>

    </div>

</div>

@endsection