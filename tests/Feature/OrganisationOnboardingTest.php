<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationOwnerNotification;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

it('shows the organisation onboarding page to guests', function () {
    $response = $this->get('http://'.config('app.main_domain').'/onboarding/organisation');

    $response->assertOk()->assertInertia(function (Assert $page) {
        $page->component('onboarding/new-organisation')
            ->where('mainDomain', config('app.main_domain'));
    });
});

it('creates an organisation with owner and seeds default roles', function () {
    Notification::fake();

    app(PermissionSeeder::class)->run();

    $payload = [
        'organisation_name' => 'Acme Inc',
        'organisation_slug' => 'acme',
        'organisation_email' => 'contact@acme.test',
        'owner_first_name' => 'Ada',
        'owner_last_name' => 'Lovelace',
        'owner_email' => 'ada@acme.test',
        'owner_phone' => '1234567890',
        'password' => 'super-secure-pass',
        'password_confirmation' => 'super-secure-pass',
    ];

    $response = $this->post('http://'.config('app.main_domain').'/onboarding/organisation', $payload);

    $response->assertRedirect('https://acme.'.config('app.main_domain').'/dashboard');

    $organization = Organization::where('slug', 'acme')->first();
    expect($organization)->not()->toBeNull();

    $owner = User::where('email', 'ada@acme.test')->first();
    expect($owner)->not()->toBeNull()
        ->and($owner->current_organization_id)->toBe($organization->id)
        ->and($organization->users()->where('users.id', $owner->id)->exists())->toBeTrue();

    $rolesCount = Role::where('organization_id', $organization->id)->count();
    expect($rolesCount)->toBe(count(config('role-permission-mapping')));

    $ownerRole = Role::where('organization_id', $organization->id)->where('slug', 'owner')->first();
    expect($ownerRole)->not()->toBeNull();
    expect($owner->roles()->where('roles.id', $ownerRole->id)->exists())->toBeTrue();

    Notification::assertSentTo($owner, OrganizationOwnerNotification::class);
});
