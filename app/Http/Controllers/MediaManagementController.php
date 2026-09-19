<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Media;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize(
            'viewAny',
            Media::class
        );

        $organizationsQuery =
            Organization::orderBy('name');

        /*
         * Organisation Administrators may select
         * only their assigned organisation.
         */
        if (
            $request->user()->role ===
            User::ROLE_ORGANISATION_ADMIN
        ) {
            $organizationsQuery->where(
                'id',
                $request->user()->organization_id
            );
        }

        $organizations =
            $organizationsQuery->get();

        $selectedOrganization = null;
        $groups = collect();
        $selectedGroup = null;
        $media = collect();

        if ($request->filled('organization_id')) {
            $selectedOrganization =
                Organization::findOrFail(
                    $request->input(
                        'organization_id'
                    )
                );

            /*
             * Protect against changing
             * organization_id in the query string.
             */
            $this->authorize(
                'view',
                $selectedOrganization
            );

            $groups = $selectedOrganization
                ->groups()
                ->orderByDesc('is_head_office')
                ->orderBy('name')
                ->get();
        }

        if (
            $selectedOrganization &&
            $request->filled('group_id')
        ) {
            /*
             * The Group must belong to the selected
             * Organisation.
             */
            $selectedGroup = Group::where(
                'organization_id',
                $selectedOrganization->id
            )
                ->findOrFail(
                    $request->input('group_id')
                );

            $this->authorize(
                'view',
                $selectedGroup
            );

            $media = $selectedGroup
                ->media()
                ->orderByDesc('created_at')
                ->paginate(5)
                ->withQueryString();
        }

        return view('media.manage.index', [
            'organizations' =>
                $organizations,

            'selectedOrganization' =>
                $selectedOrganization,

            'groups' =>
                $groups,

            'selectedGroup' =>
                $selectedGroup,

            'media' =>
                $media,
        ]);
    }


    public function create(Group $group): View
    {
        /*
         * The Group determines which Organisation
         * will own the new Media record.
         */
        $this->authorize(
            'create',
            [
                Media::class,
                $group,
            ]
        );

        $group->load('organization');

        return view('media.manage.create', [
            'group' => $group,
        ]);
    }


    public function store(
        Request $request,
        Group $group
    ): RedirectResponse {
        /*
         * Authorization must happen before
         * validation or file storage.
         */
        $this->authorize(
            'create',
            [
                Media::class,
                $group,
            ]
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                'string',
                'in:document,image,audio,video,link',
            ],

            'file' => [
                'nullable',
                'file',
                'mimetypes:application/pdf,image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/wav,audio/ogg,video/mp4,video/webm,video/quicktime',
                'max:10240',
            ],

            'external_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'access_level' => [
                'required',
                'in:' .
                    Media::ACCESS_PUBLIC . ',' .
                    Media::ACCESS_ORGANISATION . ',' .
                    Media::ACCESS_GROUP,
            ],
        ]);


        /*
         * A Media record must contain either
         * an uploaded file or an external URL.
         */
        if (
            ! $request->hasFile('file') &&
            empty($validated['external_url'])
        ) {
            return back()
                ->withErrors([
                    'file' =>
                        'Please upload a file or provide an external URL.',
                ])
                ->withInput();
        }


        /*
         * A Media record cannot contain both.
         */
        if (
            $request->hasFile('file') &&
            ! empty($validated['external_url'])
        ) {
            return back()
                ->withErrors([
                    'file' =>
                        'Please provide either a file or an external URL, not both.',
                ])
                ->withInput();
        }


        $filePath = null;

        if ($request->hasFile('file')) {
            /*
             * Public Media is stored on the public
             * disk. Restricted Media stays private.
             */
            $disk =
                $validated['access_level'] ===
                Media::ACCESS_PUBLIC
                    ? 'public'
                    : 'local';

            $filePath = $request
                ->file('file')
                ->store(
                    'media',
                    $disk
                );
        }


        $media = Media::create([
            'group_id' =>
                $group->id,

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'type' =>
                $validated['type'],

            'file_path' =>
                $filePath,

            'external_url' =>
                $validated['external_url'] ?? null,

            'access_level' =>
                $validated['access_level'],
        ]);


        return redirect()
            ->route('media.manage.index', [
                'organization_id' =>
                    $group->organization_id,

                'group_id' =>
                    $group->id,
            ])
            ->with(
                'success',
                'Media #' .
                $media->id .
                ' (' .
                $media->title .
                ') was created successfully.'
            );
    }


    public function show(Media $media): View
    {
        $this->authorize(
            'view',
            $media
        );

        $media->load(
            'group.organization'
        );

        return view('media.manage.show', [
            'media' => $media,
        ]);
    }


    public function edit(Media $media): View
    {
        $this->authorize(
            'update',
            $media
        );

        $media->load(
            'group.organization'
        );

        return view('media.manage.edit', [
            'media' => $media,
        ]);
    }


    public function update(
        Request $request,
        Media $media
    ): RedirectResponse {
        /*
         * This must occur before validation,
         * file creation, movement or deletion.
         */
        $this->authorize(
            'update',
            $media
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                'string',
                'in:document,image,audio,video,link',
            ],

            'file' => [
                'nullable',
                'file',
                'mimetypes:application/pdf,image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/wav,audio/ogg,video/mp4,video/webm,video/quicktime',
                'max:10240',
            ],

            'external_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'access_level' => [
                'required',
                'in:' .
                    Media::ACCESS_PUBLIC . ',' .
                    Media::ACCESS_ORGANISATION . ',' .
                    Media::ACCESS_GROUP,
            ],
        ]);


        /*
         * Existing uploaded files cannot coexist
         * with an external URL.
         */
        if (
            ! empty($validated['external_url']) &&
            (
                $request->hasFile('file') ||
                $media->file_path
            )
        ) {
            return back()
                ->withErrors([
                    'file' =>
                        'Please provide either a file or an external URL, not both.',
                ])
                ->withInput();
        }


        $oldDisk =
            $media->access_level ===
            Media::ACCESS_PUBLIC
                ? 'public'
                : 'local';

        $newFilePath =
            $media->file_path;


        /*
         * If a replacement file is uploaded,
         * store it first and then remove the old
         * physical file.
         */
        if ($request->hasFile('file')) {
            $newDisk =
                $validated['access_level'] ===
                Media::ACCESS_PUBLIC
                    ? 'public'
                    : 'local';

            $newFilePath = $request
                ->file('file')
                ->store(
                    'media',
                    $newDisk
                );

            if ($media->file_path) {
                Storage::disk(
                    $oldDisk
                )->delete(
                    $media->file_path
                );
            }
        }


        /*
         * If the access level changes while the
         * existing file is retained, move the file
         * between public and private storage.
         */
        if (
            ! $request->hasFile('file') &&
            $media->file_path &&
            $media->access_level !==
                $validated['access_level']
        ) {
            $newDisk =
                $validated['access_level'] ===
                Media::ACCESS_PUBLIC
                    ? 'public'
                    : 'local';

            $contents = Storage::disk(
                $oldDisk
            )->get(
                $media->file_path
            );

            Storage::disk(
                $newDisk
            )->put(
                $media->file_path,
                $contents
            );

            Storage::disk(
                $oldDisk
            )->delete(
                $media->file_path
            );
        }


        $media->update([
            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'type' =>
                $validated['type'],

            'file_path' =>
                $newFilePath,

            'external_url' =>
                $validated['external_url'] ?? null,

            'access_level' =>
                $validated['access_level'],
        ]);


        return redirect()
            ->route('media.manage.index', [
                'organization_id' =>
                    $media->group->organization_id,

                'group_id' =>
                    $media->group_id,
            ])
            ->with(
                'success',
                'Media #' .
                $media->id .
                ' (' .
                $media->title .
                ') was updated successfully.'
            );
    }


    public function destroy(
        Media $media
    ): RedirectResponse {
        /*
         * Protect direct Media ID manipulation.
         */
        $this->authorize(
            'delete',
            $media
        );

        $organizationId =
            $media->group->organization_id;

        $groupId =
            $media->group_id;

        $mediaId =
            $media->id;

        $mediaTitle =
            $media->title;


        /*
         * Soft archive only. The physical file
         * deliberately remains in storage.
         */
        $media->delete();


        return redirect()
            ->route('media.manage.index', [
                'organization_id' =>
                    $organizationId,

                'group_id' =>
                    $groupId,
            ])
            ->with(
                'success',
                'Media #' .
                $mediaId .
                ' (' .
                $mediaTitle .
                ') was archived successfully.'
            );
    }


    public function archived(
        Group $group
    ): View {
        /*
         * The Group determines ownership of the
         * archived Media collection.
         */
        $this->authorize(
            'view',
            $group
        );

        $group->load(
            'organization'
        );

        $media = Media::onlyTrashed()
            ->where(
                'group_id',
                $group->id
            )
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('media.manage.archived', [
            'group' => $group,
            'media' => $media,
        ]);
    }


    public function restore(
        Group $group,
        int $media
    ): RedirectResponse {
        /*
         * Authorize the parent Group before
         * looking up archived Media.
         */
        $this->authorize(
            'view',
            $group
        );

        /*
         * Constrain the archived Media record to
         * the Group in the URL. This prevents an
         * ID from another Group being substituted.
         */
        $media = Media::onlyTrashed()
            ->where(
                'group_id',
                $group->id
            )
            ->findOrFail($media);

        $this->authorize(
            'restore',
            $media
        );


        $media->restore();


        return redirect()
            ->route('media.manage.archived', [
                'group' => $group,
            ])
            ->with(
                'success',
                'Media #' .
                $media->id .
                ' (' .
                $media->title .
                ') was restored successfully.'
            );
    }
}