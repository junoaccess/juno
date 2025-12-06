<?php

namespace App\Services;

use App\Actions\CreateOrganisationAction;
use App\DataTransferObjects\OwnerData;
use App\Filters\OrganizationFilter;
use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrganizationService
{
    public function __construct(
        protected CreateOrganisationAction $createOrganisationAction,
    ) {}

    /**
     * Create a new organization with owner details.
     */
    public function create(array $data, OwnerData $ownerData): Organization
    {
        return $this->createOrganisationAction->execute($data, $ownerData);
    }

    /**
     * Update an existing organization.
     */
    public function update(Organization $organization, array $data): Organization
    {
        $organization->update(array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
        ]));

        return $organization->fresh();
    }

    /**
     * Delete an organization.
     */
    public function delete(Organization $organization): bool
    {
        return $organization->delete();
    }

    /**
     * Restore a soft-deleted organization.
     */
    public function restore(Organization $organization): bool
    {
        return $organization->restore();
    }

    /**
     * Get paginated list of organizations with optional filtering.
     */
    public function paginate(int $perPage = 15, ?OrganizationFilter $filter = null): LengthAwarePaginator
    {
        $query = Organization::query()
            ->withCount(['users', 'teams'])
            ->with(['users:id,first_name,last_name,email']);

        if ($filter) {
            $query = $query->filter($filter);
        } else {
            $query = $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Load common relationships for an organization.
     */
    public function loadRelationships(Organization $organization): Organization
    {
        return $organization->load([
            'users:id,first_name,last_name,email',
            'teams:id,name,organization_id',
            'roles:id,name,slug,organization_id',
        ]);
    }

    /**
     * Switch user's current organization with validation.
     */
    public function switchUserOrganization($user, Organization $organization): void
    {
        // Verify user belongs to this organization
        if (! $user->belongsToOrganization($organization)) {
            abort(403, 'You do not have access to this organization.');
        }

        // Update user's current organization
        $user->update([
            'current_organization_id' => $organization->id,
        ]);
    }

    /**
     * Get organization subdomain URL.
     */
    public function getOrganizationUrl(Organization $organization, string $path = '/dashboard'): string
    {
        return "https://{$organization->slug}.".config('app.main_domain').$path;
    }
}
