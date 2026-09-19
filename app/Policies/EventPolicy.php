<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;

class EventPolicy
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
        Event $event
    ): bool {
        return $this->ownsEvent(
            $user,
            $event
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
        Event $event
    ): bool {
        return $this->ownsEvent(
            $user,
            $event
        );
    }

    public function delete(
        User $user,
        Event $event
    ): bool {
        return $this->ownsEvent(
            $user,
            $event
        );
    }

    public function restore(
        User $user,
        Event $event
    ): bool {
        return $this->ownsEvent(
            $user,
            $event
        );
    }

    public function forceDelete(
        User $user,
        Event $event
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

    private function ownsEvent(
        User $user,
        Event $event
    ): bool {
        $event->loadMissing('group');

        return $this->ownsGroup(
            $user,
            $event->group
        );
    }
}