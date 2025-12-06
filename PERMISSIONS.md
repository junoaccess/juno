# Permission System Documentation

## Overview

The permission system uses a `{resource}:{action}` format with enum-based helper methods for type safety.

## Enum Helper Methods

### Resource Enum

The `Resource` enum provides convenient methods to generate permission strings:

```php
use App\Enums\Resource;
use App\Enums\Permission;

// Shorthand methods for common permissions
Resource::USERS->viewAny()      // 'users:view_any'
Resource::USERS->view()         // 'users:view'
Resource::USERS->create()       // 'users:create'
Resource::USERS->update()       // 'users:update'
Resource::USERS->delete()       // 'users:delete'
Resource::USERS->restore()      // 'users:restore'
Resource::USERS->forceDelete()  // 'users:force_delete'

// Wildcard for all permissions on a resource
Resource::USERS->wildcard()     // 'users:*'

// Custom permission with any Permission enum
Resource::USERS->permission(Permission::CREATE)  // 'users:create'
```

### Permission Enum

The `Permission` enum also provides helper methods:

```php
use App\Enums\Resource;
use App\Enums\Permission;

// Build permission string from action
Permission::VIEW_ANY->for(Resource::USERS)  // 'users:view_any'
Permission::CREATE->for(Resource::TEAMS)    // 'teams:create'

// Global wildcard (super admin)
Permission::all()  // '*'
```

## Usage in Policies

Policies now use enum helper methods for type safety:

```php
use App\Enums\Resource;
use App\Policies\Traits\AuthorizesWithPermissions;

class UserPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Resource::USERS->viewAny());
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Resource::USERS->create());
    }
}
```

## Flexible hasPermission Method

The `hasPermission` method accepts multiple formats:

```php
// Using Resource enum with shorthand
$this->hasPermission($user, Resource::USERS->viewAny());

// Using Permission enum helper
$this->hasPermission($user, Permission::VIEW_ANY->for(Resource::USERS));

// Using Resource enum with Permission enum
$this->hasPermission($user, Resource::USERS->permission(Permission::VIEW_ANY));

// Using plain string (backward compatible)
$this->hasPermission($user, 'users:view_any');

// Using Resource wildcard
$this->hasPermission($user, Resource::USERS->wildcard());  // grants all user permissions

// Using global wildcard
$this->hasPermission($user, Permission::all());  // grants everything
```

## Wildcard Permissions

The system supports three levels of wildcards:

1. **Specific Permission**: `users:view_any` - Only view any user
2. **Resource Wildcard**: `users:*` - All user permissions
3. **Global Wildcard**: `*` - All permissions on all resources

When checking permissions, the system automatically checks:
- The exact permission
- The resource wildcard (`{resource}:*`)
- The global wildcard (`*`)

## Database Structure

Permissions are stored in the `permissions` table:

| Column      | Type   | Example          |
| ----------- | ------ | ---------------- |
| name        | string | `users:view_any` |
| slug        | string | `users-view-any` |
| description | string | View any user    |

## Example Permission Seeds

```php
// Seeded permissions include:
'organizations:view_any'
'organizations:create'
'organizations:*'  // All organization permissions

'users:view_any'
'users:create'
'users:*'  // All user permissions

'*'  // Super admin - all permissions
```

## Best Practices

1. **Use Enum Methods**: Prefer `Resource::USERS->viewAny()` over `'users:view_any'`
2. **Type Safety**: Enums provide IDE autocomplete and prevent typos
3. **Wildcards**: Use resource wildcards for role-based permissions, global wildcard for super admins only
4. **Consistency**: Always use snake_case for actions (e.g., `view_any`, `force_delete`)
