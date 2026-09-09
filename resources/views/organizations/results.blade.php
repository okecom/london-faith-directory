@extends('layouts.app')

@section('title', 'Organisation Search Results')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Organisation Search Results
    </h1>


    {{-- Search parameters --}}

    <section
        class="search-summary"
        aria-labelledby="search-summary-heading"
    >

        <h2 id="search-summary-heading">
            Your Search
        </h2>

        <dl class="search-summary-list">

            <div>
                <dt>Religion</dt>
                <dd>{{ $religion->name }}</dd>
            </div>

            <div>
                <dt>Denomination</dt>
                <dd>{{ $denomination->name }}</dd>
            </div>

            <div>
                <dt>Location</dt>
                <dd>{{ $location->name }}</dd>
            </div>

        </dl>

    </section>


    {{-- Results --}}

    @if ($organizations->count() > 0)

        <p class="results-count">
            {{ $organizations->total() }}
            organisation(s) found.
        </p>


        <div class="results-table-wrapper">

            <table class="results-table">

                <thead>

                    <tr>
                        <th>Organisation</th>
                        <th>Denomination</th>
                        <th>Location</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach ($organizations as $organization)

                        <tr>

                            <td>
                                <a
                                    href="{{ route('organizations.show', [
                                        'organization' => $organization,
                                        'from' => request()->fullUrl(),
                                    ]) }}"
                                    class="organization-link"
                                >
                                    {{ $organization->name }}
                                </a>
                            </td>

                            <td>
                                {{ $organization->denomination->name }}
                            </td>

                            <td>
                                {{ $organization->location->name }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}

        <nav
            class="pagination"
            aria-label="Organisation results pages"
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
                No organisations found
            </h2>

            <p>
                We could not find any organisations
                matching your selected denomination
                and location.
            </p>

            <p>
                Try changing your search criteria.
            </p>

        </div>

    @endif


    <div class="page-actions">

        <a
            href="{{ route('organizations.search') }}"
            class="button"
        >
            New Search
        </a>

        <a
            href="{{ url()->previous() }}"
            class="text-link"
        >
            Back
        </a>

    </div>

</div>

@endsection