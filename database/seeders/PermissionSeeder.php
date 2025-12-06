<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Organization permissions
            ['name' => 'organizations:view_any', 'description' => 'View any organization'],
            ['name' => 'organizations:view', 'description' => 'View organization details'],
            ['name' => 'organizations:create', 'description' => 'Create new organizations'],
            ['name' => 'organizations:update', 'description' => 'Update organizations'],
            ['name' => 'organizations:delete', 'description' => 'Delete organizations'],
            ['name' => 'organizations:restore', 'description' => 'Restore deleted organizations'],
            ['name' => 'organizations:force_delete', 'description' => 'Permanently delete organizations'],
            ['name' => 'organizations:*', 'description' => 'All organization permissions'],

            // User permissions
            ['name' => 'users:view_any', 'description' => 'View any user'],
            ['name' => 'users:view', 'description' => 'View user details'],
            ['name' => 'users:create', 'description' => 'Create new users'],
            ['name' => 'users:update', 'description' => 'Update users'],
            ['name' => 'users:delete', 'description' => 'Delete users'],
            ['name' => 'users:restore', 'description' => 'Restore deleted users'],
            ['name' => 'users:force_delete', 'description' => 'Permanently delete users'],
            ['name' => 'users:*', 'description' => 'All user permissions'],

            // Team permissions
            ['name' => 'teams:view_any', 'description' => 'View any team'],
            ['name' => 'teams:view', 'description' => 'View team details'],
            ['name' => 'teams:create', 'description' => 'Create new teams'],
            ['name' => 'teams:update', 'description' => 'Update teams'],
            ['name' => 'teams:delete', 'description' => 'Delete teams'],
            ['name' => 'teams:restore', 'description' => 'Restore deleted teams'],
            ['name' => 'teams:force_delete', 'description' => 'Permanently delete teams'],
            ['name' => 'teams:*', 'description' => 'All team permissions'],

            // Invitation permissions
            ['name' => 'invitations:view_any', 'description' => 'View any invitation'],
            ['name' => 'invitations:view', 'description' => 'View invitation details'],
            ['name' => 'invitations:create', 'description' => 'Create new invitations'],
            ['name' => 'invitations:update', 'description' => 'Update invitations'],
            ['name' => 'invitations:delete', 'description' => 'Delete invitations'],
            ['name' => 'invitations:restore', 'description' => 'Restore deleted invitations'],
            ['name' => 'invitations:force_delete', 'description' => 'Permanently delete invitations'],
            ['name' => 'invitations:*', 'description' => 'All invitation permissions'],

            // Role permissions
            ['name' => 'roles:view_any', 'description' => 'View any role'],
            ['name' => 'roles:view', 'description' => 'View role details'],
            ['name' => 'roles:create', 'description' => 'Create new roles'],
            ['name' => 'roles:update', 'description' => 'Update roles'],
            ['name' => 'roles:delete', 'description' => 'Delete roles'],
            ['name' => 'roles:restore', 'description' => 'Restore deleted roles'],
            ['name' => 'roles:force_delete', 'description' => 'Permanently delete roles'],
            ['name' => 'roles:*', 'description' => 'All role permissions'],

            // Permission permissions
            ['name' => 'permissions:view_any', 'description' => 'View any permission'],
            ['name' => 'permissions:view', 'description' => 'View permission details'],
            ['name' => 'permissions:create', 'description' => 'Create new permissions'],
            ['name' => 'permissions:update', 'description' => 'Update permissions'],
            ['name' => 'permissions:delete', 'description' => 'Delete permissions'],
            ['name' => 'permissions:restore', 'description' => 'Restore deleted permissions'],
            ['name' => 'permissions:force_delete', 'description' => 'Permanently delete permissions'],
            ['name' => 'permissions:*', 'description' => 'All permission permissions'],

            // Global wildcard
            ['name' => '*', 'description' => 'All permissions (super admin)'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
