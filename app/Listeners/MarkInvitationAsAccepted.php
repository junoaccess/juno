<?php

namespace App\Listeners;

use App\Events\InvitationAccepted;

class MarkInvitationAsAccepted
{
    /**
     * Handle the event.
     */
    public function handle(InvitationAccepted $event): void
    {
        $event->invitation->markAsAccepted();
    }
}
