<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
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
        Organization $organization
    ): bool {
        return $this->ownsOrganization(
            $user,
            $organization
        );
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(
        User $user,
        Organization $organization
    ): bool {
        return $this->ownsOrganization(
            $user,
            $organization
        );
    }

    public function delete(
        User $user,
        Organization $organization
    ): bool {
        return false;
    }

    public function restore(
        User $user,
        Organization $organization
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        Organization $organization
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

    private function ownsOrganization(
        User $user,
        Organization $organization
    ): bool {
        return $this->isOrganisationAdmin($user) &&
            $user->organization_id ===
                $organization->id;
    }
}