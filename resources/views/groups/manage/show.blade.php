@extends('layouts.app')

@section('title', 'Group Details')

@section('content')

<div class="page-card">

    <h1 class="page-heading">
        {{ $group->name }}
    </h1>

    <p class="page-introduction">
        Group record #{{ $group->id }}
    </p>


    <div class="selected-organisation">

        <h2>
            {{ $group->organization->name }}
        </h2>

        <p>
            Organisation record:
            #{{ $group->organization->id }}
        </p>

    </div>


    <div class="group-details">

        <dl>

            <dt>Group Type</dt>
            <dd>
                {{ $group->is_head_office
                    ? 'Head Office'
                    : 'Organisation Group' }}
            </dd>


            <dt>Contact Name</dt>
            <dd>
                {{ $group->contact_name
                    ?: 'Not provided' }}
            </dd>


            <dt>Telephone</dt>
            <dd>
                {{ $group->telephone
                    ?: 'Not provided' }}
            </dd>


            <dt>Email</dt>
            <dd>
                {{ $group->email
                    ?: 'Not provided' }}
            </dd>


            <dt>Description</dt>
            <dd>
                {{ $group->description
                    ?: 'No description provided.' }}
            </dd>

        </dl>

    </div>


    <div class="page-actions">

        <a
            href="{{ route(
                'groups.manage.edit',
                $group
            ) }}"
            class="button button-edit"
        >
            Edit Group
        </a>

        <a
            href="{{ route(
                'groups.manage.index',
                [
                    'organization_id' =>
                        $group->organization_id
                ]
            ) }}"
            class="text-link"
        >
            Back to Groups
        </a>

    </div>

</div>

@endsection