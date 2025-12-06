<?php

namespace App\Events;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Invitation $invitation;

    public User $user;

    public function __construct(Invitation $invitation, User $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }
}
