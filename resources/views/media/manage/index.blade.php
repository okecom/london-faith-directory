@extends('layouts.app')

@section('title', 'Manage Media')

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

    <h1 class="page-heading">
        Manage Media
    </h1>

    <p class="page-introduction">
        Select an organisation and one of its groups
        to view media resources.
    </p>


    <form
        method="GET"
        action="{{ route('media.manage.index') }}"
        class="event-management-search"
    >

        <div class="form-group">

            <label for="organization_id">
                Organisation
            </label>

            <select
                name="organization_id"
                id="organization_id"
                required
                onchange="this.form.submit()"
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


        @if ($selectedOrganization)

            <div class="form-group">

                <label for="group_id">
                    Group
                </label>

                <select
                    name="group_id"
                    id="group_id"
                    required
                >

                    <option value="">
                        Select a group
                    </option>

                    @foreach ($groups as $group)

                        <option
                            value="{{ $group->id }}"
                            @selected(
                                request('group_id') ==
                                $group->id
                            )
                        >
                            {{ $group->name }}

                            @if ($group->is_head_office)
                                (Head Office)
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>


            <button
                type="submit"
                class="button"
            >
                View Media
            </button>

        @endif

    </form>


    @if ($selectedGroup)

        <div class="page-actions">

            <a
                href="{{ route(
                    'media.manage.create',
                    $selectedGroup
                ) }}"
                class="button"
            >
                + Add New Media
            </a>

            <a
                href="{{ route(
                    'media.manage.archived',
                    $selectedGroup
                ) }}"
                class="button button-secondary"
            >
                Archived Media
            </a>

        </div>


        <div class="selected-organisation">

            <h2>
                {{ $selectedOrganization->name }}
            </h2>

            <p>
                Group:
                <strong>
                    {{ $selectedGroup->name }}
                </strong>
            </p>

        </div>


        @if ($media->count())

            <div class="results-table-wrapper">

                <table class="results-table">

                    <thead>
                        <tr>
                            <th>Media No.</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Access</th>
                            <th>Source</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($media as $item)

                            <tr>

                                <td>
                                    {{ $item->id }}
                                </td>

                                <td>
                                    {{ $item->title }}
                                </td>

                                <td>
                                    {{ ucfirst($item->type) }}
                                </td>

                                <td>
                                    {{ ucfirst($item->access_level) }}
                                </td>

                                <td>
                                    @if ($item->file_path)
                                        Uploaded file
                                    @elseif ($item->external_url)
                                        External link
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>

                                    <div class="crud-actions">

                                        <a
                                            href="{{ route(
                                                'media.manage.show',
                                                $item
                                            ) }}"
                                            class="button button-small"
                                        >
                                            Show
                                        </a>

                                        <a
                                            href="{{ route(
                                                'media.manage.edit',
                                                $item
                                            ) }}"
                                            class="button button-small button-edit"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'media.manage.destroy',
                                                $item
                                            ) }}"
                                            class="delete-form"
                                            onsubmit="return confirm(
                                                @js(
                                                    'Are you sure you want to archive "' .
                                                    $item->title .
                                                    '"? It will no longer appear in the active media list.'
                                                )
                                            );"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="button button-small button-delete"
                                            >
                                                Archive
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{ $media->links() }}

        @else

            <div class="no-results">

                <h2>No media found</h2>

                <p>
                    This group does not currently have
                    any active media resources.
                </p>

            </div>

        @endif

    @endif

</div>

@endsection