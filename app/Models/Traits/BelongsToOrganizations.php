<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganizations
{
    /**
     * Scope a query to only include users belonging to a specific organization.
     */
    public function scopeForOrganization(Builder $query, int|string $organizationId): Builder
    {
        return $query->whereHas('organizations', function ($q) use ($organizationId) {
            $q->where('organizations.id', $organizationId);
        });
    }

    /**
     * Scope to exclude users who don't belong to any organization.
     */
    public function scopeWithOrganizations(Builder $query): Builder
    {
        return $query->has('organizations');
    }

    /**
     * Scope to include only users without any organization membership.
     */
    public function scopeWithoutOrganizations(Builder $query): Builder
    {
        return $query->doesntHave('organizations');
    }

    /**
     * Scope to get users with their roles for a specific organization.
     */
    public function scopeWithOrganizationRoles(Builder $query, int|string $organizationId): Builder
    {
        return $query->with([
            'roles' => fn ($q) => $q->where('organization_id', $organizationId),
        ]);
    }
}
