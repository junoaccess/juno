<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\Team;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class TeamPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->viewAny());
    }

    public function view(User $user, Team $team): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->view());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->create());
    }

    public function update(User $user, Team $team): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->update());
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->delete());
    }

    public function restore(User $user, Team $team): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->restore());
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return $this->hasPermission($user, Resource::TEAMS->forceDelete());
    }
}
