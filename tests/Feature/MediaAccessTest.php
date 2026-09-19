<?php

use App\Models\Denomination;
use App\Models\Group;
use App\Models\Location;
use App\Models\Media;
use App\Models\Organization;
use App\Models\Religion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);


function createMediaTestOrganization(
    string $name = 'Media Test Organisation'
): Organization {
    $religion = Religion::create([
        'name' => 'Test Religion '.uniqid(),
    ]);

    $denomination = Denomination::create([
        'religion_id' => $religion->id,
        'name' => 'Test Denomination '.uniqid(),
    ]);

    $location = Location::create([
        'name' => 'Test Location '.uniqid(),
    ]);

    return Organization::create([
        'name' => $name,
        'denomination_id' => $denomination->id,
        'location_id' => $location->id,
        'description' => 'Organisation used for media access tests.',
        'address' => '1 Test Street, London',
    ]);
}


function createMediaTestGroup(
    Organization $organization
): Group {
    return Group::create([
        'organization_id' => $organization->id,
        'name' => 'Media Test Group',
        'description' => 'Group used for media access tests.',
        'is_head_office' => false,
    ]);
}


function createRestrictedMedia(
    Group $group,
    string $accessLevel = Media::ACCESS_ORGANISATION
): Media {
    return Media::create([
        'group_id' => $group->id,
        'title' => 'Restricted Test Media',
        'description' => 'Restricted media used for testing.',
        'type' => 'document',
        'file_path' => 'media/test-document.pdf',
        'external_url' => null,
        'access_level' => $accessLevel,
    ]);
}


it('allows an anonymous visitor to view public media', function () {
    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);

    $media = Media::create([
        'group_id' => $group->id,
        'title' => 'Public Test Media',
        'description' => 'Public media used for testing.',
        'type' => 'link',
        'file_path' => null,
        'external_url' => 'https://example.com',
        'access_level' => Media::ACCESS_PUBLIC,
    ]);

    $this->get(route('media.show', $media))
        ->assertOk()
        ->assertSee('Public Test Media');
});


it('denies an anonymous visitor access to organisation media', function () {
    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);
    $media = createRestrictedMedia($group);

    $this->get(route('media.show', $media))
        ->assertForbidden();

    $this->get(route('media.access', $media))
        ->assertForbidden();
});


it('allows a site administrator to access organisation media', function () {
    Storage::fake('local');

    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);
    $media = createRestrictedMedia($group);

    Storage::disk('local')->put(
        $media->file_path,
        'test pdf content'
    );

    $siteAdmin = User::factory()->create([
        'role' => User::ROLE_SITE_ADMIN,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($siteAdmin)
        ->get(route('media.show', $media))
        ->assertOk();

    $this->actingAs($siteAdmin)
        ->get(route('media.access', $media))
        ->assertOk();
});


it('denies an organisation administrator from another organisation', function () {
    $organization = createMediaTestOrganization(
        'First Test Organisation'
    );

    $otherOrganization = createMediaTestOrganization(
        'Second Test Organisation'
    );

    $group = createMediaTestGroup($organization);
    $media = createRestrictedMedia($group);

    $organisationAdmin = User::factory()->create([
        'role' => User::ROLE_ORGANISATION_ADMIN,
        'organization_id' => $otherOrganization->id,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($organisationAdmin)
        ->get(route('media.show', $media))
        ->assertForbidden();

    $this->actingAs($organisationAdmin)
        ->get(route('media.access', $media))
        ->assertForbidden();
});


it('allows an organisation administrator from the same organisation', function () {
    Storage::fake('local');

    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);
    $media = createRestrictedMedia($group);

    Storage::disk('local')->put(
        $media->file_path,
        'test pdf content'
    );

    $organisationAdmin = User::factory()->create([
        'role' => User::ROLE_ORGANISATION_ADMIN,
        'organization_id' => $organization->id,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($organisationAdmin)
        ->get(route('media.show', $media))
        ->assertOk()
        ->assertSee('Restricted Test Media');

    $this->actingAs($organisationAdmin)
        ->get(route('media.access', $media))
        ->assertOk();
});


it('denies an inactive organisation administrator', function () {
    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);
    $media = createRestrictedMedia($group);

    $organisationAdmin = User::factory()->create([
        'role' => User::ROLE_ORGANISATION_ADMIN,
        'organization_id' => $organization->id,
        'is_active' => false,
        'must_change_password' => false,
    ]);

    $this->actingAs($organisationAdmin)
        ->get(route('media.show', $media))
        ->assertForbidden();

    $this->actingAs($organisationAdmin)
        ->get(route('media.access', $media))
        ->assertForbidden();
});

it('denies an anonymous visitor access to group media', function () {
    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);

    $media = createRestrictedMedia(
        $group,
        Media::ACCESS_GROUP
    );

    $this->get(route('media.show', $media))
        ->assertForbidden();

    $this->get(route('media.access', $media))
        ->assertForbidden();
});


it('denies a registered user access to group media', function () {
    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);

    $media = createRestrictedMedia(
        $group,
        Media::ACCESS_GROUP
    );

    $user = User::factory()->create([
        'role' => User::ROLE_REGISTERED_USER,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($user)
        ->get(route('media.show', $media))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('media.access', $media))
        ->assertForbidden();
});


it('allows the matching organisation administrator to access group media', function () {
    Storage::fake('local');

    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);

    $media = createRestrictedMedia(
        $group,
        Media::ACCESS_GROUP
    );

    Storage::disk('local')->put(
        $media->file_path,
        'test group media content'
    );

    $organisationAdmin = User::factory()->create([
        'role' => User::ROLE_ORGANISATION_ADMIN,
        'organization_id' => $organization->id,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($organisationAdmin)
        ->get(route('media.show', $media))
        ->assertOk();

    $this->actingAs($organisationAdmin)
        ->get(route('media.access', $media))
        ->assertOk();
});


it('allows a site administrator to access group media', function () {
    Storage::fake('local');

    $organization = createMediaTestOrganization();
    $group = createMediaTestGroup($organization);

    $media = createRestrictedMedia(
        $group,
        Media::ACCESS_GROUP
    );

    Storage::disk('local')->put(
        $media->file_path,
        'test group media content'
    );

    $siteAdmin = User::factory()->create([
        'role' => User::ROLE_SITE_ADMIN,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($siteAdmin)
        ->get(route('media.show', $media))
        ->assertOk();

    $this->actingAs($siteAdmin)
        ->get(route('media.access', $media))
        ->assertOk();
});