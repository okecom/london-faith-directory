<?php

use App\Models\Denomination;
use App\Models\Group;
use App\Models\Location;
use App\Models\Media;
use App\Models\Organization;
use App\Models\Religion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);


function createManagementTestOrganization(
    string $name = 'Media Management Test Organisation'
): Organization {
    $religion = Religion::create([
        'name' => 'Management Test Religion '.uniqid(),
    ]);

    $denomination = Denomination::create([
        'religion_id' => $religion->id,
        'name' => 'Management Test Denomination '.uniqid(),
    ]);

    $location = Location::create([
        'name' => 'Management Test Location '.uniqid(),
    ]);

    return Organization::create([
        'name' => $name,
        'denomination_id' => $denomination->id,
        'location_id' => $location->id,
        'description' => 'Organisation used for media management tests.',
        'address' => '1 Management Test Street, London',
    ]);
}


function createManagementTestGroup(
    Organization $organization
): Group {
    return Group::create([
        'organization_id' => $organization->id,
        'name' => 'Media Management Test Group',
        'description' => 'Group used for media management tests.',
        'is_head_office' => false,
    ]);
}


function createManagementSiteAdmin(): User
{
    return User::factory()->create([
        'role' => User::ROLE_SITE_ADMIN,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);
}


function createManagementTestMedia(
    Group $group,
    array $attributes = []
): Media {
    return Media::create(array_merge([
        'group_id' => $group->id,
        'title' => 'Management Test Media',
        'description' => 'Media used for management testing.',
        'type' => 'document',
        'file_path' => null,
        'external_url' => 'https://example.com/resource',
        'access_level' => Media::ACCESS_PUBLIC,
    ], $attributes));
}


it('creates an external link media record', function () {
    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $response = $this
        ->actingAs($siteAdmin)
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'External Test Resource',
                'description' => 'External resource test.',
                'type' => 'link',
                'external_url' => 'https://example.com/resource',
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        );

    $response->assertRedirect(
        route('media.manage.index', [
            'organization_id' => $organization->id,
            'group_id' => $group->id,
        ])
    );

    $this->assertDatabaseHas('media', [
        'group_id' => $group->id,
        'title' => 'External Test Resource',
        'type' => 'link',
        'file_path' => null,
        'external_url' => 'https://example.com/resource',
        'access_level' => Media::ACCESS_PUBLIC,
    ]);
});


it('stores a public uploaded file on the public disk', function () {
    Storage::fake('public');
    Storage::fake('local');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $file = UploadedFile::fake()->create(
        'public-document.pdf',
        100,
        'application/pdf'
    );

    $this->actingAs($siteAdmin)
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'Public Uploaded Document',
                'description' => 'Public upload test.',
                'type' => 'document',
                'file' => $file,
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        )
        ->assertRedirect();

    $media = Media::where(
        'title',
        'Public Uploaded Document'
    )->firstOrFail();

    expect($media->file_path)->not->toBeNull();

    Storage::disk('public')
        ->assertExists($media->file_path);

    Storage::disk('local')
        ->assertMissing($media->file_path);
});


it('stores restricted uploaded files on the private local disk', function () {
    Storage::fake('public');
    Storage::fake('local');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $file = UploadedFile::fake()->create(
        'restricted-document.pdf',
        100,
        'application/pdf'
    );

    $this->actingAs($siteAdmin)
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'Restricted Uploaded Document',
                'description' => 'Restricted upload test.',
                'type' => 'document',
                'file' => $file,
                'access_level' =>
                    Media::ACCESS_ORGANISATION,
            ]
        )
        ->assertRedirect();

    $media = Media::where(
        'title',
        'Restricted Uploaded Document'
    )->firstOrFail();

    expect($media->file_path)->not->toBeNull();

    Storage::disk('local')
        ->assertExists($media->file_path);

    Storage::disk('public')
        ->assertMissing($media->file_path);
});


it('requires either a file or an external url', function () {
    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $response = $this
        ->actingAs($siteAdmin)
        ->from(route('media.manage.create', $group))
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'Missing Resource',
                'description' => 'No resource supplied.',
                'type' => 'document',
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        );

    $response
        ->assertRedirect(
            route('media.manage.create', $group)
        )
        ->assertSessionHasErrors('file');

    $this->assertDatabaseMissing('media', [
        'title' => 'Missing Resource',
    ]);
});


it('rejects a file and external url supplied together', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $file = UploadedFile::fake()->create(
        'document.pdf',
        100,
        'application/pdf'
    );

    $response = $this
        ->actingAs($siteAdmin)
        ->from(route('media.manage.create', $group))
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'Invalid Double Resource',
                'type' => 'document',
                'file' => $file,
                'external_url' =>
                    'https://example.com/resource',
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        );

    $response
        ->assertRedirect(
            route('media.manage.create', $group)
        )
        ->assertSessionHasErrors('file');

    $this->assertDatabaseMissing('media', [
        'title' => 'Invalid Double Resource',
    ]);
});


it('updates media metadata without replacing its file', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $path = 'media/existing-document.pdf';

    Storage::disk('public')->put(
        $path,
        'existing file contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $path,
            'external_url' => null,
        ]
    );

    $this->actingAs($siteAdmin)
        ->put(
            route('media.manage.update', $media),
            [
                'title' => 'Updated Media Title',
                'description' => 'Updated description.',
                'type' => 'document',
                'external_url' => null,
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        )
        ->assertRedirect();

    $media->refresh();

    expect($media->title)
        ->toBe('Updated Media Title');

    expect($media->file_path)
        ->toBe($path);

    Storage::disk('public')
        ->assertExists($path);
});


