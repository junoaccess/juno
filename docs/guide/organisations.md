# Organisations and Access Control

## What are Organisations?

Organisations are the fundamental unit of data isolation in Juno. Every resource (users, teams, roles, permissions) belongs to an organisation, and data is automatically scoped to ensure complete isolation.

## Organisation Structure

```
Organisation
├── Users (members)
├── Teams
├── Roles
├── Permissions
└── Invitations
```

## Key Features

### Data Isolation

All queries are automatically scoped to the current organisation using Laravel's global scopes. This ensures that users can only access data from organisations they belong to.

### Multi-Organisation Users

Users can belong to multiple organisations and switch between them. Each user has a `current_organisation_id` that determines which organisation's data they're viewing.

### Organisation Roles

Roles are organisation-specific, allowing fine-grained control over permissions. A user might be an admin in one organisation but have limited permissions in another.

## Role-Based Access Control

### Permission Format

Permissions follow the `resource:action` pattern:

- `users:view_any` - View all users
- `users:view` - View a specific user
- `users:create` - Create users
- `users:update` - Update users
- `users:delete` - Delete users

### Wildcard Permissions

Wildcard permissions provide flexible access control:

- `users:*` - All permissions for users
- `teams:*` - All permissions for teams
- `*` - Global super admin (all permissions)

### Type-Safe Enums

Juno provides type-safe enum helpers for permissions:

```php
use App\Enums\Resource;
use App\Enums\Permission;

// Using Resource enum
Resource::USERS->viewAny();   // returns "users:view_any"
Resource::USERS->create();    // returns "users:create"
Resource::USERS->wildcard();  // returns "users:*"

// Using Permission enum
Permission::VIEW_ANY->for(Resource::USERS);  // returns "users:view_any"
```

## Teams

Teams provide an additional layer of organization within an organisation. Users can belong to multiple teams, enabling collaboration and permission scoping at the team level.

## Invitation System

The invitation system allows organisations to invite new users via email:

- **Single-use invitations** - Automatically revoked after acceptance
- **Role assignment** - Invitations can specify roles
- **Token-based** - Secure, hashed invitation tokens
- **Email notifications** - Automatic email delivery

## Access Control Flow

1. User authenticates and selects an organisation
2. Organisation ID is set in session
3. All queries automatically scope to that organisation
4. Policies check role permissions for actions
5. User can switch organisations at any time

## Next Steps

- Learn about [Roles & Permissions](/guide/roles-and-permissions) in detail
- Understand [Teams](/guide/teams) structure
- Explore [Invitations](/guide/invitations) workflow
