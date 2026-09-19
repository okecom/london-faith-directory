@extends('layouts.app')

@section('title', 'Add Media')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        Add Media
    </h1>

    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Group:
            <strong>{{ $group->name }}</strong>
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
            'media.manage.store',
            $group
        ) }}"
        class="organisation-form"
        enctype="multipart/form-data"
    >

        @csrf


        <div class="form-group">

            <label for="title">
                Title
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
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

                <option value="">
                    Select a media type
                </option>

                <option
                    value="document"
                    @selected(old('type') === 'document')
                >
                    Document
                </option>

                <option
                    value="image"
                    @selected(old('type') === 'image')
                >
                    Image
                </option>

                <option
                    value="audio"
                    @selected(old('type') === 'audio')
                >
                    Audio
                </option>

                <option
                    value="video"
                    @selected(old('type') === 'video')
                >
                    Video
                </option>

                <option
                    value="link"
                    @selected(old('type') === 'link')
                >
                    External Link
                </option>

            </select>

        </div>


        <div class="form-group">

            <label for="file">
                Upload File
            </label>

            <input
                type="file"
                id="file"
                name="file"
            >

            <small class="form-help">
                Maximum file size: 10 MB.
                Upload a file or provide an external URL below,
                but not both.
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
                value="{{ old('external_url') }}"
                placeholder="https://example.org/resource"
            >

            <small class="form-help">
                Use this for media hosted on another website,
                such as an external video or document.
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
                        old('access_level', 'public') ===
                        'public'
                    )
                >
                    Public
                </option>

                <option
                    value="organisation"
                    @selected(
                        old('access_level') ===
                        'organisation'
                    )
                >
                    Organisation
                </option>

                <option
                    value="group"
                    @selected(
                        old('access_level') ===
                        'group'
                    )
                >
                    Group
                </option>

            </select>

            <small class="form-help">
                Public media can be viewed by anyone.
                Organisation and Group media will require
                authorised access.
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
            >{{ old('description') }}</textarea>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="button"
            >
                Add Media
            </button>

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
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection