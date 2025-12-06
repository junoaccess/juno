<?php

namespace App\Enums;

enum Resource: string
{
    case ORGANIZATIONS = 'organizations';
    case USERS = 'users';
    case TEAMS = 'teams';
    case INVITATIONS = 'invitations';
    case ROLES = 'roles';
    case PERMISSIONS = 'permissions';
    case RESOURCES = 'resources';

    public function permission(Permission $permission): string
    {
        return $this->value.':'.$permission->value;
    }

    public function viewAny(): string
    {
        return $this->permission(Permission::VIEW_ANY);
    }

    public function view(): string
    {
        return $this->permission(Permission::VIEW);
    }

    public function create(): string
    {
        return $this->permission(Permission::CREATE);
    }

    public function update(): string
    {
        return $this->permission(Permission::UPDATE);
    }

    public function delete(): string
    {
        return $this->permission(Permission::DELETE);
    }

    public function restore(): string
    {
        return $this->permission(Permission::RESTORE);
    }

    public function forceDelete(): string
    {
        return $this->permission(Permission::FORCE_DELETE);
    }

    public function wildcard(): string
    {
        return $this->value.':*';
    }
}
