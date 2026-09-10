@extends('layouts.app')

@section('title', 'Archived Organisations')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Archived Organisations
    </h1>

    <p class="page-introduction">
        View and restore archived organisation records.
    </p>


    @if (session('success'))

        <div
            class="success-message"
            role="status"
        >
            {{ session('success') }}
        </div>

    @endif


    <form
        method="GET"
        action="{{ route(
            'organizations.manage.archived'
        ) }}"
        class="record-search"
    >

        <div class="form-group">

            <label for="record_number">
                Search by Record Number
            </label>

            <input
                type="number"
                name="record_number"
                id="record_number"
                min="1"
                value="{{ request('record_number') }}"
                placeholder="Enter organisation ID"
            >

        </div>

        <button
            type="submit"
            class="button"
        >
            Search
        </button>


        @if (request()->filled('record_number'))

            <a
                href="{{ route(
                    'organizations.manage.archived'
                ) }}"
                class="text-link"
            >
                Clear Search
            </a>

        @endif

    </form>


    @if ($organizations->count())

        <div class="results-table-wrapper">

            <table class="results-table">

                <thead>
                    <tr>
                        <th>Record No.</th>
                        <th>Organisation Name</th>
                        <th>Archived</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($organizations as $organization)

                        <tr>

                            <td>
                                {{ $organization->id }}
                            </td>

                            <td>
                                {{ $organization->name }}
                            </td>

                            <td>
                                {{ $organization->deleted_at
                                    ->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'organizations.manage.restore',
                                        $organization->id
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


        <nav
            class="pagination"
            aria-label="Archived organisation pages"
        >

            <div>

                @if ($organizations->onFirstPage())

                    <span class="pagination-disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $organizations->previousPageUrl() }}"
                        class="button button-secondary"
                    >
                        Previous
                    </a>

                @endif

            </div>


            <div class="pagination-status">

                Page
                {{ $organizations->currentPage() }}
                of
                {{ $organizations->lastPage() }}

            </div>


            <div>

                @if ($organizations->hasMorePages())

                    <a
                        href="{{ $organizations->nextPageUrl() }}"
                        class="button button-secondary"
                    >
                        Next
                    </a>

                @else

                    <span class="pagination-disabled">
                        Next
                    </span>

                @endif

            </div>

        </nav>

    @else

        <div class="no-results">

            <h2>No archived organisations found</h2>

            <p>
                There are currently no archived organisation
                records matching your search.
            </p>

        </div>

    @endif


    <div class="page-actions">

        <a
            href="{{ route(
                'organizations.manage.index'
            ) }}"
            class="text-link"
        >
            Back to Manage Organisations
        </a>

    </div>

</div>

@endsection