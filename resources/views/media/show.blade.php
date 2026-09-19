@extends('layouts.app')

@section('title', $media->title)

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        {{ $media->title }}
    </h1>

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


            <dt>Description</dt>
            <dd>
                {{ $media->description
                    ?: 'No description provided.' }}
            </dd>


            <dt>Access</dt>
            <dd>
                {{ ucfirst($media->access_level) }}
            </dd>

        </dl>

    </div>


    <div class="page-actions">

        <a
            href="{{ route(
                'media.access',
                $media
            ) }}"
            class="button"
            @if ($media->external_url)
                target="_blank"
                rel="noopener noreferrer"
            @endif
        >
            @if ($media->external_url)
                Open Resource
            @else
                Download File
            @endif
        </a>

    </div>

</div>

@endsection