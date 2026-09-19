<?php

use App\Models\Denomination;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Group;
use App\Models\Location;
use App\Models\Media;
use App\Models\Organization;
use App\Models\Religion;
use App\Models\User;

function createOwnershipOrganization(
    string $name
): Organization {
    $religion = Religion::create([
        'name' => 'Test Religion ' . uniqid(),
    ]);

    $denomination = Denomination::create([
        'religion_id' => $religion->id,
        'name' => 'Test Denomination ' . uniqid(),
    ]);

    $location = Location::create([
        'name' => 'Test Location ' . uniqid(),
    ]);

    return Organization::create([
        'name' => $name,
        'denomination_id' => $denomination->id,
        'location_id' => $location->id,
        'description' => 'Ownership test organisation.',
        'address' => '1 Test Street',
    ]);
}


function createOwnershipGroup(
    Organization $organization,
    string $name
): Group {
    return Group::create([
        'organization_id' => $organization->id,
        'name' => $name,
        'description' => 'Ownership test group.',
        'is_head_office' => false,
    ]);
}


function createOwnershipEvent(
    Group $group,
    string $name
): Event {
    $eventType = EventType::create([
        'name' => 'Test Event Type ' . uniqid(),
    ]);

    $location = Location::create([
        'name' => 'Event Location ' . uniqid(),
    ]);

    return Event::create([
        'group_id' => $group->id,
        'event_type_id' => $eventType->id,
        'location_id' => $location->id,
        'name' => $name,
        'description' => 'Ownership test event.',
        'start_datetime' => now()->addDay(),
    ]);
}


function createOwnershipMedia(
    Group $group,
    string $title
): Media {
    return Media::create([
        'group_id' => $group->id,
        'title' => $title,
        'description' => 'Ownership test media.',
        'type' => 'link',
        'external_url' => 'https://example.com/test',
        'access_level' => Media::ACCESS_PUBLIC,
    ]);
}


function createOwnershipOrganisationAdmin(
    Organization $organization
): User {
    return User::factory()->create([
        'role' => User::ROLE_ORGANISATION_ADMIN,
        'organization_id' => $organization->id,
        'is_active' => true,
        'must_change_password' => false,
    ]);
}


function createOwnershipSiteAdmin(): User
{
    return User::factory()->create([
        'role' => User::ROLE_SITE_ADMIN,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);
}


beforeEach(function () {
    $this->organizationA =
        createOwnershipOrganization(
            'Ownership Organisation A'
        );

    $this->organizationB =
        createOwnershipOrganization(
            'Ownership Organisation B'
        );

    $this->groupA =
        createOwnershipGroup(
            $this->organizationA,
            'Ownership Group A'
        );

    $this->groupB =
        createOwnershipGroup(
            $this->organizationB,
            'Ownership Group B'
        );

    $this->eventA =
        createOwnershipEvent(
            $this->groupA,
            'Ownership Event A'
        );

    $this->eventB =
        createOwnershipEvent(
            $this->groupB,
            'Ownership Event B'
        );

    $this->mediaA =
        createOwnershipMedia(
            $this->groupA,
            'Ownership Media A'
        );

    $this->mediaB =
        createOwnershipMedia(
            $this->groupB,
            'Ownership Media B'
        );

    $this->organizationAdmin =
        createOwnershipOrganisationAdmin(
            $this->organizationA
        );

    $this->siteAdmin =
        createOwnershipSiteAdmin();
});


test(
    'organisation administrator can edit own organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'organizations.manage.edit',
                    $this->organizationA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot edit another organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'organizations.manage.edit',
                    $this->organizationB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot create an organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'organizations.manage.create'
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot archive an organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->delete(
                route(
                    'organizations.manage.destroy',
                    $this->organizationA
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'organizations',
            [
                'id' =>
                    $this->organizationA->id,

                'deleted_at' =>
                    null,
            ]
        );
    }
);


test(
    'organisation administrator sees only own organisation in organisation management',
    function () {
        $response = $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'organizations.manage.index'
                )
            );

        $response->assertOk();

        $response->assertSee(
            $this->organizationA->name
        );

        $response->assertDontSee(
            $this->organizationB->name
        );
    }
);


