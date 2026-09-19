@extends('layouts.app')

@section('title', 'Archived Media')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Archived Media
    </h1>


    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>
                {{ $group->name }}
            </strong>
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
                        <th>Archived</th>
                        <th>Action</th>
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
                                {{ ucfirst(
                                    $item->access_level
                                ) }}
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
                                {{ $item->deleted_at
                                    ->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'media.manage.restore',
                                        [
                                            'group' =>
                                                $group,

                                            'media' =>
                                                $item->id,
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


        {{ $media->links() }}

    @else

        <div class="no-results">

            <h2>No archived media</h2>

            <p>
                This group currently has
                no archived media.
            </p>

        </div>

    @endif


    <div class="page-actions">

        <a
            href="{{ route(
                'media.manage.index',
                [
                    'organization_id' =>
                        $group->organization_id,

                    'group_id' =>
                        $group->id,
                ]
            ) }}"
            class="text-link"
        >
            Back to Media
        </a>

    </div>

</div>

@endsection