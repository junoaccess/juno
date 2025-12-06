<?php

namespace App\Listeners;

use App\Events\OrganisationCreated;
use App\Jobs\OnboardOrganization;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnboardNewOrganisation implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrganisationCreated $event): void
    {
        OnboardOrganization::dispatch($event->organisation, $event->ownerData);
    }
}