test(
    'organisation administrator can view own group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'groups.manage.show',
                    $this->groupA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot view another organisations group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'groups.manage.show',
                    $this->groupB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator can create group for own organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'groups.manage.create',
                    $this->organizationA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot create group for another organisation',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'groups.manage.create',
                    $this->organizationB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot update another organisations group',
    function () {
        $originalName =
            $this->groupB->name;

        $this->actingAs(
            $this->organizationAdmin
        )
            ->put(
                route(
                    'groups.manage.update',
                    $this->groupB
                ),
                [
                    'name' =>
                        'Tampered Group Name',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'groups',
            [
                'id' =>
                    $this->groupB->id,

                'name' =>
                    $originalName,
            ]
        );
    }
);


test(
    'organisation administrator cannot archive another organisations group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->delete(
                route(
                    'groups.manage.destroy',
                    $this->groupB
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'groups',
            [
                'id' =>
                    $this->groupB->id,

                'deleted_at' =>
                    null,
            ]
        );
    }
);


test(
    'organisation administrator cannot select another organisation in group management',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'groups.manage.index',
                    [
                        'organization_id' =>
                            $this->organizationB->id,
                    ]
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator can view own event',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.show',
                    $this->eventA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot view another organisations event',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.show',
                    $this->eventB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator can create event for own group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.create',
                    $this->groupA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot create event for another organisations group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.create',
                    $this->groupB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot archive another organisations event',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->delete(
                route(
                    'events.manage.destroy',
                    $this->eventB
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'events',
            [
                'id' =>
                    $this->eventB->id,

                'deleted_at' =>
                    null,
            ]
        );
    }
);


test(
    'organisation administrator cannot select another organisation in event management',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.index',
                    [
                        'organization_id' =>
                            $this->organizationB->id,
                    ]
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot mix organisation and group ids in event management',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'events.manage.index',
                    [
                        'organization_id' =>
                            $this->organizationA->id,

                        'group_id' =>
                            $this->groupB->id,
                    ]
                )
            )
            ->assertNotFound();
    }
);


test(
    'organisation administrator can view own media',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.show',
                    $this->mediaA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot view another organisations media',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.show',
                    $this->mediaB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator can create media for own group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.create',
                    $this->groupA
                )
            )
            ->assertOk();
    }
);


test(
    'organisation administrator cannot create media for another organisations group',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.create',
                    $this->groupB
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot update another organisations media',
    function () {
        $originalTitle =
            $this->mediaB->title;

        $this->actingAs(
            $this->organizationAdmin
        )
            ->put(
                route(
                    'media.manage.update',
                    $this->mediaB
                ),
                [
                    'title' =>
                        'Tampered Media Title',

                    'type' =>
                        'link',

                    'external_url' =>
                        'https://example.com/tampered',

                    'access_level' =>
                        Media::ACCESS_PUBLIC,
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'media',
            [
                'id' =>
                    $this->mediaB->id,

                'title' =>
                    $originalTitle,
            ]
        );
    }
);


test(
    'organisation administrator cannot archive another organisations media',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->delete(
                route(
                    'media.manage.destroy',
                    $this->mediaB
                )
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'media',
            [
                'id' =>
                    $this->mediaB->id,

                'deleted_at' =>
                    null,
            ]
        );
    }
);


test(
    'organisation administrator cannot select another organisation in media management',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.index',
                    [
                        'organization_id' =>
                            $this->organizationB->id,
                    ]
                )
            )
            ->assertForbidden();
    }
);


test(
    'organisation administrator cannot mix organisation and group ids in media management',
    function () {
        $this->actingAs(
            $this->organizationAdmin
        )
            ->get(
                route(
                    'media.manage.index',
                    [
                        'organization_id' =>
                            $this->organizationA->id,

                        'group_id' =>
                            $this->groupB->id,
                    ]
                )
            )
            ->assertNotFound();
    }
);


test(
    'organisation administrator cannot restore media through another group',
    function () {
        $this->mediaB->delete();

        $this->actingAs(
            $this->organizationAdmin
        )
            ->patch(
                route(
                    'media.manage.restore',
                    [
                        'group' =>
                            $this->groupA,

                        'media' =>
                            $this->mediaB->id,
                    ]
                )
            )
            ->assertNotFound();

        expect(
            Media::onlyTrashed()
                ->find($this->mediaB->id)
        )->not->toBeNull();
    }
);


test(
    'site administrator can manage another organisations group',
    function () {
        $this->actingAs(
            $this->siteAdmin
        )
            ->get(
                route(
                    'groups.manage.edit',
                    $this->groupB
                )
            )
            ->assertOk();
    }
);


test(
    'site administrator can manage another organisations event',
    function () {
        $this->actingAs(
            $this->siteAdmin
        )
            ->get(
                route(
                    'events.manage.edit',
                    $this->eventB
                )
            )
            ->assertOk();
    }
);


test(
    'site administrator can manage another organisations media',
    function () {
        $this->actingAs(
            $this->siteAdmin
        )
            ->get(
                route(
                    'media.manage.edit',
                    $this->mediaB
                )
            )
            ->assertOk();
    }
);