<?php

namespace App\Policies;

use App\Enums\Resource;
use App\Models\User;
use App\Policies\Traits\AuthorizesWithPermissions;

class UserPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::USERS->viewAny());
    }

    public function view(User $user, User $model): bool
    {
        return $this->hasPermission($user, Resource::USERS->view())
            || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::USERS->create());
    }

    public function update(User $user, User $model): bool
    {
        return $this->hasPermission($user, Resource::USERS->update())
            || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->hasPermission($user, Resource::USERS->delete());
    }

    public function restore(User $user, User $model): bool
    {
        return $this->hasPermission($user, Resource::USERS->restore());
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $this->hasPermission($user, Resource::USERS->forceDelete());
    }
}
