<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\Role;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class RolePolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::ROLES->viewAny());
    }

    public function view(User $user, Role $role): bool
    {
        return $this->hasPermission($user, Resource::ROLES->view());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::ROLES->create());
    }

    public function update(User $user, Role $role): bool
    {
        return $this->hasPermission($user, Resource::ROLES->update());
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->hasPermission($user, Resource::ROLES->delete());
    }

    public function restore(User $user, Role $role): bool
    {
        return $this->hasPermission($user, Resource::ROLES->restore());
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $this->hasPermission($user, Resource::ROLES->forceDelete());
    }
}
