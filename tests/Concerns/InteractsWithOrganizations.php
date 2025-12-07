<?php

namespace Tests\Concerns;

use App\Models\Organization;
use App\Models\User;

trait InteractsWithOrganizations
{
    /**
     * Set the HTTP host to the organization's subdomain.
     */
    protected function setOrganizationHost(Organization $organization): static
    {
        app()->instance('currentOrganizationId', $organization->id);

        return $this->withServerVariables([
            'HTTP_HOST' => "{$organization->slug}.".config('app.main_domain'),
        ]);
    }

    /**
     * Attach the user to the organization, set current organization, set host, and authenticate.
     */
    protected function actingAsUserInOrganization(User $user, Organization $organization, array $pivotAttributes = []): static
    {
        if (! $organization->users()->whereKey($user->id)->exists()) {
            $organization->users()->attach($user->id, $pivotAttributes);
        }

        $user->setCurrentOrganization($organization);

        $this->setOrganizationHost($organization);

        $this->actingAs($user);

        return $this;
    }
}
