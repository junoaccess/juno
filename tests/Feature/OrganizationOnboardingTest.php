<?php

use App\Enums\Role as RoleEnum;
use App\Jobs\OnboardOrganization;
use App\Mail\OrganizationOwnerInvitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Queue::fake();
    Mail::fake();
});

it('dispatches onboarding job when organization is created', function () {
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
        'phone' => '+1234567890',
    ]);

    Queue::assertPushed(OnboardOrganization::class, function ($job) use ($organization) {
        return $job->organization->id === $organization->id;
    });
});

it('creates roles from role-permission mapping for organization', function () {
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    // Manually run the job for testing
    $job = new OnboardOrganization($organization);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    // Check that roles were created
    $mapping = config('role-permission-mapping');
    $roleCount = count($mapping);

    expect($organization->roles()->count())->toBe($roleCount);

    // Verify specific roles exist
    assertDatabaseHas('roles', [
        'organization_id' => $organization->id,
        'slug' => RoleEnum::OWNER->value,
    ]);

    assertDatabaseHas('roles', [
        'organization_id' => $organization->id,
        'slug' => RoleEnum::ADMIN->value,
    ]);
});

it('creates permissions and assigns them to roles', function () {
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    // Get the owner role
    $ownerRole = $organization->roles()->where('slug', RoleEnum::OWNER->value)->first();

    expect($ownerRole)->not->toBeNull();
    expect($ownerRole->permissions()->count())->toBeGreaterThan(0);

    // Check specific permission exists
    $organizationsViewPermission = App\Models\Permission::where('name', 'organizations:view')->first();
    expect($organizationsViewPermission)->not->toBeNull();
});

it('creates owner user when provided with owner data', function () {
    $ownerData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@testorg.com',
        'phone' => '+1234567890',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization, $ownerData);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    assertDatabaseHas('users', [
        'email' => 'john@testorg.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

it('reuses existing user when email matches', function () {
    // Create an existing user
    $existingUser = User::factory()->create([
        'email' => 'existing@testorg.com',
        'first_name' => 'Existing',
    ]);

    $ownerData = [
        'first_name' => 'New Name',
        'email' => 'existing@testorg.com',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization, $ownerData);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    // Should still only have one user with this email
    expect(User::where('email', 'existing@testorg.com')->count())->toBe(1);

    // User should be attached to organization
    expect($organization->users()->where('users.id', $existingUser->id)->exists())->toBeTrue();
});

it('assigns owner role to the owner user', function () {
    $ownerData = [
        'first_name' => 'Owner',
        'last_name' => 'User',
        'email' => 'owner@testorg.com',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization, $ownerData);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    $owner = User::where('email', 'owner@testorg.com')->first();
    $ownerRole = $organization->roles()->where('slug', RoleEnum::OWNER->value)->first();

    expect($owner)->not->toBeNull();
    expect($ownerRole)->not->toBeNull();

    // Check that owner has the owner role for this organization
    expect(
        $owner->roles()
            ->where('roles.id', $ownerRole->id)
            ->wherePivot('organization_id', $organization->id)
            ->exists()
    )->toBeTrue();
});

it('queues onboarding email to owner', function () {
    $ownerData = [
        'first_name' => 'Owner',
        'email' => 'owner@testorg.com',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization, $ownerData);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    Mail::assertQueued(OrganizationOwnerInvitation::class, function ($mail) use ($organization) {
        return $mail->organization->id === $organization->id
            && $mail->hasTo('owner@testorg.com');
    });
});

it('is idempotent and does not duplicate when run multiple times', function () {
    $ownerData = [
        'first_name' => 'Owner',
        'email' => 'owner@testorg.com',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $service = app(App\Services\OrganizationOnboardingService::class);

    // Run onboarding twice
    $job1 = new OnboardOrganization($organization, $ownerData);
    $job1->handle($service);

    $rolesCountAfterFirst = $organization->fresh()->roles()->count();
    $usersCountAfterFirst = User::where('email', 'owner@testorg.com')->count();

    $job2 = new OnboardOrganization($organization, $ownerData);
    $job2->handle($service);

    $rolesCountAfterSecond = $organization->fresh()->roles()->count();
    $usersCountAfterSecond = User::where('email', 'owner@testorg.com')->count();

    // Counts should remain the same
    expect($rolesCountAfterSecond)->toBe($rolesCountAfterFirst);
    expect($usersCountAfterSecond)->toBe($usersCountAfterFirst);
    expect($usersCountAfterSecond)->toBe(1);
});

it('attaches owner to organization', function () {
    $ownerData = [
        'email' => 'owner@testorg.com',
    ];

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'email' => 'owner@testorg.com',
    ]);

    $job = new OnboardOrganization($organization, $ownerData);
    $job->handle(app(App\Services\OrganizationOnboardingService::class));

    $owner = User::where('email', 'owner@testorg.com')->first();

    expect($organization->users()->where('users.id', $owner->id)->exists())->toBeTrue();
});
