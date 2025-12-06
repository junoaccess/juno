<?php

namespace App\Observers;

use App\Jobs\OnboardOrganization;
use App\Models\Organization;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrganizationObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        // Get owner data if it was temporarily stored on the model
        $ownerData = $organization->ownerData ?? null;

        // Dispatch onboarding job after the database transaction commits
        OnboardOrganization::dispatch($organization, $ownerData);
    }
}
