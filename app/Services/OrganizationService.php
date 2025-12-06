<?php

namespace App\Services;

use App\DataTransferObjects\OwnerData;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    /**
     * Create a new organization with owner details.
     */
    public function create(array $data, OwnerData $ownerData): Organization
    {
        return DB::transaction(function () use ($data, $ownerData) {
            $organization = Organization::create([
                'name' => $data['name'],
                'email' => $ownerData->email,
                'phone' => $ownerData->phone,
                'website' => $data['website'] ?? null,
                'owner_name' => trim($ownerData->firstName.' '.$ownerData->lastName),
                'owner_email' => $ownerData->email,
                'owner_phone' => $ownerData->phone,
            ]);

            // Store owner data for the observer to access (redundant but kept for compatibility)
            $organization->ownerData = $ownerData->toArray();

            return $organization;
        });
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
     * Get paginated list of organizations.
     */
    public function paginate(int $perPage = 15)
    {
        return Organization::query()
            ->withCount(['users', 'teams'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Load common relationships for an organization.
     */
    public function loadRelationships(Organization $organization): Organization
    {
        return $organization->load(['users', 'teams', 'roles']);
    }
}
