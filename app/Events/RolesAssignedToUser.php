<?php

namespace App\Events;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RolesAssignedToUser
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;

    public Organization $organisation;

    /** @var array<int> */
    public array $roleIds;

    /**
     * @param  array<int>  $roleIds
     */
    public function __construct(User $user, Organization $organisation, array $roleIds)
    {
        $this->user = $user;
        $this->organisation = $organisation;
        $this->roleIds = $roleIds;
    }
}
