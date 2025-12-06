<?php

namespace App\Events;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserJoinedOrganisation
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;

    public Organization $organisation;

    public function __construct(User $user, Organization $organisation)
    {
        $this->user = $user;
        $this->organisation = $organisation;
    }
}
