@extends('layouts.app')

@section('title', 'Manage Groups')

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
                Manage Groups
            </h1>

            <p class="page-introduction">
                Select an organisation to view and manage
                its groups.
            </p>
        </div>

    </div>


    <form
        method="GET"
        action="{{ route('groups.manage.index') }}"
        class="group-organisation-search"
    >

        <div class="form-group">

            <label for="organization_id">
                Organisation
            </label>

            <select
                name="organization_id"
                id="organization_id"
                required
            >

                <option value="">
                    Select an organisation
                </option>

                @foreach ($organizations as $organization)

                    <option
                        value="{{ $organization->id }}"
                        @selected(
                            request('organization_id') ==
                            $organization->id
                        )
                    >
                        #{{ $organization->id }}
                        -
                        {{ $organization->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <button
            type="submit"
            class="button"
        >
            View Groups
        </button>

    </form>


    @if ($selectedOrganization)

        <div class="page-actions">

            <a
                href="{{ route(
                    'groups.manage.create',
                    $selectedOrganization
                ) }}"
                class="button"
            >
                + Create New Group
            </a>

</div>

        <div class="selected-organisation">

            <h2>
                {{ $selectedOrganization->name }}
            </h2>

            <p>
                Organisation record:
                #{{ $selectedOrganization->id }}
            </p>

        </div>


        @if ($groups->count())

            <div class="results-table-wrapper">

                <table class="results-table">

                    <thead>
                        <tr>
                            <th>Group No.</th>
                            <th>Group Name</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Actions</th>
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

                                    @if ($group->is_head_office)

                                        <strong>
                                            Head Office
                                        </strong>

                                    @else

                                        Organisation Group

                                    @endif

                                </td>

                                <td>
                                    {{ $group->contact_name
                                        ?: 'Not provided' }}
                                </td>

                                <td>

                                    <div class="crud-actions">

                                        <button
                                            type="button"
                                            class="button button-small"
                                            disabled
                                        >
                                            Show
                                        </button>

                                        <button
                                            type="button"
                                            class="button button-small button-edit"
                                            disabled
                                        >
                                            Edit
                                        </button>

                                        @unless ($group->is_head_office)

                                            <button
                                                type="button"
                                                class="button button-small button-delete"
                                                disabled
                                            >
                                                Archive
                                            </button>

                                        @endunless

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="no-results">

                <h2>No groups found</h2>

                <p>
                    This organisation does not currently
                    have any active groups.
                </p>

            </div>

        @endif

    @endif

</div>

@endsection