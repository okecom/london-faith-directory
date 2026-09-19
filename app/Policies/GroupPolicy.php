<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Group;
use App\Models\User;

class GroupPolicy
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
        Group $group
    ): bool {
        return $this->ownsGroup(
            $user,
            $group
        );
    }

    public function create(
        User $user,
        Organization $organization
    ): bool {
        return $this->isOrganisationAdmin($user) &&
            $user->organization_id ===
                $organization->id;
    }

    public function update(
        User $user,
        Group $group
    ): bool {
        return $this->ownsGroup(
            $user,
            $group
        );
    }

    public function delete(
        User $user,
        Group $group
    ): bool {
        return $this->ownsGroup(
            $user,
            $group
        );
    }

    public function restore(
        User $user,
        Group $group
    ): bool {
        return $this->ownsGroup(
            $user,
            $group
        );
    }

    public function forceDelete(
        User $user,
        Group $group
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
}