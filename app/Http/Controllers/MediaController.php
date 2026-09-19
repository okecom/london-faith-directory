<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(
        Request $request,
        Media $media
    ): View {
        $media->load('group.organization');

        $this->authorizeAccess(
            $request,
            $media
        );

        return view(
            'media.show',
            compact('media')
        );
    }


    public function access(
        Request $request,
        Media $media
    ): RedirectResponse|StreamedResponse {
        $media->load('group.organization');

        $this->authorizeAccess(
            $request,
            $media
        );

        if ($media->external_url) {
            return redirect()->away(
                $media->external_url
            );
        }

        abort_unless(
            $media->file_path,
            404,
            'This media resource has no file.'
        );

        $disk = $media->access_level ===
            Media::ACCESS_PUBLIC
                ? 'public'
                : 'local';

        abort_unless(
            Storage::disk($disk)
                ->exists($media->file_path),
            404,
            'The media file could not be found.'
        );

        return Storage::disk($disk)
            ->download(
                $media->file_path,
                basename($media->file_path)
            );
    }


    private function authorizeAccess(
        Request $request,
        Media $media
    ): void {
        if (
            $media->access_level ===
            Media::ACCESS_PUBLIC
        ) {
            return;
        }

        $user = $request->user();

        abort_unless(
            $user &&
            $user->is_active,
            403,
            'You are not authorised to access this media.'
        );

        if (
            $user->role ===
            User::ROLE_SITE_ADMIN
        ) {
            return;
        }

        if (
            $user->role ===
            User::ROLE_ORGANISATION_ADMIN &&
            $user->organization_id ===
                $media->group->organization_id
        ) {
            return;
        }

        abort(
            403,
            'You are not authorised to access this media.'
        );
    }
}