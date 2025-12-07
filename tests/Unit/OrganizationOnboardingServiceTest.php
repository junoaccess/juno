<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationOwnerNotification;
use App\Services\OrganizationOnboardingService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('onboards an organization with roles and owner assignment', function () {
    Notification::fake();

    app(PermissionSeeder::class)->run();

    $organization = Organization::factory()->create([
        'name' => 'Acme',
        'slug' => 'acme',
        'owner_name' => 'Ada Lovelace',
        'owner_email' => 'ada@example.com',
        'owner_phone' => '1234567890',
    ]);

    $service = app(OrganizationOnboardingService::class);

    $service->onboard($organization);
    $service->onboard($organization); // idempotent

    $rolesCount = Role::where('organization_id', $organization->id)->count();
    expect($rolesCount)->toBe(count(config('role-permission-mapping')));

    $owner = User::where('email', 'ada@example.com')->first();
    expect($owner)->not()->toBeNull()
        ->and($owner->current_organization_id)->toBe($organization->id)
        ->and($organization->users()->wherePivot('is_default', true)->where('users.id', $owner->id)->exists())->toBeTrue();

    Notification::assertSentTo($owner, OrganizationOwnerNotification::class);
});
