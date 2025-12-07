<?php

use App\Events\InvitationCreated;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Event;

it('creates invitations with secure tokens and revokes existing pending invites', function () {
    Event::fake();

    $organization = Organization::factory()->create();
    $inviter = User::factory()->create();

    $existing = Invitation::create([
        'email' => 'person@example.com',
        'token_hash' => hash('sha256', 'old-token'),
        'status' => 'pending',
        'expires_at' => now()->addDay(),
        'organization_id' => $organization->id,
    ]);

    $service = app(InvitationService::class);

    $invitation = $service->createWithToken(
        organization: $organization,
        email: 'person@example.com',
        roles: ['owner'],
        invitedBy: $inviter,
        name: 'Person'
    );

    Event::assertDispatched(InvitationCreated::class, function (InvitationCreated $event) use ($invitation) {
        return $event->invitation->is($invitation)
            && hash('sha256', $event->token) === $invitation->token_hash;
    });

    $existing->refresh();
    expect($existing->status)->toBe('revoked');

    expect($invitation->status)->toBe('pending')
        ->and($invitation->roles)->toBe(['owner'])
        ->and($invitation->invited_by)->toBe($inviter->id);
});
