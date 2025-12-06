<?php

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\artisan;

it('creates an admin user with organization', function () {
    artisan('admin:create', [
        '--email' => 'admin@test.com',
        '--password' => 'password123',
        '--first-name' => 'Test',
        '--last-name' => 'Admin',
        '--organization' => 'Test Organization',
        '--force' => true,
    ])->assertSuccessful();

    // Assert user was created
    $user = User::where('email', 'admin@test.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->first_name)->toBe('Test')
        ->and($user->last_name)->toBe('Admin')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    // Assert organization was created
    $organization = Organization::where('slug', 'test-organization')->first();
    expect($organization)->not->toBeNull()
        ->and($organization->name)->toBe('Test Organization');

    // Assert user is attached to organization
    expect($user->organizations)->toHaveCount(1)
        ->and($user->organizations->first()->id)->toBe($organization->id)
        ->and($user->current_organization_id)->toBe($organization->id);

    // Assert user has ADMIN role
    expect($user->roles)->toHaveCount(1);
    $role = $user->roles->first();
    expect($role->name)->toBe(Role::ADMIN->value)
        ->and($role->organization_id)->toBe($organization->id);
});

it('uses existing organization if slug matches', function () {
    // Create an existing organization without triggering observer
    $existingOrg = Organization::withoutEvents(function () {
        return Organization::create([
            'name' => 'Existing Org',
            'slug' => 'existing-org',
            'email' => 'org@example.com',
            'owner_email' => 'owner@example.com',
            'owner_name' => 'Owner Name',
        ]);
    });

    artisan('admin:create', [
        '--email' => 'admin@existing.com',
        '--password' => 'password123',
        '--first-name' => 'Admin',
        '--last-name' => 'User',
        '--organization' => 'Existing Org',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'admin@existing.com')->first();
    expect($user)->not->toBeNull();

    // Assert user is attached to the existing organization
    expect($user->organizations->first()->id)->toBe($existingOrg->id);

    // Only one organization should exist with that slug
    expect(Organization::where('slug', 'existing-org')->count())->toBe(1);
});

it('validates email uniqueness', function () {
    // Create an existing user
    User::factory()->create(['email' => 'duplicate@test.com']);

    artisan('admin:create', [
        '--email' => 'duplicate@test.com',
        '--password' => 'password123',
        '--first-name' => 'Test',
        '--last-name' => 'User',
        '--organization' => 'Test Org',
        '--force' => true,
    ])->assertFailed();
});

it('accepts password as option', function () {
    artisan('admin:create', [
        '--email' => 'with-password@test.com',
        '--password' => 'CustomPass123!',
        '--first-name' => 'Test',
        '--last-name' => 'User',
        '--organization' => 'Test Org',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'with-password@test.com')->first();
    expect($user)->not->toBeNull()
        ->and(Hash::check('CustomPass123!', $user->password))->toBeTrue();
});

it('assigns all permissions to admin role when permissions exist', function () {
    // Create some test permissions
    Permission::factory()->count(5)->create();

    artisan('admin:create', [
        '--email' => 'admin-perms@test.com',
        '--password' => 'password123',
        '--first-name' => 'Test',
        '--last-name' => 'Admin',
        '--organization' => 'Test Organization',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'admin-perms@test.com')->first();
    $role = $user->roles->first();

    // Admin role should have all 5 permissions
    expect($role->permissions)->toHaveCount(5);
});

it('creates admin role without permissions when no permissions exist', function () {
    // Make sure no permissions exist
    Permission::query()->delete();

    artisan('admin:create', [
        '--email' => 'admin-no-perms@test.com',
        '--password' => 'password123',
        '--first-name' => 'Test',
        '--last-name' => 'Admin',
        '--organization' => 'Test Organization',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'admin-no-perms@test.com')->first();
    $role = $user->roles->first();

    // Admin role should exist but have no permissions
    expect($role)->not->toBeNull()
        ->and($role->name)->toBe(Role::ADMIN->value)
        ->and($role->permissions)->toBeEmpty();
});

it('sets user current organization to the created organization', function () {
    artisan('admin:create', [
        '--email' => 'current-org@test.com',
        '--password' => 'password123',
        '--first-name' => 'Test',
        '--last-name' => 'Admin',
        '--organization' => 'Current Org',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'current-org@test.com')->first();
    $organization = Organization::where('slug', 'current-org')->first();

    expect($user->current_organization_id)->toBe($organization->id);
});
