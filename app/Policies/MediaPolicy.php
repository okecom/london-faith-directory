<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function before(
        User $user,
        string $ability
    ): bool|null {
        if (
            $user->is_active &&
            $user->role === User::ROLE_SITE_ADMIN
        ) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->isOrganisationAdmin($user);
    }

    public function view(
        User $user,
        Media $media
    ): bool {
        return $this->ownsMedia(
            $user,
            $media
        );
    }

    public function create(
        User $user,
        Group $group
    ): bool {
        return $this->ownsGroup(
            $user,
            $group
        );
    }

    public function update(
        User $user,
        Media $media
    ): bool {
        return $this->ownsMedia(
            $user,
            $media
        );
    }

    public function delete(
        User $user,
        Media $media
    ): bool {
        return $this->ownsMedia(
            $user,
            $media
        );
    }

    public function restore(
        User $user,
        Media $media
    ): bool {
        return $this->ownsMedia(
            $user,
            $media
        );
    }

    public function forceDelete(
        User $user,
        Media $media
    ): bool {
        return false;
    }

    private function isOrganisationAdmin(
        User $user
    ): bool {
        return $user->is_active &&
            $user->role ===
                User::ROLE_ORGANISATION_ADMIN &&
            $user->organization_id !== null;
    }

    private function ownsGroup(
        User $user,
        Group $group
    ): bool {
        return $this->isOrganisationAdmin($user) &&
            $user->organization_id ===
                $group->organization_id;
    }

    private function ownsMedia(
        User $user,
        Media $media
    ): bool {
        $media->loadMissing('group');

        return $this->ownsGroup(
            $user,
            $media->group
        );
    }
}