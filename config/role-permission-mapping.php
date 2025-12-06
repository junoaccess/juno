<?php

use App\Enums\Role;

return [
    /*
     * Admin Role Permissions - Full access to everything
     */
    Role::ADMIN->value => [
        'organizations:view_any',
        'organizations:view',
        'organizations:create',
        'organizations:update',
        'organizations:delete',
        'organizations:restore',
        'organizations:force_delete',

        'users:view_any',
        'users:view',
        'users:create',
        'users:update',
        'users:delete',
        'users:restore',
        'users:force_delete',

        'teams:view_any',
        'teams:view',
        'teams:create',
        'teams:update',
        'teams:delete',
        'teams:restore',
        'teams:force_delete',

        'invitations:view_any',
        'invitations:view',
        'invitations:create',
        'invitations:update',
        'invitations:delete',
        'invitations:restore',
        'invitations:force_delete',

        'roles:view_any',
        'roles:view',
        'roles:create',
        'roles:update',
        'roles:delete',
        'roles:restore',
        'roles:force_delete',

        'permissions:view_any',
        'permissions:view',
        'permissions:create',
        'permissions:update',
        'permissions:delete',
        'permissions:restore',
        'permissions:force_delete',
    ],

    /*
     * Owner Role Permissions - Full access to their organization
     */
    Role::OWNER->value => [
        'organizations:view',
        'organizations:create',
        'organizations:update',
        'organizations:delete',

        'users:view_any',
        'users:view',
        'users:create',
        'users:update',
        'users:delete',
        'users:restore',
        'users:force_delete',

        'teams:view_any',
        'teams:view',
        'teams:create',
        'teams:update',
        'teams:delete',
        'teams:restore',
        'teams:force_delete',

        'invitations:view_any',
        'invitations:view',
        'invitations:create',
        'invitations:update',
        'invitations:delete',
        'invitations:restore',
        'invitations:force_delete',

        'roles:view_any',
        'roles:view',
        'roles:create',
        'roles:update',
        'roles:delete',
        'roles:restore',
        'roles:force_delete',

        'permissions:view_any',
        'permissions:view',
    ],

    /*
     * Manager Role Permissions - Limited management capabilities
     */
    Role::MANAGER->value => [
        'organizations:view',
        'organizations:update',

        'users:view_any',
        'users:view',

        'teams:view_any',
        'teams:view',
        'teams:create',
        'teams:update',
        'teams:delete',

        'invitations:view_any',
        'invitations:view',
        'invitations:create',
        'invitations:update',
        'invitations:delete',
    ],

    /*
     * Staff Role Permissions - Basic access
     */
    Role::STAFF->value => [
        'users:view',
        'users:update',

        'teams:view',
    ],

    /*
     * Customer Role Permissions - Minimal access
     */
    Role::CUSTOMER->value => [
        'users:view',
        'users:update',
    ],
];
