<?php

use App\Events\InvitationCreated;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

it('shows the registration form for a valid invitation token', function () {
    Event::fake([InvitationCreated::class]);

    $organization = Organization::factory()->create(['slug' => 'acme']);
    $inviter = User::factory()->create();

    app(InvitationService::class)->createWithToken(
        organization: $organization,
        email: 'guest@example.com',
        roles: [],
        invitedBy: $inviter
    );

    /** @var \App\Events\InvitationCreated $event */
    $event = Event::dispatched(InvitationCreated::class)->first()[0];
    $token = $event->token;
    $invitation = $event->invitation->fresh();

    expect($invitation->token_hash)->toBe(hash('sha256', $token));

    expect(app(InvitationService::class)->findByToken($token))->not()->toBeNull();

    $url = route('invitations.accept', [
        'organizationSlug' => $organization->slug,
        'token' => $token,
    ]);
    expect(app(InvitationService::class)->findByToken($token))->not()->toBeNull();

    $response = $this->get($url, [
        'HTTP_HOST' => "{$organization->slug}.".config('app.main_domain'),
    ]);

    $response->assertOk()->assertInertia(function (Assert $page) use ($token) {
        $page->component('auth/register-with-invitation')
            ->where('token', $token)
            ->where('hasAccount', false)
            ->where('invitation.email', 'guest@example.com');
    });

    expect(Invitation::withoutGlobalScopes()->count())->toBe(1);
});

it('registers a new user from an invitation and marks it accepted', function () {
    Event::fake([InvitationCreated::class]);

    $organization = Organization::factory()->create(['slug' => 'acme']);
    $role = Role::where('organization_id', $organization->id)
        ->where('slug', 'manager')
        ->firstOrFail();
    $inviter = User::factory()->create();

    app(InvitationService::class)->createWithToken(
        organization: $organization,
        email: 'guest@example.com',
        roles: [$role->slug],
        invitedBy: $inviter
    );

    /** @var \App\Events\InvitationCreated $event */
    $event = Event::dispatched(InvitationCreated::class)->first()[0];
    $token = $event->token;
    $invitation = $event->invitation->fresh();

    expect($invitation->token_hash)->toBe(hash('sha256', $token));

    expect(app(InvitationService::class)->findByToken($token))->not()->toBeNull();

    $url = route('invitations.accept.store', [
        'organizationSlug' => $organization->slug,
        'token' => $token,
    ]);
    expect(app(InvitationService::class)->findByToken($token))->not()->toBeNull();

    $response = $this->post($url, [
        'first_name' => 'Guest',
        'last_name' => 'User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], [
        'HTTP_HOST' => "{$organization->slug}.".config('app.main_domain'),
    ]);

    $response->assertRedirect('https://acme.'.config('app.main_domain').'/dashboard');

    $user = User::where('email', 'guest@example.com')->first();
    expect($user)->not()->toBeNull()
        ->and($user->current_organization_id)->toBe($organization->id)
        ->and($organization->users()->whereKey($user->id)->exists())->toBeTrue();

    $invitation->refresh();
    expect($invitation->status)->toBe('accepted');
    expect($invitation->accepted_at)->not()->toBeNull();

    expect($user->roles()->where('roles.id', $role->id)->exists())->toBeTrue();
});

it('returns 404 for an invalid invitation token', function () {
    $organization = Organization::factory()->create(['slug' => 'acme']);

    $this->get('/invitations/accept/invalid-token', [
        'HTTP_HOST' => "{$organization->slug}.".config('app.main_domain'),
    ])->assertNotFound();
});
