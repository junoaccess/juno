<?php

namespace App\Policies\Traits;

use App\Enums\Permission;
use App\Enums\Resource;
use App\Models\User;

trait AuthorizesWithPermissions
{
    protected function hasPermission(User $user, Resource|Permission|string $permission, ?Permission $action = null): bool
    {
        $permissionString = match (true) {
            $permission instanceof Resource && $action instanceof Permission => $permission->permission($action),
            $permission instanceof Resource => $permission->wildcard(),
            $permission instanceof Permission && null === $action => $permission::all(),
            is_string($permission) => $permission,
            default => throw new \InvalidArgumentException('Invalid permission format'),
        };

        // Support wildcard permissions (e.g., 'users:*' grants all user permissions)
        $parts = explode(':', $permissionString);
        $resource = $parts[0] ?? '';

        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permissionString, $resource) {
                $query->where('name', $permissionString)
                    ->orWhere('name', $resource.':*')
                    ->orWhere('name', '*');
            })
            ->exists();
    }
}
