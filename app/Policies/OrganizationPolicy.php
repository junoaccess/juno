<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\Organization;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class OrganizationPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->viewAny());
    }

    public function view(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->view());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->create());
    }

    public function update(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->update());
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->delete());
    }

    public function restore(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->restore());
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, Resource::ORGANIZATIONS->forceDelete());
    }
}
