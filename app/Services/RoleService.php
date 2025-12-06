<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function paginate(int $perPage = 15)
    {
        return Role::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'organization_id' => $data['organization_id'],
        ]);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ]));

        return $role->fresh();
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function restore(Role $role): bool
    {
        return $role->restore();
    }

    public function loadRelationships(Role $role): Role
    {
        return $role->load(['organization', 'permissions', 'users']);
    }
}
