<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    public function paginate(int $perPage = 15)
    {
        return Permission::query()
            ->latest()
            ->paginate($perPage);
    }

    public function loadRelationships(Permission $permission): Permission
    {
        return $permission->load(['roles']);
    }
}