it('moves an existing file from public to private storage', function () {
    Storage::fake('public');
    Storage::fake('local');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $path = 'media/move-to-private.pdf';

    Storage::disk('public')->put(
        $path,
        'file contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $path,
            'external_url' => null,
            'access_level' => Media::ACCESS_PUBLIC,
        ]
    );

    $this->actingAs($siteAdmin)
        ->put(
            route('media.manage.update', $media),
            [
                'title' => $media->title,
                'description' => $media->description,
                'type' => $media->type,
                'external_url' => null,
                'access_level' =>
                    Media::ACCESS_ORGANISATION,
            ]
        )
        ->assertRedirect();

    $media->refresh();

    expect($media->access_level)
        ->toBe(Media::ACCESS_ORGANISATION);

    Storage::disk('local')
        ->assertExists($path);

    Storage::disk('public')
        ->assertMissing($path);
});


it('moves an existing file from private to public storage', function () {
    Storage::fake('public');
    Storage::fake('local');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $path = 'media/move-to-public.pdf';

    Storage::disk('local')->put(
        $path,
        'file contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $path,
            'external_url' => null,
            'access_level' =>
                Media::ACCESS_ORGANISATION,
        ]
    );

    $this->actingAs($siteAdmin)
        ->put(
            route('media.manage.update', $media),
            [
                'title' => $media->title,
                'description' => $media->description,
                'type' => $media->type,
                'external_url' => null,
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        )
        ->assertRedirect();

    $media->refresh();

    expect($media->access_level)
        ->toBe(Media::ACCESS_PUBLIC);

    Storage::disk('public')
        ->assertExists($path);

    Storage::disk('local')
        ->assertMissing($path);
});


it('replaces an uploaded file and deletes the old file', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $oldPath = 'media/old-document.pdf';

    Storage::disk('public')->put(
        $oldPath,
        'old contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $oldPath,
            'external_url' => null,
        ]
    );

    $replacement = UploadedFile::fake()->create(
        'replacement.pdf',
        100,
        'application/pdf'
    );

    $this->actingAs($siteAdmin)
        ->put(
            route('media.manage.update', $media),
            [
                'title' => $media->title,
                'description' => $media->description,
                'type' => 'document',
                'file' => $replacement,
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        )
        ->assertRedirect();

    $media->refresh();

    expect($media->file_path)
        ->not->toBe($oldPath);

    Storage::disk('public')
        ->assertMissing($oldPath);

    Storage::disk('public')
        ->assertExists($media->file_path);
});


it('archives media without deleting its physical file', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $path = 'media/archive-test.pdf';

    Storage::disk('public')->put(
        $path,
        'archive test contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $path,
            'external_url' => null,
        ]
    );

    $this->actingAs($siteAdmin)
        ->delete(
            route('media.manage.destroy', $media)
        )
        ->assertRedirect();

    $this->assertSoftDeleted('media', [
        'id' => $media->id,
    ]);

    Storage::disk('public')
        ->assertExists($path);
});


it('restores archived media', function () {
    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $media = createManagementTestMedia($group);

    $media->delete();

    $this->assertSoftDeleted('media', [
        'id' => $media->id,
    ]);

    $this->actingAs($siteAdmin)
        ->patch(
            route(
                'media.manage.restore',
                [
                    'group' => $group,
                    'media' => $media->id,
                ]
            )
        )
        ->assertRedirect(
            route(
                'media.manage.archived',
                $group
            )
        );

    $this->assertDatabaseHas('media', [
        'id' => $media->id,
        'deleted_at' => null,
    ]);
});

it('rejects an external url when an existing uploaded file is retained', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $path = 'media/existing-resource.pdf';

    Storage::disk('public')->put(
        $path,
        'existing contents'
    );

    $media = createManagementTestMedia(
        $group,
        [
            'file_path' => $path,
            'external_url' => null,
        ]
    );

    $response = $this
        ->actingAs($siteAdmin)
        ->from(route('media.manage.edit', $media))
        ->put(
            route('media.manage.update', $media),
            [
                'title' => $media->title,
                'description' => $media->description,
                'type' => $media->type,
                'external_url' =>
                    'https://example.com/new-resource',
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        );

    $response
        ->assertRedirect(
            route('media.manage.edit', $media)
        )
        ->assertSessionHasErrors('file');

    $media->refresh();

    expect($media->file_path)->toBe($path);
    expect($media->external_url)->toBeNull();

    Storage::disk('public')
        ->assertExists($path);
});

it('rejects unsupported uploaded file types', function () {
    Storage::fake('public');

    $organization = createManagementTestOrganization();
    $group = createManagementTestGroup($organization);
    $siteAdmin = createManagementSiteAdmin();

    $file = UploadedFile::fake()->create(
        'unsafe.exe',
        100,
        'application/x-msdownload'
    );

    $response = $this
        ->actingAs($siteAdmin)
        ->from(route('media.manage.create', $group))
        ->post(
            route('media.manage.store', $group),
            [
                'title' => 'Unsupported Upload',
                'description' =>
                    'Unsupported file type test.',
                'type' => 'document',
                'file' => $file,
                'access_level' => Media::ACCESS_PUBLIC,
            ]
        );

    $response
        ->assertRedirect(
            route('media.manage.create', $group)
        )
        ->assertSessionHasErrors('file');

    $this->assertDatabaseMissing('media', [
        'title' => 'Unsupported Upload',
    ]);
});