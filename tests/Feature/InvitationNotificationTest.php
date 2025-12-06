<?php

use App\Events\InvitationCreated;
use App\Listeners\SendInvitationEmail;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\OrganisationInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

beforeEach(function () {
    Notification::fake();
});

it('sends notification to existing users with mail and database channels', function () {
    $organization = Organization::factory()->create([
        'name' => 'Acme Inc',
        'slug' => 'acme-'.Str::random(6),
        'email' => 'contact@acme.test',
    ]);

    $invitee = User::factory()->create([
        'email' => 'invitee@acme.test',
    ]);

    $inviter = User::factory()->create();

    $token = Str::random(32);

    $invitation = Invitation::create([
        'email' => $invitee->email,
        'name' => 'Invitee Name',
        'token_hash' => hash('sha256', $token),
        'invited_by' => $inviter->id,
        'roles' => ['member'],
        'organization_id' => $organization->id,
        'expires_at' => now()->addDay(),
    ]);

    (new SendInvitationEmail)->handle(new InvitationCreated($invitation, $token));

    Notification::assertSentTo(
        $invitee,
        OrganisationInvitationNotification::class,
        function ($notification, array $channels) use ($invitation, $token) {
            return in_array('mail', $channels, true)
                && in_array('database', $channels, true)
                && $notification->invitation->is($invitation)
                && $notification->token === $token;
        }
    );
});

it('sends mail notification when the invitee is not a user', function () {
    $organization = Organization::factory()->create([
        'name' => 'Globex',
        'slug' => 'globex-'.Str::random(6),
        'email' => 'hello@globex.test',
    ]);

    $token = Str::random(32);

    $invitation = Invitation::create([
        'email' => 'newperson@globex.test',
        'name' => 'New Person',
        'token_hash' => hash('sha256', $token),
        'roles' => ['admin'],
        'organization_id' => $organization->id,
        'expires_at' => now()->addDays(2),
    ]);

    (new SendInvitationEmail)->handle(new InvitationCreated($invitation, $token));

    Notification::assertSentOnDemand(
        OrganisationInvitationNotification::class,
        function ($notification, array $channels, object $notifiable) use ($invitation, $token) {
            return $notifiable->routes['mail'] === $invitation->email
                && $notification->invitation->is($invitation)
                && $notification->token === $token;
        }
    );
});
