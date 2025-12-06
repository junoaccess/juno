<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class RolePermissionSeeder
{
    public function seed(Organization $organization): void
    {
        $mapping = config('role-permission-mapping');

        foreach ($mapping as $roleName => $permissions) {
            $role = $this->createOrGetRole($organization, $roleName);
            $this->attachPermissions($role, $permissions);
        }
    }

    protected function createOrGetRole(Organization $organization, string $roleName): Role
    {
        return Role::firstOrCreate(
            [
                'slug' => Str::slug($roleName),
                'organization_id' => $organization->id,
            ],
            [
                'name' => ucfirst($roleName),
                'description' => "Default {$roleName} role for {$organization->name}",
            ]
        );
    }

    protected function attachPermissions(Role $role, array $permissions): void
    {
        foreach ($permissions as $permissionName) {
            $permission = $this->getPermission($permissionName);

            if ($permission) {
                $role->permissions()->syncWithoutDetaching($permission);
            }
        }
    }

    protected function getPermission(string $permissionName): ?Permission
    {
        return Permission::where('name', $permissionName)->first();
    }
}
