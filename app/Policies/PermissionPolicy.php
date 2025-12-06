<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\Permission;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class PermissionPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->viewAny());
    }

    public function view(User $user, Permission $permission): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->view());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->create());
    }

    public function update(User $user, Permission $permission): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->update());
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->delete());
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->restore());
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $this->hasPermission($user, Resource::PERMISSIONS->forceDelete());
    }
}
