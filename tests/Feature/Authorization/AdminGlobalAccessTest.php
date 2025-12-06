<?php

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('allows admin users to perform any action', function () {
    $organization = Organization::withoutEvents(function () {
        return Organization::create([
            'name' => 'Test Organization',
            'slug' => 'test-org',
            'email' => 'org@example.com',
            'owner_email' => 'owner@example.com',
            'owner_name' => 'Owner Name',
        ]);
    });

    // Create admin role
    $adminRole = App\Models\Role::create([
        'name' => Role::ADMIN->value,
        'organization_id' => $organization->id,
        'description' => 'Admin role',
    ]);

    // Create admin user with ADMIN role
    $adminUser = User::factory()->create([
        'current_organization_id' => $organization->id,
    ]);
    $adminUser->organizations()->attach($organization->id, ['is_default' => true]);
    $adminUser->roles()->attach($adminRole->id, ['organization_id' => $organization->id]);

    // Test various abilities - admin should be able to do anything
    expect(Gate::forUser($adminUser)->allows('view', User::class))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('create', User::class))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('update', $adminUser))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('delete', $adminUser))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('viewAny', User::class))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('restore', $adminUser))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('forceDelete', $adminUser))->toBeTrue();
});

it('does not bypass authorization for non-admin users', function () {
    $organization = Organization::withoutEvents(function () {
        return Organization::create([
            'name' => 'Test Organization',
            'slug' => 'test-org-2',
            'email' => 'org2@example.com',
            'owner_email' => 'owner2@example.com',
            'owner_name' => 'Owner Name 2',
        ]);
    });

    // Create staff role (not admin)
    $staffRole = App\Models\Role::create([
        'name' => Role::STAFF->value,
        'organization_id' => $organization->id,
        'description' => 'Staff role',
    ]);

    // Create regular user with STAFF role
    $staffUser = User::factory()->create([
        'current_organization_id' => $organization->id,
    ]);
    $staffUser->organizations()->attach($organization->id, ['is_default' => true]);
    $staffUser->roles()->attach($staffRole->id, ['organization_id' => $organization->id]);

    // Staff user should not automatically pass all gates
    // Actual authorization will depend on policies
    expect(Gate::forUser($staffUser)->check('delete', $staffUser))->toBeFalse();
});

it('allows admin users even without explicit policies', function () {
    $organization = Organization::withoutEvents(function () {
        return Organization::create([
            'name' => 'Test Organization',
            'slug' => 'test-org-3',
            'email' => 'org3@example.com',
            'owner_email' => 'owner3@example.com',
            'owner_name' => 'Owner Name 3',
        ]);
    });

    // Create admin role
    $adminRole = App\Models\Role::create([
        'name' => Role::ADMIN->value,
        'organization_id' => $organization->id,
        'description' => 'Admin role',
    ]);

    // Create admin user
    $adminUser = User::factory()->create([
        'current_organization_id' => $organization->id,
    ]);
    $adminUser->organizations()->attach($organization->id, ['is_default' => true]);
    $adminUser->roles()->attach($adminRole->id, ['organization_id' => $organization->id]);

    // Test arbitrary ability names that may not have policies
    expect(Gate::forUser($adminUser)->allows('random-ability'))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('some-custom-permission'))->toBeTrue()
        ->and(Gate::forUser($adminUser)->allows('any-action-name'))->toBeTrue();
});
