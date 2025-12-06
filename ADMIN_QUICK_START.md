# Admin System - Quick Reference

## Create Admin User

```bash
# Interactive mode
php artisan admin:create

# Non-interactive mode
php artisan admin:create \
  --email=admin@example.com \
  --password=SecurePassword123 \
  --first-name=Admin \
  --last-name=User \
  --organization="My Company" \
  --force
```

## What Gets Created

1. ✅ Admin user account (email verified)
2. ✅ Organization (or uses existing if slug matches)
3. ✅ ADMIN role with all permissions
4. ✅ User assigned to organization and role

## Authorization

Users with ADMIN role can perform **any action** - all authorization checks automatically pass.

```php
// In AppServiceProvider::boot()
Gate::before(function ($user, $ability) {
    return $user->roles()->where('name', 'admin')->exists() ? true : null;
});
```

## Testing

```bash
# Test admin creation
php artisan test --filter=CreateAdminUser

# Test authorization
php artisan test --filter=AdminGlobalAccess
```

## Security Notes

- ⚠️ Admin users bypass ALL policy checks
- ⚠️ Use strong passwords (min 8 chars, auto-gen is 16 chars)
- ⚠️ Only grant to trusted system administrators
- ✅ Passwords are automatically hashed
- ✅ Email is automatically verified
- ✅ All actions are testable

## Key Files

- Command: `app/Console/Commands/CreateAdminUser.php`
- Gate: `app/Providers/AppServiceProvider.php`
- Tests: `tests/Feature/Commands/CreateAdminUserTest.php`
- Tests: `tests/Feature/Authorization/AdminGlobalAccessTest.php`
- Docs: `ADMIN_SYSTEM.md`

## Common Issues

**Email already exists?**
```bash
php artisan tinker
>>> User::where('email', 'admin@example.com')->delete();
```

**Admin can't access resources?**
- Check: `$user->roles()->where('name', 'admin')->exists()`
- Verify: Gate is registered in AppServiceProvider
- Confirm: User is authenticated

## Integration with Seeding

```php
// In DatabaseSeeder.php
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
```

For complete documentation, see [ADMIN_SYSTEM.md](./ADMIN_SYSTEM.md)
