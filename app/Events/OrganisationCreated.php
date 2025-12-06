<?php

namespace App\Events;

use App\Models\Organization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganisationCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Organization $organisation;

    public ?array $ownerData;

    public function __construct(Organization $organisation, ?array $ownerData = null)
    {
        $this->organisation = $organisation;
        $this->ownerData = $ownerData;
    }
}
