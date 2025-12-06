# Admin System Documentation

## Overview

The application includes a comprehensive admin system that allows users with the `ADMIN` role to perform any action in the system, bypassing all policy checks. This is useful for system administrators who need full access to manage the application.

## Features

1. **Admin User Creation Command** - Create admin users via console
2. **Global Authorization Gate** - ADMIN role bypasses all policy checks
3. **Organization Assignment** - Admin users are automatically assigned to an organization
4. **Comprehensive Testing** - Full test coverage for admin functionality

## Creating Admin Users

### Using the Console Command

The `admin:create` command allows you to create admin users with full system access.

#### Basic Usage

```bash
php artisan admin:create
```

This will prompt you for:
- Email address
- Password (or generate one automatically)
- First name
- Last name
- Organization name

#### Using Options (Non-Interactive)

```bash
php artisan admin:create \
  --email=admin@example.com \
  --password=SecurePassword123! \
  --first-name=John \
  --last-name=Doe \
  --organization="Acme Inc" \
  --force
```

#### Available Options

- `--email=EMAIL` - The email address for the admin user
- `--password=PASSWORD` - The password (leave empty to auto-generate)
- `--first-name=NAME` - First name of the admin
- `--last-name=NAME` - Last name of the admin
- `--organization=NAME` - Organization name (created if doesn't exist)
- `--force` - Skip confirmation prompts

### What Happens When You Create an Admin

1. **Organization Creation/Assignment**
   - If the organization doesn't exist, it's created
   - If it exists (by slug), the existing one is used
   - Admin user is assigned to the organization with `is_default = true`

2. **User Creation**
   - User is created with the provided details
   - Email is automatically verified
   - Password is hashed using Laravel's secure hashing
   - User's `current_organization_id` is set to the organization

3. **Role Assignment**
   - `ADMIN` role is created for the organization (if needed)
   - All existing permissions are attached to the ADMIN role
   - User is assigned the ADMIN role within the organization

4. **Output**
   - Success message with credential summary
   - Organization details including slug
   - Auto-generated password (if applicable)

### Example Output

```
Creating admin user...

 ┌────────────┬─────────────────┐
 │ Field      │ Value           │
 ├────────────┼─────────────────┤
 │ Email      │ admin@test.com  │
 │ First Name │ Admin           │
 │ Last Name  │ User            │
 │ Organization│ Test Org       │
 │ Role       │ admin           │
 └────────────┴─────────────────┘

 Create this admin user? (yes/no) [yes]:
 > yes

✓ Created organization: Test Org
✓ Created admin user: admin@test.com
✓ Assigned user to organization: Test Org
✓ Created ADMIN role for organization
✓ Granted all permissions (15) to ADMIN role
✓ Assigned ADMIN role to user

✅ Admin user created successfully!

 ┌─────────────────┬──────────────────┐
 │ Credential      │ Value            │
 ├─────────────────┼──────────────────┤
 │ Email           │ admin@test.com   │
 │ Password        │ password123      │
 │ Organization    │ Test Org         │
 │ Organization Slug│ test-org        │
 └─────────────────┴──────────────────┘
```

## Global Authorization Gate

### How It Works

The `AppServiceProvider` includes a global authorization gate that checks if a user has the `ADMIN` role. If they do, all authorization checks automatically pass.

```php
// In AppServiceProvider::boot()
Gate::before(function ($user, $ability) {
    // Check if the user has the ADMIN role in any organization
    return $user->roles()->where('name', 'admin')->exists() ? true : null;
});
```

### Authorization Behavior

- **Admin Users**: Bypass all policy checks, can perform any action
- **Non-Admin Users**: Follow normal policy authorization rules
- **Return Values**:
  - `true` - Authorization passes (for admin users)
  - `null` - Continue with normal policy checks (for non-admin users)

### What Actions Can Admins Perform?

Admin users can perform **any action** in the system, including:

- View any resource (`viewAny`, `view`)
- Create any resource (`create`)
- Update any resource (`update`)
- Delete any resource (`delete`)
- Restore soft-deleted resources (`restore`)
- Force delete resources (`forceDelete`)
- Any custom ability defined in your policies

### Example Authorization Checks

```php
// In a controller or policy
Gate::authorize('delete', $user); // ✅ Always passes for admin
Gate::allows('viewAny', Post::class); // ✅ Always true for admin
$this->authorize('update', $post); // ✅ Always passes for admin

// In a Blade view
@can('delete', $user)
    <!-- ✅ Always shown for admin users -->
@endcan
```

## Testing

### Running Tests

```bash
# Run all admin-related tests
php artisan test --filter=CreateAdminUser
php artisan test --filter=AdminGlobalAccess

# Run all tests
php artisan test
```

### Test Coverage

The admin system includes comprehensive tests:

1. **CreateAdminUserTest**
   - ✅ Creates admin user with organization
   - ✅ Uses existing organization if slug matches
   - ✅ Validates email uniqueness
   - ✅ Accepts password as option
   - ✅ Assigns all permissions to admin role when permissions exist
   - ✅ Creates admin role without permissions when no permissions exist
   - ✅ Sets user current organization to the created organization

2. **AdminGlobalAccessTest**
   - ✅ Allows admin users to perform any action
   - ✅ Does not bypass authorization for non-admin users
   - ✅ Allows admin users even without explicit policies

## Security Considerations

### Important Notes

- **Power User**: Admin users have unrestricted access to all resources
- **Use Sparingly**: Only grant ADMIN role to trusted system administrators
- **Audit Trail**: Consider implementing activity logging for admin actions
- **Production Safety**: Always use strong passwords for admin users

### Password Security

- Minimum 8 characters required
- Auto-generated passwords are 16 characters long
- All passwords are hashed using Laravel's `Hash::make()`
- Passwords are cast to `hashed` in the User model

### Organization Isolation

While admins can bypass authorization checks, the system still respects:
- Organization scope (via `UserOrganizationScope`)
- Current organization context
- Database-level constraints

Admins are **not** automatically scoped to all organizations - they still operate within their assigned organizations by default. The authorization bypass only affects policy checks.

## Integration with DatabaseSeeder

If you want to automatically create an admin user during seeding:

```php
// In database/seeders/DatabaseSeeder.php
public function run(): void
{
    // ... other seeders ...

    // Create admin user if in local/staging environment
    if (app()->environment(['local', 'staging'])) {
        Artisan::call('admin:create', [
            '--email' => 'admin@example.com',
            '--password' => 'password',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--organization' => 'Default Organization',
            '--force' => true,
        ]);
    }
}
```

## Troubleshooting

### Command Fails with Validation Error

**Problem**: Email already exists

**Solution**: Use a different email or delete the existing user first

```bash
# Check if user exists
php artisan tinker
>>> User::where('email', 'admin@example.com')->first();

# Delete if needed
>>> User::where('email', 'admin@example.com')->delete();
```

### Admin User Can't Access Resources

**Problem**: User has ADMIN role but can't access resources

**Checklist**:
1. ✅ Verify user has ADMIN role: `$user->roles()->where('name', 'admin')->exists()`
2. ✅ Verify global gate is registered in `AppServiceProvider::boot()`
3. ✅ Verify user is authenticated
4. ✅ Check if specific policies have explicit denials before the gate

### Organization Not Found

**Problem**: Organization slug doesn't match

**Solution**: Check the slug generation

```php
// Organization slugs are auto-generated from names
'Test Organization' -> 'test-organization'
'Acme Inc' -> 'acme-inc'
```

## Role System Integration

### Role Enum

The ADMIN role is defined in `app/Enums/Role.php`:

```php
enum Role: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';
}
```

### Database Structure

- Roles are organization-specific (have `organization_id`)
- Users can have multiple roles across different organizations
- Pivot table: `organization_user_role` with `organization_id`
- ADMIN role in any organization grants global access

## Files Modified/Created

### Created Files
- `app/Console/Commands/CreateAdminUser.php` - Admin creation command
- `tests/Feature/Commands/CreateAdminUserTest.php` - Command tests
- `tests/Feature/Authorization/AdminGlobalAccessTest.php` - Authorization tests
- `ADMIN_SYSTEM.md` - This documentation

### Modified Files
- `app/Providers/AppServiceProvider.php` - Added `Gate::before()` for admin access
- `app/Models/User.php` - Added `email_verified_at` and `current_organization_id` to fillable
- `database/factories/PermissionFactory.php` - Added factory definition for testing

## Best Practices

1. **Environment-Specific**: Only create admin users in appropriate environments
2. **Strong Passwords**: Always use strong passwords for admin accounts
3. **Document Access**: Keep a record of who has admin access
4. **Regular Audits**: Periodically review admin user list
5. **Principle of Least Privilege**: Only grant admin when necessary
6. **Two-Factor Authentication**: Consider enabling 2FA for admin accounts
7. **Activity Logging**: Implement logging for sensitive admin actions

## Future Enhancements

Potential improvements to consider:

- [ ] Admin activity logging
- [ ] Super admin vs. organization admin distinction
- [ ] Time-limited admin access
- [ ] Admin notification system
- [ ] Admin dashboard
- [ ] Audit trail for admin actions
- [ ] Multi-factor authentication requirement for admins
- [ ] Admin impersonation feature (for support)
