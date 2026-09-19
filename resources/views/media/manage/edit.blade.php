@extends('layouts.app')

@section('title', 'Edit Media')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Edit Media
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


    @if ($errors->any())

        <div class="error-message" role="alert">

            <strong>
                Please correct the following:
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'media.manage.update',
            $media
        ) }}"
        class="organisation-form"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


        <div class="form-group">

            <label for="title">
                Title
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old(
                    'title',
                    $media->title
                ) }}"
                required
            >

        </div>


        <div class="form-group">

            <label for="type">
                Media Type
            </label>

            <select
                id="type"
                name="type"
                required
            >

                <option
                    value="document"
                    @selected(
                        old('type', $media->type) ===
                        'document'
                    )
                >
                    Document
                </option>

                <option
                    value="image"
                    @selected(
                        old('type', $media->type) ===
                        'image'
                    )
                >
                    Image
                </option>

                <option
                    value="audio"
                    @selected(
                        old('type', $media->type) ===
                        'audio'
                    )
                >
                    Audio
                </option>

                <option
                    value="video"
                    @selected(
                        old('type', $media->type) ===
                        'video'
                    )
                >
                    Video
                </option>

                <option
                    value="link"
                    @selected(
                        old('type', $media->type) ===
                        'link'
                    )
                >
                    External Link
                </option>

            </select>

        </div>


        @if ($media->file_path)

            <div class="form-group">

                <label>
                    Current File
                </label>

                <p>
                    {{ basename($media->file_path) }}
                </p>

                <small class="form-help">
                    Leave the replacement file field empty
                    to keep this file.
                </small>

            </div>

        @endif


        <div class="form-group">

            <label for="file">
                @if ($media->file_path)
                    Replace File
                @else
                    Upload File
                @endif
            </label>

            <input
                type="file"
                id="file"
                name="file"
            >

            <small class="form-help">
                Maximum file size: 10 MB.
                Selecting a new file will replace
                the existing uploaded file.
            </small>

        </div>


        <div class="form-group">

            <label for="external_url">
                External URL
            </label>

            <input
                type="url"
                id="external_url"
                name="external_url"
                value="{{ old(
                    'external_url',
                    $media->external_url
                ) }}"
                placeholder="https://example.org/resource"
            >

            <small class="form-help">
                Do not provide an external URL when
                uploading a replacement file.
            </small>

        </div>


        <div class="form-group">

            <label for="access_level">
                Access Level
            </label>

            <select
                id="access_level"
                name="access_level"
                required
            >

                <option
                    value="public"
                    @selected(
                        old(
                            'access_level',
                            $media->access_level
                        ) === 'public'
                    )
                >
                    Public
                </option>

                <option
                    value="organisation"
                    @selected(
                        old(
                            'access_level',
                            $media->access_level
                        ) === 'organisation'
                    )
                >
                    Organisation
                </option>

                <option
                    value="group"
                    @selected(
                        old(
                            'access_level',
                            $media->access_level
                        ) === 'group'
                    )
                >
                    Group
                </option>

            </select>

            <small class="form-help">
                Changing the access level of an uploaded
                file will move it between public and
                protected storage when necessary.
            </small>

        </div>


        <div class="form-group">

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="6"
            >{{ old(
                'description',
                $media->description
            ) }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button button-edit"
            >
                Save Changes
            </button>

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
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection