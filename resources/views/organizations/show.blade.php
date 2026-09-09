@extends('layouts.app')

@section('title', $organization->name)

@section('content')

<div class="page-card">

    <div class="profile-heading">

        <h1 class="page-heading">
            {{ $organization->name }}
        </h1>

        <p class="profile-subtitle">
            Organisation Profile
        </p>

    </div>


    <div class="profile-grid">

        {{-- Organisation photo --}}

        <div class="profile-photo">

            @if ($organization->photo)

                <img
                    src="{{ asset($organization->photo) }}"
                    alt="Photo of {{ $organization->name }}"
                >

            @else

                <div class="profile-photo-placeholder">
                    No photo available
                </div>

            @endif

        </div>


        {{-- Organisation details --}}

        <div class="profile-details">

            <dl>

                <div class="profile-detail">
                    <dt>Religion</dt>

                    <dd>
                        {{ $organization
                            ->denomination
                            ->religion
                            ->name }}
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Denomination</dt>

                    <dd>
                        {{ $organization
                            ->denomination
                            ->name }}
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Location</dt>

                    <dd>
                        {{ $organization
                            ->location
                            ->name }}
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Address</dt>

                    <dd>
                        {{ $organization->address }}
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Telephone</dt>

                    <dd>
                        @if ($organization->telephone)

                            <a
                                href="tel:{{ $organization->telephone }}"
                            >
                                {{ $organization->telephone }}
                            </a>

                        @else

                            Not available

                        @endif
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Email</dt>

                    <dd>
                        @if ($organization->email)

                            <a
                                href="mailto:{{ $organization->email }}"
                            >
                                {{ $organization->email }}
                            </a>

                        @else

                            Not available

                        @endif
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Head / Leader</dt>

                    <dd>
                        {{ $organization->head
                            ?? 'Not available' }}
                    </dd>
                </div>


                <div class="profile-detail">
                    <dt>Website</dt>

                    <dd>
                        @if ($organization->website)

                            <a
                                href="{{ $organization->website }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Visit website
                            </a>

                        @else

                            Not available

                        @endif
                    </dd>
                </div>

            </dl>

        </div>

    </div>


    {{-- Description --}}

    <section class="profile-description">

        <h2>
            About this organisation
        </h2>

        @if ($organization->description)

            <p>
                {{ $organization->description }}
            </p>

        @else

            <p>
                No description is currently available.
            </p>

        @endif

    </section>


    {{-- Navigation --}}

    <div class="page-actions">
    @if (
        $backUrl &&
        str_starts_with(
            $backUrl,
            url('/organizations/results')
        )
    )

        <a
            href="{{ $backUrl }}"
            class="button"
        >
            Back to Results
        </a>

    @else

        <a
            href="{{ route('organizations.search') }}"
            class="button"
        >
            Back to Search
        </a>

    @endif


        <a
            href="{{ route('organizations.search') }}"
            class="text-link"
        >
            Back to Main Search
        </a>

    </div>

</div>

@endsection