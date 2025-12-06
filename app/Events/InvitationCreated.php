<?php

namespace App\Events;

use App\Models\Invitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Invitation $invitation;

    public string $token;

    public function __construct(Invitation $invitation, string $token)
    {
        $this->invitation = $invitation;
        $this->token = $token;
    }
}
