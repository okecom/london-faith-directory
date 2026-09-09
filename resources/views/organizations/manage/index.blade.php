@extends('layouts.app')

@section('title', 'Manage Organisations')

@section('content')

<div class="page-card">

        @if (session('success'))

        <div
            class="success-message"
            role="status"
        >
            {{ session('success') }}
        </div>

        @endif

    <div class="management-heading">

        <div>
            <h1 class="page-heading">
                Manage Organisations
            </h1>

            <p class="page-introduction">
                View, search, create, edit and delete
                organisation profiles.
            </p>
        </div>

        <a
            href="{{ route('organizations.manage.create') }}"
            class="button create-button"
        >
            + Create New Organisation
        </a>

    </div>


    {{-- Record number search --}}

    <form
        method="GET"
        action="{{ route('organizations.manage.index') }}"
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
                href="{{ route('organizations.manage.index') }}"
                class="text-link"
            >
                Clear Search
            </a>

        @endif

    </form>


    {{-- Organisation table --}}

    @if ($organizations->count())

        <div class="results-table-wrapper">

            <table class="results-table">

                <thead>
                    <tr>
                        <th>Record No.</th>
                        <th>Organisation Name</th>
                        <th>Actions</th>
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

                                <div class="crud-actions">

                                    <a
                                        href="{{ route(
                                            'organizations.show',
                                            $organization
                                        ) }}"
                                        class="button button-small"
                                    >
                                        Show
                                    </a>

                                    <a
                                        href="{{ route(
                                            'organizations.manage.edit',
                                            $organization
                                        ) }}"
                                        class="button button-small button-edit"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        class="button button-small button-delete"
                                    >
                                        Delete
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <nav
            class="pagination"
            aria-label="Organisation management pages"
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

            <h2>
                No organisation found
            </h2>

            <p>
                No organisation matches that record number.
            </p>

        </div>

    @endif

</div>

@endsection