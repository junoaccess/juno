<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\Invitation;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class InvitationPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->viewAny());
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->view());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->create());
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->update());
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->delete());
    }

    public function restore(User $user, Invitation $invitation): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->restore());
    }

    public function forceDelete(User $user, Invitation $invitation): bool
    {
        return $this->hasPermission($user, Resource::INVITATIONS->forceDelete());
    }
}
