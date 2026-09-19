@extends('layouts.app')

@section('title', 'Media Details')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        {{ $media->title }}
    </h1>

    <p class="page-introduction">
        Media record #{{ $media->id }}
    </p>


    <div class="selected-organisation">

        <h2>
            {{ $media->group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>
                {{ $media->group->name }}
            </strong>
        </p>

    </div>


    <div class="group-details">

        <dl>

            <dt>Media Type</dt>
            <dd>
                {{ ucfirst($media->type) }}
            </dd>


            <dt>Access Level</dt>
            <dd>
                {{ ucfirst($media->access_level) }}
            </dd>


            <dt>Source</dt>
            <dd>

                @if ($media->file_path)

                    Uploaded file

                @elseif ($media->external_url)

                    External link

                @else

                    Not provided

                @endif

            </dd>


            <dt>File</dt>
            <dd>

                @if ($media->file_path)

                    {{ basename($media->file_path) }}

                @else

                    No uploaded file

                @endif

            </dd>


            <dt>External URL</dt>
            <dd>

                @if ($media->external_url)

                    <a
                        href="{{ $media->external_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $media->external_url }}
                    </a>

                @else

                    Not provided

                @endif

            </dd>


            <dt>Description</dt>
            <dd>
                {{ $media->description
                    ?: 'No description provided.' }}
            </dd>


            <dt>Added</dt>
            <dd>
                {{ $media->created_at
                    ->format('d/m/Y H:i') }}
            </dd>

        </dl>

    </div>


    <div class="page-actions">

        <a
            href="{{ route(
                'media.manage.edit',
                $media
            ) }}"
            class="button button-edit"
        >
            Edit Media
        </a>

        <a
            href="{{ route(
                'media.manage.index',
                [
                    'organization_id' =>
                        $media->group
                            ->organization_id,

                    'group_id' =>
                        $media->group_id,
                ]
            ) }}"
            class="text-link"
        >
            Back to Media
        </a>

    </div>

</div>

@endsection