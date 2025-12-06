<?php

namespace App\Listeners;

use App\Events\InvitationCreated;
use App\Models\User;
use App\Notifications\OrganisationInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendInvitationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(InvitationCreated $event): void
    {
        // Find existing user or create anonymous notifiable
        $user = User::where('email', $event->invitation->email)->first();

        // Send notification (handles both mail and database channels)
        if ($user) {
            // Send to existing user (mail + database notification)
            $user->notify(new OrganisationInvitationNotification($event->invitation, $event->token));

            return;
        }

        // Send mail to non-user email address
        Notification::route('mail', $event->invitation->email)->notify(
            new OrganisationInvitationNotification($event->invitation, $event->token)
        );
    }
}
