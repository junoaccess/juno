<?php

namespace App\Support;

use App\Models\Organization;

class CurrentOrganization
{
    public function __construct(
        private ?Organization $organization = null
    ) {}

    /**
     * Get the current organization model.
     */
    public function get(): ?Organization
    {
        return $this->organization;
    }

    /**
     * Set the current organization.
     */
    public function set(?Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * Get the organization ID.
     */
    public function id(): ?int
    {
        return $this->organization?->id;
    }

    /**
     * Get the organization slug.
     */
    public function slug(): ?string
    {
        return $this->organization?->slug;
    }

    /**
     * Get the organization name.
     */
    public function name(): ?string
    {
        return $this->organization?->name;
    }

    /**
     * Check if an organization is set.
     */
    public function exists(): bool
    {
        return $this->organization !== null;
    }

    /**
     * Check if the organization has a specific user.
     */
    public function hasUser(int $userId): bool
    {
        if (! $this->organization) {
            return false;
        }

        return $this->organization->users()->where('users.id', $userId)->exists();
    }
}
