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
        // Find existing user or create a notifiable recipient
        $user = User::where('email', $event->invitation->email)->first();

        if ($user) {
            // Send to existing user via their User model
            $user->notify(new OrganisationInvitationNotification($event->invitation, $event->token));
        } else {
            // Send anonymous notification via email address
            Notification::route('mail', $event->invitation->email)
                ->notify(new OrganisationInvitationNotification($event->invitation, $event->token));
        }
    }
}
